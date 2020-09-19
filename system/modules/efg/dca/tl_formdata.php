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
 * Table tl_formdata
 */
$GLOBALS['TL_DCA']['tl_formdata'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Formdata',
		'ctable'                      => array('tl_formdata_details'),
		'closed'                      => true,
		'notEditable'                 => false,
		'enableVersioning'            => false,
		'doNotCopyRecords'            => true,
		'doNotDeleteRecords'          => true,
		'switchToEdit'                => false,
		'onload_callback'             => array
		(
			array('tl_formdata', 'loadDCA'),
			array('tl_formdata', 'checkPermission')
		),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'form' => 'index'
			)
		)
	),
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 2,
			'fields'                  => array('date DESC'),
			'flag'                    => 8,
			'panelLayout'             => 'sort,filter;search,limit',

		),
		'label' => array
		(
			'fields'                  => array('form', 'date', 'ip', 'alias'),
			'label_callback'          => array('tl_formdata','getRowLabel')
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_formdata']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_formdata']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_formdata']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
			'mail' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_formdata']['mail'],
				'href'                => 'act=mail',
				'icon'                => 'system/modules/efg/assets/mail.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => 'form,ip,date,alias;published,confirmationSent,confirmationDate;be_notes;fd_member,fd_user,fd_member_group,fd_user_group'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'sorting' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'form' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_formdata']['form'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'search'                  => true,
			'filter'                  => true,
			'sorting'                 => true,
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_formdata']['date'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'filter'                  => true,
			'flag'                    => 8,
			'eval'                    => array('rgxp' => 'datim', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'ip' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_formdata']['ip'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'filter'                  => false,
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'fd_member' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_formdata']['fd_member'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'eval'                    => array('mandatory' => false, 'includeBlankOption' => true, 'tl_class'=>'w50'),
			'options_callback'        => array('tl_formdata', 'getMembersSelect'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'fd_user' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_formdata']['fd_user'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'eval'                    => array('mandatory' => false, 'includeBlankOption' => true, 'tl_class'=>'w50'),
			'options_callback'        => array('tl_formdata', 'getUsersSelect'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'fd_member_group' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_formdata']['fd_member_group'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'eval'                    => array('mandatory' => false, 'includeBlankOption' => true, 'tl_class'=>'w50'),
			'options_callback'        => array('tl_formdata', 'getMemberGroupsSelect'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'fd_user_group' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_formdata']['fd_user_group'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'eval'                    => array('mandatory' => false, 'includeBlankOption' => true, 'tl_class'=>'w50'),
			'options_callback'        => array('tl_formdata', 'getUserGroupsSelect'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_formdata']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class' => 'clr'),
			// 'default'                 => '1',
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_formdata']['alias'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'alnum', 'unique'=>true, 'spaceToUnderscore'=>true, 'maxlength'=>64),
			'save_callback' => array
			(
				array('tl_formdata', 'generateAlias')
			),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'confirmationSent' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_formdata']['confirmationSent'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50', 'doNotCopy'=>true, 'isBoolean'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'confirmationDate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_formdata']['confirmationDate'],
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => 8,
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'datim'),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
		'be_notes' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_formdata']['be_notes'],
			'inputType'               => 'textarea',
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => false,
			'filter'                  => false,
			'eval'                    => array('rows' => 5),
			'class'                   => 'fd_notes',
			'sql'                     => "text NULL"
		),
	)
);



/**
 * Class tl_formdata
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Thomas Kuhn 2007-2014
 * @author     Thomas Kuhn <mail@th-kuhn.de>
 * @package    efg
 */
class tl_formdata extends \Backend
{

	/**
	 * Database result
	 * @var array
	 */
	protected $arrData = null;


