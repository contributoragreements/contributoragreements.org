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
 * Legends
 */
$GLOBALS['TL_LANG']['tl_module']['efgSearch_legend'] = "Search settings";
$GLOBALS['TL_LANG']['tl_module']['comments_legend'] = "Comments";

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['list_formdata'] = array('Form data table', 'Please select form data table you want to list.');
$GLOBALS['TL_LANG']['tl_module']['efg_list_fields'] = array('Fields', 'Please select the fields you want to list.');
$GLOBALS['TL_LANG']['tl_module']['efg_list_searchtype'] = array('Type of search form', 'Please select the type of search form you want to use.');
$GLOBALS['TL_LANG']['efg_list_searchtype']['none'] = array('None', 'No search form');
$GLOBALS['TL_LANG']['efg_list_searchtype']['dropdown'] = array('Dropdown and input', 'Search form will contain one dropdown to select in which field to search and one text input for the search value');
$GLOBALS['TL_LANG']['efg_list_searchtype']['singlefield'] = array('Single search field', 'Search form will contain one text input. Search will be performed on each of the defined searchable fields.');
$GLOBALS['TL_LANG']['efg_list_searchtype']['multiplefields'] = array('Multiple search fields', 'Search form will contain one text input for each defined searchable field.');

$GLOBALS['TL_LANG']['tl_module']['efg_list_search'] = array('Searchable fields', 'Please select the fields that you want to be searchable in the front end.');
$GLOBALS['TL_LANG']['tl_module']['efg_list_info'] = array('Details page fields', 'Please select the fields you want to show on the details page. Select none to disable the details page feature.');
$GLOBALS['TL_LANG']['tl_module']['efg_iconfolder'] = array('Icons folder', 'Give in the directory containing your icons. If left blank the icons in folder "system/modules/efg/assets/" will be used.');
$GLOBALS['TL_LANG']['tl_module']['efg_fe_keep_id'] = array('Keep record ID on frontend editing', 'When editing in frontend normally a new record is created and therefore a new ID, then the old one is deleted. Choose this option if you rely on an unchanged record ID.');
$GLOBALS['TL_LANG']['tl_module']['efg_fe_no_formatted_mail'] = array('Do not send via e-mail (formatted text or html) on frontend editing', 'Choose this option if you want to deactivate the delivery by e-mail (formatted Text / HTML) when editing in frontend.');
$GLOBALS['TL_LANG']['tl_module']['efg_fe_no_confirmation_mail'] = array('Do not send confirmation via e-mail on frontend editing', 'Choose this option if you want to deactivate the confirmation by e-mail when editing in frontend.');

$GLOBALS['TL_LANG']['tl_module']['efg_list_access'] = array('Display restriction', 'Choose which records should be visible.');
$GLOBALS['TL_LANG']['tl_module']['efg_DetailsKey'] = array('URL fragment for detail page', 'Instead of the default key "details" you can define another key here used in URL for listing detail page.<br />This way an URL like www.domain.tld/page/<b>info</b>/alias.html can be generated, whereas standard URL would be www.domain.tld/page/<b>details</b>/alias.html');
$GLOBALS['TL_LANG']['efg_list_access']['public'] = array('Public', 'Each visitor is allowed to see all records.');
$GLOBALS['TL_LANG']['efg_list_access']['member'] = array('Owner', 'Members are allowed to see their own records only.');
$GLOBALS['TL_LANG']['efg_list_access']['groupmembers'] = array('Group members', 'Members are allowed to see their own records and records of their group members only.');

$GLOBALS['TL_LANG']['tl_module']['efg_fe_edit_access'] = array('Frontend editing', 'Choose option to enable editing records in frontend.');
$GLOBALS['TL_LANG']['efg_fe_edit_access']['none'] = array('No frontend editing', 'Records can not be edited in frontend.');
$GLOBALS['TL_LANG']['efg_fe_edit_access']['public'] = array('Public', 'Each visitor is allowed to edit all records.');
$GLOBALS['TL_LANG']['efg_fe_edit_access']['member'] = array('Owner', 'Members are allowed to edit their own records only.');
$GLOBALS['TL_LANG']['efg_fe_edit_access']['groupmembers'] = array('Group members', 'Members are allowed to edit their own records and records of their group members only.');

$GLOBALS['TL_LANG']['tl_module']['efg_fe_delete_access'] = array('Frontend deleting', 'Choose option to enable deleting records in frontend.');
$GLOBALS['TL_LANG']['efg_fe_delete_access']['none'] = array('No frontend deleting', 'Records can not be deleted in frontend.');
$GLOBALS['TL_LANG']['efg_fe_delete_access']['public'] = array('Public', 'Each visitor is allowed to delete all records.');
$GLOBALS['TL_LANG']['efg_fe_delete_access']['member'] = array('Owner', 'Members are allowed to delete their own records only.');
$GLOBALS['TL_LANG']['efg_fe_delete_access']['groupmembers'] = array('Group members', 'Members are allowed to delete their own records and records of their group members only.');

$GLOBALS['TL_LANG']['tl_module']['efg_fe_export_access'] = array('Frontend CSV export', 'Choose option to enable exporting records as CSV file in frontend.');
$GLOBALS['TL_LANG']['efg_fe_export_access']['none'] = array('No frontend export', 'Records can not be exported in frontend.');
$GLOBALS['TL_LANG']['efg_fe_export_access']['public'] = array('Public', 'Each visitor is allowed to export all records.');
$GLOBALS['TL_LANG']['efg_fe_export_access']['member'] = array('Owner', 'Members are allowed to export their own records only.');
$GLOBALS['TL_LANG']['efg_fe_export_access']['groupmembers'] = array('Group members', 'Members are allowed to export their own records and records of their group members only.');

$GLOBALS['TL_LANG']['tl_module']['efg_com_allow_comments'] = array('Enable comments', 'Allow visitors to comment items.');
$GLOBALS['TL_LANG']['tl_module']['com_moderate'] = array('Moderate comments', 'Approve comments before they are published on the website.');
$GLOBALS['TL_LANG']['tl_module']['com_bbcode'] = array('Allow BBCode', 'Allow visitors to format their comments with BBCode.');
$GLOBALS['TL_LANG']['tl_module']['com_requireLogin'] = array('Require login to comment', 'Allow only authenticated users to create comments.');
$GLOBALS['TL_LANG']['tl_module']['com_disableCaptcha'] = array('Disable the security question', 'Use this option only if you have limited comments to authenticated users.');
$GLOBALS['TL_LANG']['tl_module']['efg_com_per_page'] = array('Comments per page', 'Number of comments per page. Set to 0 to disable pagination.');
$GLOBALS['TL_LANG']['tl_module']['com_order'] = array('Sort order', 'By default, comments are sorted ascending, starting with the oldest one.');
$GLOBALS['TL_LANG']['tl_module']['com_template'] = array('Comments template', 'Here you can select the comments template.');
$GLOBALS['TL_LANG']['tl_module']['efg_com_notify'] = array('Notify', 'Please choose who to notify when comments are added.');
$GLOBALS['TL_LANG']['tl_module']['notify_admin'] = 'System administrator';
$GLOBALS['TL_LANG']['tl_module']['notify_author'] = 'Owner of the item';
$GLOBALS['TL_LANG']['tl_module']['notify_both']  = 'Owner and system administrator';
