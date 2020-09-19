<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   Efg
 * @author    Thomas Kuhn <mail@th-kuhn.de>
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 * @copyright Thomas Kuhn 2007-2014
 */


/**
 * Namespace
 */
namespace Efg;

/**
 * Class FormdataProcessor
 *
 * @copyright  Thomas Kuhn 2007-2014
 * @author     Thomas Kuhn <mail@th-kuhn.de>
 * @package    Efg
 */
class FormdataProcessor extends \Frontend
{

	protected $strFdDcaKey = '';

	protected $strFormdataDetailsKey = 'details';


	/**
	 * Process submitted form data
	 * Send mail, store data in backend
	 * @param array $arrSubmitted Submitted data
	 * @param array|bool $arrForm Form configuration
	 * @param array|bool $arrFiles Files uploaded
	 * @param array|bool $arrLabels Form field labels
	 * @return void
	 */
	public function processSubmittedData($arrSubmitted, $arrForm=false, $arrFiles=false, $arrLabels=false) {

		// Form config
		if (!$arrForm)
		{
			return;
		}

		$arrFormFields = array();

		$this->import('FrontendUser', 'Member');
		$this->import('Formdata');

		$this->strFdDcaKey = 'fd_' . (!empty($arrForm['alias']) ? $arrForm['alias'] : str_replace('-', '_', standardize($arrForm['title'])) );
		$this->Formdata->FdDcaKey = $this->strFdDcaKey;

		// Get params of related listing formdata
		$intListingId = intval($_SESSION['EFP']['LISTING_MOD']['id']);
		if ($intListingId > 0)
		{
			$objListing = \Database::getInstance()->prepare("SELECT * FROM tl_module WHERE id=?")
				->execute($intListingId);
			if ($objListing->numRows)
			{
				$arrListing = $objListing->fetchAssoc();

				// Mail delivery defined in frontend listing module
				$arrForm['sendConfirmationMailOnFrontendEditing'] = ($arrListing['efg_fe_no_confirmation_mail']) ? false : true;
				$arrForm['sendFormattedMailOnFrontendEditing'] = ($arrListing['efg_fe_no_formatted_mail']) ? false : true;
			}
		}

		if (!empty($arrListing['efg_DetailsKey']))
		{
			$this->strFormdataDetailsKey = $arrListing['efg_DetailsKey'];
		}

		$blnFEedit = false;
		$intOldId = 0;
		$strRedirectTo = '';

		$strUrl = preg_replace('/\?.*$/', '', \Environment::get('request'));
		$strUrlParams = '';
		$blnQuery = false;
		foreach (preg_split('/&(amp;)?/', $_SERVER['QUERY_STRING']) as $fragment)
		{
			if (strlen($fragment))
			{
				if (strncasecmp($fragment, $this->strFormdataDetailsKey, strlen($this->strFormdataDetailsKey)) !== 0 && strncasecmp($fragment, 'act', 3) !== 0)
				{
					$strUrlParams .= (!$blnQuery ? '' : '&amp;') . $fragment;
					$blnQuery = true;
				}
			}
		}

		if (in_array($arrListing['efg_fe_edit_access'], array('public','groupmembers','member')))
		{
			if (\Input::get('act') == 'edit')
			{
				$blnFEedit = true;

				$objCheck = \Database::getInstance()->prepare("SELECT id FROM tl_formdata WHERE id=? OR alias=?")
					->execute(\Input::get($this->strFormdataDetailsKey), \Input::get($this->strFormdataDetailsKey));

				if ($objCheck->numRows == 1)
				{
					$intOldId = intval($objCheck->id);
				}
				else
				{
					$this->log('Could not identify record by ID "' . \Input::get($this->strFormdataDetailsKey) . '"', __METHOD__, TL_GENERAL);
				}
			}
		}

		// Types of form fields with storable data
		$arrFFstorable = $this->Formdata->arrFFstorable;

		if (($arrForm['storeFormdata'] || $arrForm['sendConfirmationMail'] || $arrForm['sendFormattedMail']) && !empty($arrSubmitted))
		{
			$timeNow = time();

			$this->loadDataContainer($this->strFdDcaKey);
			$this->loadDataContainer('tl_formdata_details');
			$this->loadDataContainer('tl_files');

			$arrFormFields = $this->Formdata->getFormfieldsAsArray($arrForm['id']);

			$arrBaseFields = array();
			$arrDetailFields = array();
			if (!empty($GLOBALS['TL_DCA']['tl_formdata']['tl_formdata']['baseFields']))
			{
				$arrBaseFields = $GLOBALS['TL_DCA']['tl_formdata']['tl_formdata']['baseFields'];
			}
			if (!empty($GLOBALS['TL_DCA']['tl_formdata']['tl_formdata']['detailFields']))
			{
				$arrDetailFields = $GLOBALS['TL_DCA']['tl_formdata']['tl_formdata']['detailFields'];
			}
			$arrHookFields = array_merge($arrBaseFields, $arrDetailFields);

			$arrToSave = array();
			foreach($arrSubmitted as $k => $varVal)
			{
				if (in_array($k, array('id')))
				{
					continue;
				}
				elseif (in_array($k, $arrHookFields) || in_array($k, array_keys($arrFormFields)) || in_array($k, array('FORM_SUBMIT','MAX_FILE_SIZE')))
				{
					$arrToSave[$k] = $varVal;
				}
			}

			// HOOK: process efg form data callback
			if (array_key_exists('processEfgFormData', $GLOBALS['TL_HOOKS']) && is_array($GLOBALS['TL_HOOKS']['processEfgFormData']))
			{
				foreach ($GLOBALS['TL_HOOKS']['processEfgFormData'] as $key => $callback)
				{
					$this->import($callback[0]);
					$arrResult = $this->$callback[0]->$callback[1]($arrToSave, $arrFiles, $intOldId, $arrForm, $arrLabels);
					if (!empty($arrResult))
					{
						$arrSubmitted = $arrResult;
						$arrToSave = $arrSubmitted;
					}
				}
			}

		}

		// Formdata storage
		if ($arrForm['storeFormdata'] && !empty($arrSubmitted))
		{
			$blnStoreOptionsValue = ($arrForm['efgStoreValues']) ? true : false;

			// Get old record on frontend editing
			if ($intOldId > 0)
			{
				$arrOldData = $this->Formdata->getFormdataAsArray($intOldId);
				$arrOldFormdata = $arrOldData['fd_base'];
				$arrOldFormdataDetails = $arrOldData['fd_details'];
			}

			// Prepare record tl_formdata
			$arrSet = array
			(
				'form' => $arrForm['title'],
				'tstamp' => $timeNow,
				'date' => $timeNow,
				'ip' => \System::anonymizeIp(\Environment::get('ip')),
				'published' => ($GLOBALS['TL_DCA']['tl_formdata']['fields']['published']['default'] ? '1' : '' ),
				'fd_member' => intval($this->Member->id),
				'fd_member_group' => intval($this->Member->groups[0]),
				'fd_user' => intval($this->User->id),
				'fd_user_group' => intval($this->User->groups[0])
			);

			// Keep some values from existing record on frontend editing
			if ($intOldId > 0)
			{
				$arrSet['form'] = $arrOldFormdata['form'];
				$arrSet['be_notes'] = $arrOldFormdata['be_notes'];
				$arrSet['fd_member'] = $arrOldFormdata['fd_member'];
				$arrSet['fd_member_group'] = $arrOldFormdata['fd_member_group'];
				if (intval($this->Member->id) > 0)
				{
					$arrSet['fd_member'] = intval($this->Member->id);
					if (count($this->Member->groups) == 1 && intval($this->Member->groups[0]) > 0)
					{
						$arrSet['fd_member_group'] = intval($this->Member->groups[0]);
					}
				}
				else
				{
					$arrSet['fd_member'] = 0;
				}
				$arrSet['fd_user'] = $arrOldFormdata['fd_user'];
				$arrSet['fd_user_group'] = $arrOldFormdata['fd_user_group'];

				// Set published to value of old record, if no default value is defined
				if (!isset($GLOBALS['TL_DCA']['tl_formdata']['fields']['published']['default']))
				{
					$arrSet['published'] = $arrOldFormdata['published'];
				}
			}

			// Store formdata: Update or insert and delete
			if ($blnFEedit && strlen($arrListing['efg_fe_keep_id']))
			{
				$intNewId = $intOldId;
				\Database::getInstance()->prepare("UPDATE tl_formdata %s WHERE id=?")->set($arrSet)->execute($intOldId);
				\Database::getInstance()->prepare("DELETE FROM tl_formdata_details WHERE pid=?")->execute($intOldId);
			}
			else
			{
				$objNewFormdata = \Database::getInstance()->prepare("INSERT INTO tl_formdata %s")->set($arrSet)->execute();
				$intNewId = $objNewFormdata->insertId;

				// Update related comments
				if (in_array('comments', \ModuleLoader::getActive()))
				{
					\Database::getInstance()->prepare("UPDATE tl_comments %s WHERE `source` = 'tl_formdata' AND parent=?")
						->set(array('parent' => $intNewId))
						->execute($intOldId);
				}
			}

			// Store details data
			foreach ($arrFormFields as $k => $arrField)
			{

				$strType = $arrField['formfieldType'];
				$strVal = '';

				if (in_array($strType, $arrFFstorable))
				{

					if ($blnStoreOptionsValue)
					{
						$arrField['eval']['efgStoreValues'] = true;
					}
					else
					{
						$arrField['eval']['efgStoreValues'] = false;
					}

					// Set rgxp 'date' for field type 'calendar' if not set
					if ($strType == 'calendar')
					{
						if (!isset($arrField['rgxp']))
						{
							$arrField['rgxp'] = 'date';
						}
					}
					// Set rgxp 'date' and dateFormat for field type 'xdependentcalendarfields'
					elseif ($strType == 'xdependentcalendarfields')
					{
						$arrField['rgxp'] = 'date';
						$arrField['dateFormat'] = $arrField['xdateformat'];
					}

					$strVal = $this->Formdata->preparePostValueForDatabase($arrSubmitted[$k], $arrField, $arrFiles[$k]);

					// Special treatment for type upload
					// Keep old file on frontend editing, if no new file has been uploaded
					if ($strType == 'upload')
					{
						if ($intOldId)
						{
							if (!$arrFiles[$k]['name'])
							{
								if (strlen($arrOldFormdataDetails[$k]['value']))
								{
									$strVal = $arrOldFormdataDetails[$k]['value'];
								}
							}
						}
					}

					if (isset($arrSubmitted[$k]) || ($strType == 'upload' && strlen($strVal)))
					{
						// Prepare data
						$arrFieldSet = array(
							'pid' => $intNewId,
							'sorting' => $arrField['sorting'],
							'tstamp' => $timeNow,
							'ff_id' => $arrField['id'],
							'ff_name' => $arrField['name'],
							'value' => $strVal
						);

						$objNewFormdataDetails = \Database::getInstance()
							->prepare("INSERT INTO tl_formdata_details %s")
							->set($arrFieldSet)
							->execute();

					}

				}
			}

			// Delete old record after frontend editing
			if ($blnFEedit)
			{
				if (!isset($arrListing['efg_fe_keep_id']) || $arrListing['efg_fe_keep_id'] != "1")
				{
					if ($intNewId > 0 && intval($intOldId)>0 && intval($intNewId) != intval($intOldId))
					{
						\Database::getInstance()->prepare("DELETE FROM tl_formdata_details WHERE pid=?")
							->execute($intOldId);
						\Database::getInstance()->prepare("DELETE FROM tl_formdata WHERE id=?")
							->execute($intOldId);
					}
				}
				$strRedirectTo = preg_replace('/\?.*$/', '', \Environment::get('request'));
			}

			// Auto-generate alias
			$strAlias = $this->Formdata->generateAlias($arrOldFormdata['alias'], $arrForm['title'], $intNewId);
			if (strlen($strAlias))
			{
				$arrUpd = array('alias' => $strAlias);
				\Database::getInstance()->prepare("UPDATE tl_formdata %s WHERE id=?")
					->set($arrUpd)
					->execute($intNewId);
			}
		}

		// Store data in the session to display on confirmation page
		unset($_SESSION['EFP']['FORMDATA']);
		$blnSkipEmptyFields = ($arrForm['confirmationMailSkipEmpty']) ? true : false;

		foreach ($arrFormFields as $k => $arrField)
		{
			$strType = $arrField['formfieldType'];
			$strVal = '';
			if (in_array($strType, $arrFFstorable))
			{
				$strVal = $this->Formdata->preparePostValueForMail($arrSubmitted[$k], $arrField, $arrFiles[$k], $blnSkipEmptyFields);
			}

			$_SESSION['EFP']['FORMDATA'][$k] = $strVal;
		}
		$_SESSION['EFP']['FORMDATA']['_formId_'] = $arrForm['id'];

		// Confirmation Mail
		if ($blnFEedit && !$arrForm['sendConfirmationMailOnFrontendEditing'])
		{
			$arrForm['sendConfirmationMail'] = false;
		}

		if ($arrForm['sendConfirmationMail'])
		{
			$objMailProperties = new \stdClass();
			$objMailProperties->subject = '';
			$objMailProperties->sender = '';
			$objMailProperties->senderName = '';
			$objMailProperties->replyTo = '';
			$objMailProperties->recipients = array();
			$objMailProperties->messageText = '';
			$objMailProperties->messageHtmlTmpl = '';
			$objMailProperties->messageHtml = '';
			$objMailProperties->attachments = array();
			$objMailProperties->skipEmptyFields = false;

			$objMailProperties->skipEmptyFields = ($arrForm['confirmationMailSkipEmpty']) ? true : false;

			// Set the sender as given in form configuration
			list($senderName, $sender) = \String::splitFriendlyEmail($arrForm['confirmationMailSender']);
			$objMailProperties->sender = $sender;
			$objMailProperties->senderName = $senderName;

			// Set the 'reply to' address, if given in form configuration
			if (!empty($arrForm['confirmationMailReplyto']))
			{
				list($replyToName, $replyTo) = \String::splitFriendlyEmail($arrForm['confirmationMailReplyto']);
				$objMailProperties->replyTo = (strlen($replyToName) ? $replyToName . ' <' . $replyTo . '>' : $replyTo);
			}

			// Set recipient(s)
			$recipientFieldName = $arrForm['confirmationMailRecipientField'];
			$varRecipient = $arrSubmitted[$recipientFieldName];

			if (is_array($varRecipient))
			{
				$arrRecipient = $varRecipient;
			}
			else
			{
				$arrRecipient = trimsplit(',', $varRecipient);
			}

			if (!empty($arrForm['confirmationMailRecipient']))
			{
				$varRecipient = $arrForm['confirmationMailRecipient'];
				$arrRecipient = array_merge($arrRecipient, trimsplit(',', $varRecipient));
			}
			$arrRecipient = array_filter(array_unique($arrRecipient));

			if (!empty($arrRecipient))
			{
				foreach ($arrRecipient as $kR => $recipient)
				{
					list($recipientName, $recipient) = \String::splitFriendlyEmail($this->replaceInsertTags($recipient, false));
					$arrRecipient[$kR] = (strlen($recipientName) ? $recipientName . ' <' . $recipient . '>' : $recipient);
				}
			}
			$objMailProperties->recipients = $arrRecipient;

			// Check if we want custom attachments... (Thanks to Torben Schwellnus)
			if ($arrForm['addConfirmationMailAttachments'])
			{
				if($arrForm['confirmationMailAttachments'])
				{
					$arrCustomAttachments = deserialize($arrForm['confirmationMailAttachments'], true);

					if (!empty($arrCustomAttachments))
					{
						foreach ($arrCustomAttachments as $varFile)
						{
							$objFileModel = \FilesModel::findById($varFile);

							if ($objFileModel !== null)
							{
								$objFile = new \File($objFileModel->path);
								if ($objFile->size)
								{
									$objMailProperties->attachments[TL_ROOT .'/' . $objFile->path] = array
									(
										'file' => TL_ROOT . '/' . $objFile->path,
										'name' => $objFile->basename,
										'mime' => $objFile->mime);
								}
							}
						}
					}
				}
			}

			$objMailProperties->subject = \String::decodeEntities($arrForm['confirmationMailSubject']);
			$objMailProperties->messageText = \String::decodeEntities($arrForm['confirmationMailText']);
			$objMailProperties->messageHtmlTmpl = $arrForm['confirmationMailTemplate'];

			// Replace Insert tags and conditional tags
			$objMailProperties = $this->Formdata->prepareMailData($objMailProperties, $arrSubmitted, $arrFiles, $arrForm, $arrFormFields);

			// Send Mail
			$blnConfirmationSent = false;

			if (!empty($objMailProperties->recipients))
			{
				$objMail = new \Email();
				$objMail->from = $objMailProperties->sender;

				if (!empty($objMailProperties->senderName))
				{
					$objMail->fromName = $objMailProperties->senderName;
				}

				if (!empty($objMailProperties->replyTo))
				{
					$objMail->replyTo($objMailProperties->replyTo);
				}

				$objMail->subject = $objMailProperties->subject;

				if (!empty($objMailProperties->attachments))
				{
					foreach ($objMailProperties->attachments as $strFile => $varParams)
					{
						$strContent = file_get_contents($varParams['file'], false);
						$objMail->attachFileFromString($strContent, $varParams['name'], $varParams['mime']);
					}
				}

				if (!empty($objMailProperties->messageText))
				{
					$objMail->text = $objMailProperties->messageText;
				}

				if (!empty($objMailProperties->messageHtml))
				{
					$objMail->html = $objMailProperties->messageHtml;
				}

				foreach ($objMailProperties->recipients as $recipient)
				{
					$objMail->sendTo($recipient);
					$blnConfirmationSent = true;
				}
			}

			if ($blnConfirmationSent && isset($intNewId) && intval($intNewId) > 0)
			{
				$arrUpd = array('confirmationSent' => '1', 'confirmationDate' => $timeNow);
				$res = \Database::getInstance()->prepare("UPDATE tl_formdata %s WHERE id=?")
					->set($arrUpd)
					->execute($intNewId);
			}

		}

		// Information (formatted) Mail
		if ($blnFEedit && !$arrForm['sendFormattedMailOnFrontendEditing'])
		{
			$arrForm['sendFormattedMail'] = false;
		}

		if ($arrForm['sendFormattedMail'])
		{
			$objMailProperties = new \stdClass();
			$objMailProperties->subject = '';
			$objMailProperties->sender = '';
			$objMailProperties->senderName = '';
			$objMailProperties->replyTo = '';
			$objMailProperties->recipients = array();
			$objMailProperties->messageText = '';
			$objMailProperties->messageHtmlTmpl = '';
			$objMailProperties->messageHtml = '';
			$objMailProperties->attachments = array();
			$objMailProperties->skipEmptyFields = false;

			$objMailProperties->skipEmptyFields = ($arrForm['formattedMailSkipEmpty']) ? true : false;

			// Set the admin e-mail as "from" address
			$objMailProperties->sender = $GLOBALS['TL_ADMIN_EMAIL'];
			$objMailProperties->senderName = $GLOBALS['TL_ADMIN_NAME'];

			// Get 'reply to' address, if form contains field named 'email'
			if (isset($arrSubmitted['email']) && !empty($arrSubmitted['email']) && !is_bool(strpos($arrSubmitted['email'], '@')))
			{
				$replyTo = $arrSubmitted['email'];
				// add name
				if (isset($arrSubmitted['name']) && !empty($arrSubmitted['name']))
				{
					$replyTo = '"'. $arrSubmitted['name'] .'" <' . $arrSubmitted['email'] . '>';
				}
				$objMailProperties->replyTo = $replyTo;
			}

			// Set recipient(s)
			$varRecipient = $arrForm['formattedMailRecipient'];
			if (is_array($varRecipient))
			{
				$arrRecipient = $varRecipient;
			}
			else
			{
				$arrRecipient = trimsplit(',', $varRecipient);
			}
			$arrRecipient = array_filter(array_unique($arrRecipient));

			if (!empty($arrRecipient))
			{
				foreach ($arrRecipient as $kR => $recipient)
				{
					list($recipientName, $recipient) = \String::splitFriendlyEmail($this->replaceInsertTags($recipient, false));
					$arrRecipient[$kR] = (strlen($recipientName) ? $recipientName . ' <' . $recipient . '>' : $recipient);
				}
			}
			$objMailProperties->recipients = $arrRecipient;

			// Check if we want custom attachments... (Thanks to Torben Schwellnus)
			if ($arrForm['addFormattedMailAttachments'])
			{
				if ($arrForm['formattedMailAttachments'])
				{
					$arrCustomAttachments = deserialize($arrForm['formattedMailAttachments'], true);

					if (is_array($arrCustomAttachments))
					{
						foreach ($arrCustomAttachments as $varFile)
						{
							$objFileModel = \FilesModel::findById($varFile);

							if ($objFileModel !== null)
							{
								$objFile = new \File($objFileModel->path);
								if ($objFile->size)
								{
									$objMailProperties->attachments[TL_ROOT . '/' . $objFile->path] = array
									(
										'file' => TL_ROOT . '/' . $objFile->path,
										'name' => $objFile->basename,
										'mime' => $objFile->mime
									);
								}
							}
						}
					}
				}
			}

			$objMailProperties->subject = \String::decodeEntities($arrForm['formattedMailSubject']);
			$objMailProperties->messageText = \String::decodeEntities($arrForm['formattedMailText']);
			$objMailProperties->messageHtmlTmpl = $arrForm['formattedMailTemplate'];

			// Replace Insert tags and conditional tags
			$objMailProperties = $this->Formdata->prepareMailData($objMailProperties, $arrSubmitted, $arrFiles, $arrForm, $arrFormFields);

			// Send Mail
			$blnInformationSent = false;

			if (!empty($objMailProperties->recipients))
			{

				$objMail = new \Email();
				$objMail->from = $objMailProperties->sender;
				if (!empty($objMailProperties->senderName))
				{
					$objMail->fromName = $objMailProperties->senderName;
				}

				if (!empty($objMailProperties->replyTo))
				{
					$objMail->replyTo($objMailProperties->replyTo);
				}

				$objMail->subject = $objMailProperties->subject;

				if (!empty($objMailProperties->attachments))
				{
					foreach ($objMailProperties->attachments as $strFile => $varParams)
					{
						$strContent = file_get_contents($varParams['file'], false);
						$objMail->attachFileFromString($strContent, $varParams['name'], $varParams['mime']);
					}
				}

				if (!empty($objMailProperties->messageText))
				{
					$objMail->text = $objMailProperties->messageText;
				}

				if (!empty($objMailProperties->messageHtml))
				{
					$objMail->html = $objMailProperties->messageHtml;
				}

				foreach ($objMailProperties->recipients as $recipient)
				{
					$objMail->sendTo($recipient);
					$blnInformationSent = true;
				}
			}

		}

		// Redirect after frontend editing
		if ($blnFEedit)
		{
			if (!empty($strRedirectTo))
			{
				$strRed = preg_replace(array('/\/' . $this->strFormdataDetailsKey . '\/' . \Input::get($this->strFormdataDetailsKey) . '/i', '/' . $this->strFormdataDetailsKey . '=' . \Input::get($this->strFormdataDetailsKey) . '/i', '/act=edit/i'), array('','',''), $strUrl) . (!empty($strUrlParams) ? '?'.$strUrlParams : '');
				\Controller::redirect($strRed);
			}
		}

	}