	/**
	 * Loads $GLOBALS['TL_DCA']['tl_formdata'] or overwrites with form specific DCA config
	 * @param object $dca DataContainer
	 * @param string $varFormKey specific form if called from ModuleFormdataListing
	 */
	function loadDCA(\DataContainer $dca, $varFormKey = '')
	{
		$strModule = 'efg';
		$strName = 'feedback';
		$strFileName = 'tl_formdata';

		// TODO: remove this fix if xdependentcalendarfield is available for Contao 3 and config is fixed
		// Fix config for xdependentcalendarfields
		// xdependentcalendarfields/config/config.php registers 'FormTextField', which is a front end widget)
		if ($GLOBALS['BE_FFL']['xdependentcalendarfields'] == 'FormTextField')
		{
			$GLOBALS['BE_FFL']['xdependentcalendarfields'] = 'TextField';
		}

		// Register backend widget for 'cm_alernativeforms'
		$GLOBALS['BE_FFL']['cm_alternative'] = 'SelectMenu';

		if ($varFormKey != '' && is_string($varFormKey))
		{
			$strFileName = $varFormKey;
		}
		else
		{
			$strFileName = (\Input::get('do') == 'feedback' ? 'fd_feedback' : \Input::get('do'));
		}

		if ($varFormKey != '' && is_string($varFormKey))
		{
			if ($varFormKey != 'tl_formdata' )
			{
				if (array_key_exists($varFormKey, $GLOBALS['BE_MOD']['formdata']))
				{
					$strFile = sprintf('%s/system/modules/%s/dca/%s.php', TL_ROOT, $strModule, $strFileName);

					if (file_exists($strFile))
					{
						$strName = $varFormKey;
						include_once $strFile;

						// Replace standard dca tl_formdata by form-dependent dca
						if (is_array($GLOBALS['TL_DCA'][$strName]) && count($GLOBALS['TL_DCA'][$strName]) > 0)
						{
							$GLOBALS['TL_DCA']['tl_formdata'] = $GLOBALS['TL_DCA'][$strName];
							unset($GLOBALS['TL_DCA'][$strName]);
						}

						// HOOK: allow to load custom settings
						if (isset($GLOBALS['TL_HOOKS']['loadDataContainer']) && is_array($GLOBALS['TL_HOOKS']['loadDataContainer']))
						{
							foreach ($GLOBALS['TL_HOOKS']['loadDataContainer'] as $callback)
							{
								$this->import($callback[0]);
								$this->$callback[0]->$callback[1]('tl_formdata');
							}
						}


					}
				}
			}
		}
		else
		{
			if (array_key_exists(\Input::get('do'), $GLOBALS['BE_MOD']['formdata']))
			{
				$strFile = sprintf('%s/system/modules/%s/dca/%s.php', TL_ROOT, $strModule, $strFileName);

				if (file_exists($strFile))
				{
					$strName = \Input::get('do');
					include_once $strFile;

					// Replace standard dca tl_formdata by form-dependent dca
					if (is_array($GLOBALS['TL_DCA'][$strName]) && count($GLOBALS['TL_DCA'][$strName]) > 0)
					{
						$GLOBALS['TL_DCA']['tl_formdata'] = $GLOBALS['TL_DCA'][$strName];
						unset($GLOBALS['TL_DCA'][$strName]);
					}

					// HOOK: allow to load custom settings
					if (isset($GLOBALS['TL_HOOKS']['loadDataContainer']) && is_array($GLOBALS['TL_HOOKS']['loadDataContainer']))
					{
						foreach ($GLOBALS['TL_HOOKS']['loadDataContainer'] as $callback)
						{
							$this->import($callback[0]);
							$this->$callback[0]->$callback[1]('tl_formdata');
						}
					}

				}
			}
		}

		@include(TL_ROOT . '/system/config/dcaconfig.php');

	}


	/**
	 * Check permissions to edit table tl_formdata
	 */
	public function checkPermission()
	{
		$this->import('BackendUser', 'User');

		$arrFields = array_keys($GLOBALS['TL_DCA']['tl_formdata']['fields']);
		// check/set restrictions
		foreach ($arrFields as $strField)
		{
			if ($GLOBALS['TL_DCA']['tl_formdata']['fields'][$strField]['exclude'] == true)
			{
				if ($this->User->isAdmin || $this->User->hasAccess('tl_formdata::'.$strField, 'alexf') == true)
				{
					$GLOBALS['TL_DCA']['tl_formdata']['fields'][$strField]['exclude'] = false;
				}
			}
		}
	}


	/**
	 * Autogenerate an alias if it has not been set yet
	 * alias is created from formdata content related to first form field of type text not using rgxp=email/date/datim/time
	 * @param mixed
	 * @param object
	 * @return string
	 */
	public function generateAlias($varValue, DataContainer $dc)
	{
		$strFormTitle = '';
		if (strlen($dc->strFormFilterValue))
		{
			$strFormTitle = $dc->strFormFilterValue;
		}
		$this->import('Formdata');
		return $this->Formdata->generateAlias($varValue, $strFormTitle, $dc->id);
	}


