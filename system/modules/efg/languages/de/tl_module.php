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


$GLOBALS['TL_LANG']['tl_module']['efgSearch_legend'] = 'Sucheinstellungen';
$GLOBALS['TL_LANG']['tl_module']['comments_legend'] = 'Kommentare';
$GLOBALS['TL_LANG']['tl_module']['list_formdata'] = array('Formular-Daten-Tabelle', 'Bitte wählen Sie die Formular-Daten-Tabelle, deren Datensätze Sie auflisten möchten.');
$GLOBALS['TL_LANG']['tl_module']['efg_list_fields'] = array('Felder', 'Bitte wählen Sie die Felder, die Sie auflisten möchten.');
$GLOBALS['TL_LANG']['tl_module']['efg_list_searchtype'] = array('Typ des Such-Formulars', 'Bitte wählen Sie, welchen Typ des Such-Formulars Sie verwenden möchten.');
$GLOBALS['TL_LANG']['tl_module']['efg_list_search'] = array('Durchsuchbare Felder', 'Bitte wählen Sie die Felder, die im Frontend durchsuchbar sein sollen.');
$GLOBALS['TL_LANG']['tl_module']['efg_list_info'] = array('Felder der Detailseite', 'Bitte wählen Sie die Felder, die Sie auf der Detailseite anzeigen möchten. Wählen Sie kein Feld, um die Detailansicht eines Datensatzes zu deaktivieren.');
$GLOBALS['TL_LANG']['tl_module']['efg_iconfolder'] = array('Verzeichnis der Icons', 'Tragen Sie hier das Verzeichnis Ihrer Icons ein. Falls das Feld nicht ausgefüllt wird, werden die Icons im Verzeichnis "system/modules/efg/assets/" verwendet.');
$GLOBALS['TL_LANG']['tl_module']['efg_fe_keep_id'] = array('Datensatz-ID beibehalten bei Frontendbearbeitung', 'Bei der Frontendbearbeitung wird normalerweise ein neuer Datensatz -somit neue ID- angelegt, anschließend der alte gelöscht. Wählen Sie diese Option, falls Sie auf eine unveränderte Datensatz-ID angewiesen sind.');
$GLOBALS['TL_LANG']['tl_module']['efg_fe_no_formatted_mail'] = array('Kein Versand per E-Mail (formatierter Text / HTML) bei Frontend-Bearbeitung', 'Wählen Sie diese Option, wenn der im Formulargenerator eingestellte Versand per E-Mail (formatierter Text / HTML) bei Frontend-Bearbeitung nicht erfolgen soll.');
$GLOBALS['TL_LANG']['tl_module']['efg_fe_no_confirmation_mail'] = array('Kein Versand der Bestätigung per E-Mail bei Frontend-Bearbeitung', 'Wählen Sie diese Option, wenn der im Formulargenerator eingestellte Versand einer Bestätigungs-Mail bei Frontend-Bearbeitung nicht erfolgen soll.');
$GLOBALS['TL_LANG']['tl_module']['efg_list_access'] = array('Anzeige Einschränkung', 'Wählen Sie, welche Daten angezeigt werden dürfen.');
$GLOBALS['TL_LANG']['tl_module']['efg_DetailsKey'] = array('URL-Fragment der Detailseite', 'Anstelle der Vorgabe "details" in der URL der Auflistungs-Detailseite können Sie hier einen abweichenden Begriff angeben.<br />Dadurch kann z.B. eine URL www.domain.tld/page/<b>info</b>/alias.html statt der Standard-URL www.domain.tld/page/<b>details</b>/alias.html erzeugt werden.');
$GLOBALS['TL_LANG']['tl_module']['efg_fe_edit_access'] = array('Bearbeitung im Frontend', 'Wählen Sie, ob Daten im Frontend bearbeitet werden dürfen.');
$GLOBALS['TL_LANG']['tl_module']['efg_fe_delete_access'] = array('Löschen im Frontend', 'Wählen Sie, ob Daten im Frontend gelöscht werden dürfen.');
$GLOBALS['TL_LANG']['tl_module']['efg_fe_export_access'] = array('CSV-Export im Frontend', 'Wählen Sie, ob Daten im Frontend als CSV-Datei exportiert werden dürfen.');
$GLOBALS['TL_LANG']['tl_module']['efg_com_allow_comments'] = array('Kommentare aktivieren', 'Besuchern das Kommentieren von Einträgen erlauben.');
$GLOBALS['TL_LANG']['tl_module']['com_moderate'] = array('Kommentare moderieren', 'Kommentare erst nach Bestätigung auf der Webseite veröffentlichen.');
$GLOBALS['TL_LANG']['tl_module']['com_bbcode'] = array('BBCode erlauben', 'Besuchern das Formatieren ihrer Kommentare mittels BBCode erlauben.');
$GLOBALS['TL_LANG']['tl_module']['com_requireLogin'] = array('Login zum Kommentieren benötigt', 'Nur angemeldeten Benutzern das Erstellen von Kommentaren erlauben.');
$GLOBALS['TL_LANG']['tl_module']['com_disableCaptcha'] = array('Sicherheitsfrage deaktivieren', 'Wählen Sie diese Option nur, wenn das Erstellen von Kommentaren auf authentifizierte Benutzer beschränkt ist.');
$GLOBALS['TL_LANG']['tl_module']['efg_com_per_page'] = array('Kommentare pro Seite', 'Anzahl an Kommentaren pro Seite. Geben Sie 0 ein, um den automatischen Seitenumbruch zu deaktivieren.');
$GLOBALS['TL_LANG']['tl_module']['com_order'] = array('Sortierung', 'Standardmäßig werden Kommentare aufsteigend sortiert, beginnend mit dem ältesten.');
$GLOBALS['TL_LANG']['tl_module']['com_template'] = array('Kommentartemplate', 'Hier können Sie das Kommentartemplate auswählen.');
$GLOBALS['TL_LANG']['tl_module']['efg_com_notify'] = array('Benachrichtigung an', 'Bitte legen Sie fest, wer beim Hinzufügen neuer Kommentare benachrichtigt wird.');
$GLOBALS['TL_LANG']['tl_module']['notify_admin'] = 'Systemadministrator';
$GLOBALS['TL_LANG']['tl_module']['notify_author'] = 'Besitzer des Eintrags';
$GLOBALS['TL_LANG']['tl_module']['notify_both'] = 'Besitzer und Systemadministrator';
$GLOBALS['TL_LANG']['efg_list_searchtype']['none'] = array('Keine Suche', 'Kein Suchformular');
$GLOBALS['TL_LANG']['efg_list_searchtype']['dropdown'] = array('Dropdown und Eingabefeld', 'Das Suchformular enthält ein DropDown zur Auswahl des zu durchsuchenden Feldes und ein Eingabefeld für den Suchbegriff.');
$GLOBALS['TL_LANG']['efg_list_searchtype']['singlefield'] = array('Einzelnes Eingabefeld', 'Das Suchformular enthält ein einzelndes Eingabefeld für den Suchbegriff. Bei der Suche werden alle als durchsuchbare Felder definierten Felder berücksichtigt.');
$GLOBALS['TL_LANG']['efg_list_searchtype']['multiplefields'] = array('Mehrere Eingabefelder', 'Das Suchformular enthält für jedes durchsuchbare Feld ein separates Eingabefeld für den Suchbegriff.');
$GLOBALS['TL_LANG']['efg_list_access']['public'] = array('Öffentlich', 'Jeder Seitenbesucher darf alle Daten sehen.');
$GLOBALS['TL_LANG']['efg_list_access']['member'] = array('Besitzer', 'Mitglieder dürfen nur ihre eigenen Daten sehen.');
$GLOBALS['TL_LANG']['efg_list_access']['groupmembers'] = array('Gruppen-Mitglieder', 'Mitglieder dürfen ihre eigenen und die Daten ihrer Gruppen-Mitglieder sehen.');
$GLOBALS['TL_LANG']['efg_fe_edit_access']['none'] = array('Keine Bearbeitung', 'Daten können nicht im Frontend bearbeitet werden.');
$GLOBALS['TL_LANG']['efg_fe_edit_access']['public'] = array('Öffentlich', 'Jeder Seitenbesucher darf alle Daten bearbeiten.');
$GLOBALS['TL_LANG']['efg_fe_edit_access']['member'] = array('Besitzer', 'Mitglieder dürfen nur ihre eigenen Daten bearbeiten.');
$GLOBALS['TL_LANG']['efg_fe_edit_access']['groupmembers'] = array('Gruppen-Mitglieder', 'Mitglieder dürfen ihre eigenen und die Daten ihrer Gruppen-Mitglieder bearbeiten.');
$GLOBALS['TL_LANG']['efg_fe_delete_access']['none'] = array('Kein Löschen', 'Daten können nicht im Frontend gelöscht werden.');
$GLOBALS['TL_LANG']['efg_fe_delete_access']['public'] = array('Öffentlich', 'Jeder Seitenbesucher darf alle Daten löschen.');
$GLOBALS['TL_LANG']['efg_fe_delete_access']['member'] = array('Besitzer', 'Mitglieder dürfen nur ihre eigenen Daten löschen.');
$GLOBALS['TL_LANG']['efg_fe_delete_access']['groupmembers'] = array('Gruppen-Mitglieder', 'Mitglieder dürfen ihre eigenen und die Daten ihrer Gruppen-Mitglieder löschen.');
$GLOBALS['TL_LANG']['efg_fe_export_access']['none'] = array('Kein Export', 'Daten können nicht im Frontend exportiert werden.');
$GLOBALS['TL_LANG']['efg_fe_export_access']['public'] = array('Öffentlich', 'Jeder Seitenbesucher darf alle Daten exportieren.');
$GLOBALS['TL_LANG']['efg_fe_export_access']['member'] = array('Besitzer', 'Mitglieder dürfen nur ihre eigenen Daten exportieren.');
$GLOBALS['TL_LANG']['efg_fe_export_access']['groupmembers'] = array('Gruppen-Mitglieder', 'Mitglieder dürfen ihre eigenen und die Daten ihrer Gruppen-Mitglieder exportieren.');
