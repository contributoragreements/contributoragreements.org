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
 * Class EfgFormLookupRadio
 *
 * Form field "radio (DB)".
 * based on FormRadio by Leo Feyer
 * @copyright  Thomas Kuhn 2007-2014
 * @author     Thomas Kuhn <mail@th-kuhn.de>
 * @package    Efg
 */
class EfgFormLookupRadio extends \Widget
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'form_widget';

	/**
	 * Options
	 * @var array
	 */
	protected $arrOptions = array();


	/**
	 * Add specific attributes
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			case 'efgLookupOptions':
				$this->import('Formdata');
				$this->arrConfiguration['efgLookupOptions'] = $varValue;
				$arrOptions = $this->Formdata->prepareWidgetOptions($this->arrConfiguration);
				$this->arrOptions = $arrOptions;
				break;

			case 'mandatory':
				$this->arrConfiguration['mandatory'] = $varValue ? true : false;
				break;

			case 'rgxp':
				break;

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}


	/**
	 * Return a parameter
	 * @return string
	 * @throws Exception
	 */
	public function __get($strKey)
	{
		switch ($strKey)
		{
			case 'options':
				return $this->arrOptions;
				break;

			default:
				return parent::__get($strKey);
				break;
		}
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$strOptions = '';

		foreach ($this->arrOptions as $i => $arrOption)
		{
			$strOptions .= sprintf('<span><input type="radio" name="%s" id="opt_%s" class="radio" value="%s"%s%s <label for="opt_%s">%s</label></span>',
				$this->strName,
				$this->strId.'_'.$i,
				$arrOption['value'],
				((is_array($this->varValue) && in_array($arrOption['value'] , $this->varValue) || $this->varValue == $arrOption['value']) ? ' checked="checked"' : ''),
				$this->strTagEnding,
				$this->strId.'_'.$i,
				$arrOption['label']);
		}

		return sprintf('<div id="ctrl_%s" class="radio_container%s">%s</div>',
			$this->strId,
			(strlen($this->strClass) ? ' ' . $this->strClass : ''),
			$strOptions) . $this->addSubmit();
	}

}