	/*
	* Create List Label for formdata item
	*/
	public function getRowLabel($arrRow)
	{
		$strRet = '';

		// Titles of all forms
		if (is_null($this->arrData))
		{
			$strSql = "SELECT id,title FROM tl_form";
			$objForms = \Database::getInstance()->prepare($strSql)->execute();

			while ($objForms->next())
			{
				$this->arrData[$objForms->id]['title'] = $objForms->title;
			}
		}


		$strRet .= '<div class="fd_wrap"><div class="fd_head"><div class="cte_type unpublished">';
		$strRet .= '<strong>' . date($GLOBALS['TL_CONFIG']['datimFormat'], $arrRow['date']) . '</strong>';
		$strRet .= '<span style="color:#b3b3b3; padding-left:3px;">[' . $this->arrData[$arrRow['pid']]['title'] . ']</span>';
		$strRet .= '<span style="border:solid 1px blue">#' . $this->arrData[$arrRow['alias']] . '#</span>';
		$strRet .= '</div><p class="fd_notes">' . $arrRow['be_notes'] . '</p></div>';

		$strRet .= '<div class="mark_links">';
		// Details from table tl_formdata_details
		$strSql = "SELECT ff_name,value FROM tl_formdata_details WHERE pid=? ORDER BY sorting ASC";
		$objDetails = \Database::getInstance()->prepare($strSql)->execute($arrRow['id']);

		while ($objDetails->next())
		{
			$strRet .=  '<div class="fd_row"><div class="fd_label">' .$objDetails->ff_name . ':&nbsp;</div><div class="fd_value">' . $objDetails->value . '&nbsp;</div></div>';
		}

		$strRet .= '</div></div>';

		return $strRet;

	}


	/**
	 * Return all forms as array for dropdown
	 * @return array
	 */
	public function getFormsSelect()
	{
		$forms = array();

		// Get all forms
		$objForms = \Database::getInstance()->prepare("SELECT id,title,formID FROM tl_form WHERE storeFormdata=? ORDER BY title ASC")
			->execute("1");
		$forms[] = '-';
		if ($objForms->numRows)
		{
			while ($objForms->next())
			{
				$k = $objForms->title;
				$v = $objForms->title;
				$forms[$k] = $v;
			}
		}
		return $forms;
	}


	/**
	 * Return all members as array for dropdown
	 * @return array
	 */
	public function getMembersSelect()
	{
		$items = array();

		// Get all members
		$objItems = \Database::getInstance()->prepare("SELECT id, CONCAT(firstname,' ',lastname) AS fullname FROM tl_member ORDER BY fullname ASC")
			->execute("1");
		//$items[0] = '-';
		if ($objItems->numRows)
		{
			while ($objItems->next())
			{
				$k = $objItems->id;
				$v = $objItems->fullname;
				$items[$k] = $v;
			}
		}
		return $items;
	}


	/**
	 * Return all users as array for dropdown
	 * @return array
	 */
	public function getUsersSelect()
	{
		$items = array();

		// Get all users
		$objItems = \Database::getInstance()->prepare("SELECT id, name FROM tl_user ORDER BY name ASC")
			->execute("1");
		//$items[0] = '-';
		if ($objItems->numRows)
		{
			while ($objItems->next())
			{
				$k = $objItems->id;
				$v = $objItems->name;
				$items[$k] = $v;
			}
		}
		return $items;
	}

	/**
	 * Return all member groups as array for dropdown
	 * @return array
	 */
	public function getMemberGroupsSelect()
	{
		$items = array();

		// Get all member groups
		$objItems = \Database::getInstance()->prepare("SELECT id,`name` FROM tl_member_group ORDER BY `name` ASC")
			->execute("1");
		//$items[0] = '-';
		if ($objItems->numRows)
		{
			while ($objItems->next())
			{
				$k = $objItems->id;
				$v = $objItems->name;
				$items[$k] = $v;
			}
		}
		return $items;
	}

	/**
	 * Return all user groups as array for dropdown
	 * @return array
	 */
	public function getUserGroupsSelect()
	{
		$items = array();

		// Get all user groups
		$objItems = \Database::getInstance()->prepare("SELECT id,`name` FROM tl_user_group ORDER BY `name` ASC")
			->execute("1");
		//$items[0] = '-';
		if ($objItems->numRows)
		{
			while ($objItems->next())
			{
				$k = $objItems->id;
				$v = $objItems->name;
				$items[$k] = $v;
			}
		}
		return $items;
	}

}