	/*
	 * Callback function to display submitted data on confirmation page
	 */
	public function processConfirmationContent($strContent)
	{
		$arrSubmitted = $_SESSION['EFP']['FORMDATA'];

		// fix: after submission of normal single page form array $_SESSION['EFP']['FORMDATA'] is empty
		if (null === $arrSubmitted || (count($arrSubmitted) == 1 && array_keys($arrSubmitted) === array('_formId_')))
		{
			$arrSubmitted = $_SESSION['FORM_DATA'];
			$arrSubmitted['_formId_'] = $_SESSION['EFP']['FORMDATA'];
		}

		$blnProcess = false;
		if (preg_match('/\{\{form::/si', $strContent))
		{
			$blnProcess = true;
		}

		if (!empty($arrSubmitted) && isset($arrSubmitted['_formId_']) && $blnProcess)
		{
			$blnSkipEmptyFields = false;

			$objSkip = \Database::getInstance()->prepare("SELECT confirmationMailSkipEmpty FROM tl_form WHERE id=?")->execute($arrSubmitted['_formId_']);
			if ($objSkip->confirmationMailSkipEmpty == 1)
			{
				$blnSkipEmptyFields = true;
			}

			$this->import('Formdata');

			$arrFormFields = $this->Formdata->getFormfieldsAsArray(intval($arrSubmitted['_formId_']));

			preg_match('/<body[^>]*?>.*?<\/body>/si', $strContent, $arrMatch);

			if (!empty($arrMatch))
			{

				for ($m = 0; $m < count($arrMatch); $m++)
				{
					$strTemp = $arrMatch[$m];
					$strTemp = preg_replace(array('/\{\{/', '/\}\}/'), array('__BRCL__', '__BRCR__'), $strTemp);
					$blnEval = $this->Formdata->replaceConditionTags($strTemp);

					// Replace tags
					$tags = array();
					preg_match_all('/__BRCL__.*?__BRCR__/si', $strTemp, $tags);

					// Replace tags of type {{form::<form field name>}}
					// .. {{form::fieldname?label=Label for this field: }}
					foreach ($tags[0] as $tag)
					{
						$elements = explode('::', preg_replace(array('/^__BRCL__/i', '/__BRCR__$/i'), array('',''), $tag));
						switch (strtolower($elements[0]))
						{
							// Form
							case 'form':
								$strKey = $elements[1];
								$arrKey = explode('?', $strKey);
								$strKey = $arrKey[0];

								$arrTagParams = null;
								if (isset($arrKey[1]) && strlen($arrKey[1]))
								{
									$arrTagParams = $this->Formdata->parseInsertTagParams($tag);
								}

								$arrField = $arrFormFields[$strKey];

								$strLabel = '';
								$strVal = '';
								if ($arrTagParams && strlen($arrTagParams['label']))
								{
									$strLabel = $arrTagParams['label'];
								}

								$strVal = $arrSubmitted[$strKey];
								if (is_array($strVal))
								{
									$strVal = implode(', ', $strVal);
								}

								if (strlen($strVal))
								{
									$strVal = nl2br($strVal);
								}

								if ($arrTagParams['attachment'])
								{
									$strVal = '';
								}

								if (!strlen($strVal) && $blnSkipEmptyFields)
								{
									$strLabel = '';
								}

								$strTemp = str_replace($tag, $strLabel . $strVal, $strTemp);
								break;
						}
					}

					$strTemp = preg_replace(array('/__BRCL__/', '/__BRCR__/'), array('{{', '}}'), $strTemp);

					// Eval the code
					if ($blnEval)
					{
						$strTemp = $this->Formdata->evalConditionTags($strTemp);
					}

					$strContent = str_replace($arrMatch[$m], $strTemp, $strContent);

				}
			}

		}

		return $strContent;

	}

}
