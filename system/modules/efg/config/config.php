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

// This file is created when saving a form in form generator
// last created on 2017-10-10 13:24:50


/**
* to fix height of style class w50 in backend
*/
if (TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'system/modules/efg/assets/w50_fix.css';
}


/**
 * Use class ExtendedForm
 */
$GLOBALS['FE_MOD']['application']['form'] = 'ExtendedForm';
$GLOBALS['TL_CTE']['includes']['form'] = 'ExtendedForm';


/**
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 */



/**
 * -------------------------------------------------------------------------
 * FRONT END MODULES
 * -------------------------------------------------------------------------
 */

array_insert($GLOBALS['FE_MOD']['application'], count($GLOBALS['FE_MOD']['application']), array
(
	'formdatalisting' => 'ModuleFormdataListing'
));


/**
 * -------------------------------------------------------------------------
 * HOOKS
 * -------------------------------------------------------------------------
 */

$GLOBALS['TL_HOOKS']['processFormData'][] = array('FormdataProcessor', 'processSubmittedData');
$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('FormdataProcessor', 'processConfirmationContent');
$GLOBALS['TL_HOOKS']['listComments'][] = array('FormdataComments', 'listComments');
$GLOBALS['TL_HOOKS']['getSearchablePages'][] = array('Formdata', 'getSearchablePages');
$GLOBALS['TL_HOOKS']['executePostActions'][] = array('Formdata', 'executePostActions');
