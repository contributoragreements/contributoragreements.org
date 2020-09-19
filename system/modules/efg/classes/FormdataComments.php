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
 * Class FormdataComments
 *
 * @copyright  Thomas Kuhn 2007-2014
 * @author     Thomas Kuhn <mail@th-kuhn.de>
 * @package    Efg
 */
class FormdataComments
{

	/**
	 * List a particular record
	 * @param array
	 * @return string
	 */
	public function listComments($arrRow)
	{

		$strRet = '';

		$objParent = \Database::getInstance()->prepare("SELECT `id`, `form`, `alias`  FROM tl_formdata WHERE id=?")
			->execute($arrRow['parent']);

		if ($objParent->numRows)
		{
			$strRet .= ' (' . $objParent->form;

			if (strlen($objParent->alias))
			{
				$strRet .= ' - '.$objParent->alias;
			}
			$strRet .= ')';
		}

		return $strRet;
	}

}
