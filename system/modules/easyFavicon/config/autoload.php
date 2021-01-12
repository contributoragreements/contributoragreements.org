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
 * Register the namespace
 */
ClassLoader::addNamespace('WebCreations');


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'WebCreations\easyFavicon' => 'system/modules/easyFavicon/classes/easyFavicon.php',
));
