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
 * Namespace
 */
namespace WebCreations;


/**
 * Class easyFavicon
 *
 * @copyright  Web-Creations 2014
 * @author     Markus Kinzl
 */
class easyFavicon extends \Frontend
{
	protected $icoSizes = array(
	    array( 16, 16 ),
	    array( 24, 24 ),
	    array( 32, 32 ),
	    array( 48, 48 ),
	);
	
	//iOS 8.x largest icon size
	protected $appleTouchIconSize = array(180, 180);	
	
	/**
	 * add favicon to page
	 */
	public function addFavicon($objPage, $objLayout, $self)
	{
		//get root page details
		$rootPage = $this->getPageDetails( $objPage->rootId );
		
		//check if favicon or appletouchicon is active
		if( !$rootPage->addFavicon && !$rootPage->addAppleTouchIcon )
			return false;
		
		//xhtml or html5
		$blnXhtml = ($objPage->outputFormat == 'xhtml');
		
		//regular favicon
		if( $rootPage->addFavicon )
		{
			$objFavicon = \FilesModel::findByPk( $rootPage->faviconSRC );
			
			$favicon = false;
			
			// ico file
			if( $objFavicon !== NULL && is_file( TL_ROOT . '/' . $objFavicon->path ) && $objFavicon->extension == 'ico' )
			{
				$favicon = $objFavicon->path;
			}
			// png, jpg, gif file
			elseif( $objFavicon !== NULL && is_file( TL_ROOT . '/' . $objFavicon->path ) && ($objFavicon->extension == 'jpg' || 'jpeg' || 'gif' || 'png') )
			{
				$favicon = 'share/favicon-' . $rootPage->alias . '.ico';
				$this->createIco( TL_ROOT . '/' . $objFavicon->path, $favicon );
			}
			
			if( $favicon )
				$GLOBALS['TL_HEAD'][] = '<link rel="shortcut icon" type="image/x-icon" href="'.$this->Environment->base . $favicon.'"' . ($blnXhtml ? ' />' : '>');
						
		}
		
		//apple touch icon
		if( $rootPage->addAppleTouchIcon )
		{
			$objAppleTouchIcon = \FilesModel::findByPk( $rootPage->appleTouchIconSRC );
			
			if( $objAppleTouchIcon !== NULL && is_file( TL_ROOT . '/' . $objAppleTouchIcon->path ) )
			{
				$faviconAppleTouchIcon = 'share/favicon-apple-touch-icon-' . $rootPage->alias . '.png';
				
				//create resized png icon
				$this->getImage( $objAppleTouchIcon->path, $this->appleTouchIconSize[0], $this->appleTouchIconSize[1], 'center_center', $faviconAppleTouchIcon);
				
				$GLOBALS['TL_HEAD'][] = '<link rel="apple-touch-icon" href="'.$this->Environment->base . $faviconAppleTouchIcon.'"' . ($blnXhtml ? ' />' : '>');
			}
		}
	}
	
	/**
	 * create favicon from jpg, gif or png file
	 */
	private function createIco( $srcFile, $savePath )
	{
		//check if favicon already exists
		if( is_file( TL_ROOT . '/' . $savePath ) )
			return;
		
		require_once 'class-php-ico.php';
		
		$icoFile = new \PHP_ICO( $srcFile, $this->icoSizes );
		
		// create converted favicon file
		$objFile = new \File( $savePath );
		$objFile->write( $icoFile->_get_ico_data() );
		$objFile->close();
	}
}