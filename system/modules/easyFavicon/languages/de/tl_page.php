<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   easyFavicon
 * @author    Markus Kinzl
 * @license   LGPL
 * @copyright Web-Creations 2014
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page']['addFavicon']			= array('Favicon hinzufügen','Der Seite ein Favicon hinzufügen.');
$GLOBALS['TL_LANG']['tl_page']['faviconSRC']			= array('Favicon-Quelldatei','Bitte wählen Sie ein Favicon aus.');
$GLOBALS['TL_LANG']['tl_page']['addAppleTouchIcon']		= array('Apple Touch Icon hinzufügen','Der Seite ein Apple Touch Icon hinzufügen.');
$GLOBALS['TL_LANG']['tl_page']['appleTouchIconSRC']		= array('Apple Touch Icon-Quelldatei','Bitte wählen Sie ein Apple Touch Icon (png-Datei) aus. Datei wird auf 152x152px zugeschnitten.');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_page']['favicon_legend'] = 'Favicon';

/**
 * Explanation
 */
$GLOBALS['TL_LANG']['XPL']['faviconSRCexpl'] = array(
	array('Ico-Datei', 'Bei einer Ico-Datei wird die Originaldatei eingebunden.'),
	array('Png-/ Gif-/ Jpg-Datei', 'Wird in eine Ico-Datei umgewandelt.<br><br>Folgende Größen sind in Ico-Datei nach der Konvertierung verfügbar: 16x16, 24x24, 32x32, 48x48<br><br>Bei einer animierten Gif-Datei wird das erste Bild für die Ico-Datei verwendet.')
);