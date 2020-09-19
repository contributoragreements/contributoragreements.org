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
 * Class FormdataBackend
 *
 * @copyright  Thomas Kuhn 2007-2014
 * @author     Thomas Kuhn <mail@th-kuhn.de>
 * @package    Efg
 */
class FormdataBackend extends \Backend
{

	/**
	 * Data container object
	 * @var object
	 */
	protected $objDc;

	/**
	 * Current record
	 * @var array
	 */
	protected $arrData = array();

	// Types of form fields with storable data
	protected $arrFFstorable = array();

	// Mapping of frontend form fields to backend widgets
	protected $arrMapTL_FFL = array();

	public function __construct()
	{
		parent::__construct();

		$this->loadDataContainer('tl_form_field');
		$this->import('Formdata');

		// Types of form fields with storable data
		$this->arrFFstorable = $this->Formdata->arrFFstorable;

		// Mapping of frontend form fields to backend widgets
		$this->arrMapTL_FFL = $this->Formdata->arrMapTL_FFL;
	}

	public function generate()
	{
		if (\Input::get('do') && \Input::get('do') != 'feedback')
		{
			if ($this->Formdata->arrStoringForms[\Input::get('do')])
			{
				$session = $this->Session->getData();
				$session['filter']['tl_feedback']['form'] = $this->Formdata->arrStoringForms[\Input::get('do')]['title'];

				$this->Session->setData($session);
			}
		}

		if (\Input::get('act') == '')
		{
			return $this->objDc->showAll();
		}
		else
		{
			$act = \Input::get('act');
			return $this->objDc->$act();
		}
	}

	/**
	 * Create DCA files
	 */
	public function createFormdataDca(\DataContainer $dc)
	{
		$this->intFormId = $dc->id;
		$arrForm = \Database::getInstance()->prepare("SELECT * FROM tl_form WHERE id=?")
			->execute($this->intFormId)
			->fetchAssoc();

		$strFormKey = (!empty($arrForm['alias'])) ? $arrForm['alias'] : str_replace('-', '_', standardize($arrForm['title']));
		$this->updateConfig(array($strFormKey => $arrForm));
	}

	/**
	 * Callback edit button
	 * @return string
	 */
	public function callbackEditButton($row, $href, $label, $title, $icon, $attributes, $strTable, $arrRootIds, $arrChildRecordIds, $blnCircularReference, $strPrevious, $strNext)
	{
		$return = '';

		$strDcaKey = array_search($row['form'], $this->Formdata->arrFormsDcaKey);
		if ($strDcaKey)
		{
			$return .= '<a href="' . \Backend::addToUrl($href . '&amp;do=fd_' . $strDcaKey . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes.'>' . \Image::getHtml($icon, $label) . '</a> ';
		}

		return $return;
	}

	/**
	 * Update efg/config/config.php, dca and language files
	 */
	public function updateConfig($arrForms = null)
	{
		$arrStoringForms = $this->Formdata->arrStoringForms;

		if ($arrForms == null)
		{
			$arrForms = $arrStoringForms;
		}

		// Remove unused dca files
		$arrFiles = scan(TL_ROOT . '/system/modules/efg/dca', true);

		foreach ($arrFiles as $strFile)
		{
			if (substr($strFile, 0, 3) == 'fd_')
			{
				if (empty($arrStoringForms) || !in_array(str_replace('.php', '', substr($strFile, 3)) , array_keys($arrStoringForms)))
				{
					$objFile = new \File('system/modules/efg/dca/' . $strFile);
					$objFile->delete();
				}
			}
		}

		// Remove cached dca files
		if (is_dir(TL_ROOT . '/system/cache/dca'))
		{
			$arrFiles = scan(TL_ROOT . '/system/cache/dca', true);

			foreach ($arrFiles as $strFile)
			{
				if (substr($strFile, 0, 3) == 'fd_' || $strFile == 'tl_formdata.php' || $strFile == 'tl_formdata_details.php')
				{
					$objFile = new \File('system/cache/dca/' . $strFile);
					$objFile->delete();
				}
			}
		}

		// config/config.php
		$tplConfig = $this->newTemplate('efg_internal_config');
		$tplConfig->arrStoringForms = $arrStoringForms;

		$objConfig = new \File('system/modules/efg/config/config.php');
		$objConfig->write($tplConfig->parse());
		$objConfig->close();

		if (empty($arrStoringForms))
		{
			return;
		}

		// languages/modules.php
		$arrModLangs = scan(TL_ROOT . '/system/modules/efg/languages');
		$arrLanguages = $this->getLanguages();

		foreach ($arrModLangs as $strModLang)
		{

			// Remove cached language files
			if (is_file(TL_ROOT . '/system/cache/language/' . $strModLang .'/modules.php'))
			{
				$objFile = new \File('system/cache/language/' . $strModLang . '/modules.php');
				$objFile->delete();
			}
			if (is_file(TL_ROOT . '/system/cache/language/' . $strModLang .'/tl_formdata.php'))
			{
				$objFile = new \File('system/cache/language/' . $strModLang . '/tl_formdata.php');
				$objFile->delete();
			}

			// Create language files
			if (array_key_exists($strModLang, $arrLanguages))
			{
				$strFile = sprintf('%s/system/modules/%s/languages/%s/%s.php', TL_ROOT, 'efg', $strModLang, 'tl_efg_modules');
				if (file_exists($strFile))
				{
					include($strFile);
				}

				$tplMod = $this->newTemplate('efg_internal_modules');
				$tplMod->arrStoringForms = $arrStoringForms;

				$objMod = new \File('system/modules/efg/languages/'.$strModLang.'/modules.php');
				$objMod->write($tplMod->parse());
				$objMod->close();
			}
		}

		// dca/fd_FORMKEY.php
		if (is_array($arrForms) && !empty($arrForms))
		{
			foreach ($arrForms as $arrForm)
			{
				if (!empty($arrForm))
				{
					$arrForm = \Database::getInstance()->prepare("SELECT * FROM tl_form WHERE id=?")
						->execute($arrForm['id'])
						->fetchAssoc();

					$arrFields = array();
					$arrFieldNamesById = array();

					$arrSelectors = array();
					$arrPalettes = array();
					$strCurrentPalette = '';
					$strPreviousPalette = '';

					// Get all form fields of this form
					$arrFormFields = $this->Formdata->getFormFieldsAsArray($arrForm['id']);

					if (!empty($arrFormFields))
					{
						foreach ($arrFormFields as $strFieldKey => $arrField)
						{

							// Ignore not storable fields and some special fields like checkbox CC, fields of type password ...
							if (!in_array($arrField['type'], $this->arrFFstorable)
								|| ($arrField['type'] == 'checkbox' && $strFieldKey == 'cc'))
							{
								continue;
							}

							// Set current palette name (for 'conditionalforms' and 'cm_alternativeforms')
							if (($arrField['formfieldType'] == 'condition' && $arrField['conditionType'] == 'start')
								|| ($arrField['formfieldType'] == 'cm_alternative' && $arrField['cm_alternativeType'] == 'cm_start')
								|| ($arrField['formfieldType'] == 'cm_alternative' && $arrField['cm_alternativeType'] == 'cm_else'))
							{
								$arrSelectors[] = $arrField['name'];

								if ($arrField['formfieldType'] == 'cm_alternative' && $arrField['cm_alternativeType'] == 'cm_start')
								{
									if ($strCurrentPalette != '')
									{
										$strPreviousPalette = $strCurrentPalette;
									}
									$strCurrentPalette = $arrField['name'].'_0';

									$arrField['options'] = array(array('value' => '', 'label' => '-'), array('value' => '0', 'label' => $arrField['cm_alternativelabel']), array('value' => '1', 'label' => $arrField['cm_alternativelabelelse']));
									$arrField['value'] = $arrField['cm_alternativelabel'];

									// Add field to palette if we are inside a palette
									if ($strPreviousPalette != '')
									{
										$arrPalettes[$strPreviousPalette][] = $arrField['name'];
									}
								}
								elseif ($arrField['formfieldType'] == 'cm_alternative' && $arrField['cm_alternativeType'] == 'cm_else')
								{
									if ($strCurrentPalette != '')
									{
										if ($strCurrentPalette != $arrField['name'].'_0')
										{
											$strPreviousPalette = $strCurrentPalette;
										}
									}
									$strCurrentPalette = $arrField['name'].'_1';
								}
								else
								{
									if ($strCurrentPalette != '')
									{
										$strPreviousPalette = $strCurrentPalette;
									}
									$strCurrentPalette = $arrField['name'];

									// Add field to palette if we are inside a palette
									if ($strPreviousPalette != '')
									{
										$arrPalettes[$strPreviousPalette][] = $arrField['name'];
									}
								}
							}

							// Ignore conditionalforms conditionType 'stop' and cm_alternativeforms cm_alternativeType 'cm_stop', reset palette name
							if (($arrField['formfieldType'] == 'condition' && $arrField['conditionType'] == 'stop')
								|| ($arrField['formfieldType'] == 'cm_alternative' && $arrField['cm_alternativeType'] == 'cm_stop'))
							{

								if ($strPreviousPalette != '')
								{
									$strCurrentPalette = $strPreviousPalette;
									$strPreviousPalette = '';
								}
								else
								{
									$strCurrentPalette = '';
								}

								continue;
							}

							if (!in_array($strFieldKey, array_keys($arrFields))
								&& !($arrField['formfieldType'] == 'cm_alternative' && $arrField['cm_alternativeType'] == 'cm_else'))
							{
								$arrFields[$strFieldKey] = $arrField;
								$arrFieldNamesById[$arrField['id']] = $strFieldKey;
							}

							// Add field to palette
							if ($strCurrentPalette != '')
							{
								if (!($arrField['formfieldType'] == 'condition' && $arrField['conditionType'] == 'start')
									&& !($arrField['formfieldType'] == 'cm_alternative' && in_array($arrField['cm_alternativeType'], array('cm_start', 'cm_else', 'cm_stop'))))
								{
									$arrPalettes[$strCurrentPalette][] = $arrField['name'];
								}
							}

						}
					}

					if (!empty($arrSelectors))
					{
						$arrSelectors = array_unique($arrSelectors);
					}

					$strFormKey = (!empty($arrForm['alias'])) ? $arrForm['alias'] : str_replace('-', '_', standardize($arrForm['title']));

					$tplDca = $this->newTemplate('efg_internal_dca_formdata');
					$tplDca->strFormKey = $strFormKey;
					$tplDca->arrForm = $arrForm;
					$tplDca->arrStoringForms = $arrStoringForms;
					$tplDca->arrFields = $arrFields;
					$tplDca->arrFieldNamesById = $arrFieldNamesById;
					$tplDca->arrSelectors = $arrSelectors;
					$tplDca->arrPalettes = $arrPalettes;

					// Enable backend confirmation mail
					$blnBackendMail = false;
					if ($arrForm['sendConfirmationMail'] || strlen($arrForm['confirmationMailText']))
					{
						$blnBackendMail = true;
					}
					$tplDca->blnBackendMail = $blnBackendMail;

					$objDca = new \File('system/modules/efg/dca/fd_' . $strFormKey . '.php');
					$objDca->write($tplDca->parse());
					$objDca->close();
				}

			}
		}

		// overall dca/fd_feedback.php
		// Get all form fields of all storing forms
		if (!empty($arrStoringForms))
		{
			$arrAllFields = array();
			$arrFieldNamesById = array();

			foreach ($arrStoringForms as $strFormKey => $arrForm)
			{
				// Get all form fields of this form
				$arrFormFields = $this->Formdata->getFormFieldsAsArray($arrForm['id']);

				if (!empty($arrFormFields))
				{
					foreach ($arrFormFields as $strFieldKey => $arrField)
					{
						// Ignore not storable fields and some special fields like checkbox CC, fields of type password ...
						if (!in_array($arrField['formfieldType'], $this->arrFFstorable)
							|| ($arrField['formfieldType'] == 'checkbox' && $strFieldKey == 'cc')
							|| ($arrField['formfieldType'] == 'condition' && $arrField['conditionType'] == 'stop')
							|| ($arrField['formfieldType'] == 'cm_alternative' && in_array($arrField['cm_alternativeType'], array('cm_else', 'cm_stop'))))
						{
							continue;
						}

						$arrAllFields[$strFieldKey] = $arrField;
						$arrFieldNamesById[$arrField['id']] = $strFieldKey;
					}
				}

			}

			$strFormKey = 'feedback';

			$tplDca = $this->newTemplate('efg_internal_dca_formdata');
			$tplDca->arrForm = array('key' => 'feedback', 'title'=> $this->arrForm['title']);
			$tplDca->arrStoringForms = $arrStoringForms;
			$tplDca->arrFields = $arrAllFields;
			$tplDca->arrFieldNamesById = $arrFieldNamesById;

			$objDca = new \File('system/modules/efg/dca/fd_' . $strFormKey . '.php');
			$objDca->write($tplDca->parse());
			$objDca->close();

		}

		// Rebuild internal cache
		if (!$GLOBALS['TL_CONFIG']['bypassCache'])
		{
			$this->import('Automator');

			$this->Automator->generateConfigCache();
			$this->Automator->generateDcaCache();
			$this->Automator->generateDcaExtracts();
		}

	}

	/**
	 * Return a new template object
	 * @param string
	 * @return object
	 */
	private function newTemplate($strTemplate)
	{
		$objTemplate = new \BackendTemplate($strTemplate);
		$objTemplate->folder = 'efg';

		return $objTemplate;
	}


	/**
	 * Import Form data from CSV file
	 * @param object Datacontainer
	 * @return string CSV imort form
	 */
	public function importCsv($dc)
	{
		if (\Input::get('key') != 'import')
		{
			return '';
		}

		return $dc->importFile();
	}

}
