<?php
	/* 
offs  size  data        		content
---------------------------------------
0000 , 02 : 5d 00 				biff type, Object
0002 , 02 : 42 00       		record length
0004 , 04 : 01 00 00 00 		count of the objects in the file
0008 , 02 : 08 00				object type , 8 = picture
000a , 02 : 01 00 				object ID
000c , 02 : 14 06 				grbit
									bit0		: =1, if the object selected
									bit1		: =1, if the object moves and sizes with the cells
									bit2		: =1, if the object moves with the cells
									bit3		: reserved
									bit4		: =1, if the object is locked when the sheet is protected
									bit5..6		: reserved
									bit7		: =1, if the object is part of a group of objects
									bit8		: =1, if the object is hidden
									bit9		: =1, if the object is visible
									bit10		: =1, if the object is printable
									bit11..15	: reserved
000e , 02 : 01 00 				col-left
0010 , 02 : 00 00 				x position in left column
0012 , 02 : 01 00				row-top
0014 , 02 : 00 00 				y position in top row
0016 , 02 : 01 00 				col-right
0018 , 02 : 30 00 				x position in right column
001a , 02 : 01 00 				row-bottom
001c , 02 : 26 00 				y position in bottom row
001e , 02 : 00 00 				fmla structure length
0020 , 04 : 00 00 05 00	00 00	reserved , must be zero ?
									word-offset 0 : = 0
									word offset 1 : = 0x0005
									word-offset 2 : = 0
0026 , 01 : 09 					background color index
0027 , 01 : 09					foreground color index
0028 , 01 : 00 					fill pattern
0029 , 01 : 00 					auto fill, bit0 = 1 if the automatic fill is turned on
002a , 01 : 08 					line color index
002b , 01 : ff 					line style index
002c , 01 : 01 					line weight
002d , 01 : 00 					auto border, bit0 = 1 if the automatic borderis turned on
002e , 02 : 00 00 				frame style
									bit0		: =1 , if the rectangle has rounded corners
									bit1		: =1 , if the rectangle has a shadow border
									bit2..9 	: diameter of the oval (actualy a circle) that defines the rounded corners (if bit1 set)
									bit10..15	: unused
0030 , 02 : 09 00 				image format
0032 , 04 : b9 10 05 37 		reserved, must be zero ? wtf ???
0036 , 02 : 00 00 				length of the picture FMLA structure
0038 , 02 : 00 00 				reserved
003a , 02 : 01 00 				grbit
									bit0 	 : =0 if user manualy sizes picture  by dragging a handle
									bit1 	 : =1 if FMLA structure is a DDE reference
									bit2 	 : =1 if the picture is from a DDE link, and the only available representation of the picture is an icon
									bit3..15 : unused, must be zero ...
003c , 04 : 00 00 00 00 		reserved
0040 , 01 : 05 					length of the name
0041 , nn : 4b e9 70 20 31 		name ,maybe contain a padding byte to force word boundary allignment (=Kép 1)

0000 , 02 : 7f 00 				biff type, ImageData
0002 , 02 : 44 00 				record length
0004 , 02 : 09 00 				image format, 9 = windows bitmap format , 24bit truecolor
0006 , 02 : 01 00 				enviroment from which file was written, 1= windows
0008 , 04 : 3c 00 00 00 		image data length
000a ....							image data
			
			0c 00 00 00 04 00 04 00 01 00 18 00 
			ff 00 00 ff ff ff ff ff ff 00 00 00 
			ff ff ff ff 00 00 00 00 00 ff ff ff 
			ff ff ff 00 00 ff 00 ff 00 ff ff ff 
			00 00 ff ff ff ff ff ff ff 00 ff 00
	
	*/
	
	class xls_picture {
		var $picture_array = null;
		var $filename = null;
		var $bgcolor = null;
		var $bgcolorrgb = null;
		var $fgcolor = null;
		var $objectid = null;
		var $firstrow = null;
		var $firstcol = null;
		var $lastrow = null;
		var $lastcol = null;
		var $imageright = null;
		var $imagebottom = null;
		var $imagewidth = null;
		var $imageheight = null;
		
		function xls_picture($afilename, $abgcolor, $afgcolor, $abgcolorrgb, $aobjectid, $arow, $acol) {
			$this->picture_array = array();
			$this->filename = $afilename;
			
			$tmp = explode(".",$afilename);
			if (count($tmp)<2) { die("missing filename extension."); }
			if (!file_exists($afilename)) { die("file does not exist."); }
			
			$this->bgcolor = $abgcolor;
			$this->bgcolorrgb = $abgcolorrgb;
			$this->fgcolor = $afgcolor;
			$this->objectid = $aobjectid;
			$this->firstrow = $arow;
			$this->firstcol = $acol;
		}
		
		function scanimgline($imagehd, $line, $width, $bgcolor) {
			for ($x=0; $x!=$width; $x++) {
				$colorindex = imagecolorat($imagehd, $x, $line);
				$rgb = imagecolorsforindex($imagehd, $colorindex);
				if ($rgb["alpha"]!=0) {
					$dstR = $bgcolor >> 16 & 0xFF;
					$dstG = $bgcolor >> 8 & 0xFF;
					$dstB = $bgcolor & 0xFF;
				
					$rgb["red"]   = (($rgb["red"]   * (0xFF-$rgb["alpha"])) >> 8) + (($dstR * $rgb["alpha"]) >> 8);
					$rgb["green"] = (($rgb["green"] * (0xFF-$rgb["alpha"])) >> 8) + (($dstG * $rgb["alpha"]) >> 8);
					$rgb["blue"]  = (($rgb["blue"]  * (0xFF-$rgb["alpha"])) >> 8) + (($dstB * $rgb["alpha"]) >> 8);
				}
				$this->picture_array[] = $rgb["blue"];
				$this->picture_array[] = $rgb["green"];
				$this->picture_array[] = $rgb["red"];
				/*
				$this->picture_array[] = ($colorindex >> 16) & 0x0ff;
				$this->picture_array[] = ($colorindex >> 8) & 0x0ff;
				$this->picture_array[] = $colorindex & 0x0ff;
				*/
			}
			if ((count($this->picture_array) & 0x3)!=0) {
				$fsize = count($this->picture_array) & 0x3;
				$reqbytes = 0x4 - $fsize;
				while ($reqbytes>0) {
					$this->picture_array[] = 0;
				}
			}
		}
		
		function loaddata() {
			$tmp = explode(".",$this->filename);
			$ext = strtolower($tmp[count($tmp)-1]);
			switch ($ext) {
				case "bmp"	:	die("Windows BitMaP file not supported.");
								break;
				case "gif"	:	$img=imagecreatefromgif($this->filename);
								break;
				case "jpg"	:
				case "jpeg"	:	$img=imagecreatefromjpeg($this->filename);
								break;
				case "png"	:	$img=imagecreatefrompng($this->filename);
								break;
				case "xbm"	:	$img=imagecreatefromxbm($this->filename);
								break;
				case "xpm"	:	$img=imagecreatefromxpm($this->filename);
								break;
				default	:	die("Invalid / unknown filetype (extension).");
							break;
			}
			$this->imagewidth = imagesx($img);
			$this->imageheight = imagesy($img);
			$y=$this->imageheight;
			while ($y>0) {
				$y--;
				$this->scanimgline($img, $y, $this->imagewidth, $this->bgcolorrgb);
			}
			imagedestroy($img);
		}
		
		function save($filehandle,$xls_biffobject) {
			$xls_biffobject->clear(BIFF_OBJECT);
			$xls_biffobject->append(XLSDATA_LONG,1);					// one object
			$xls_biffobject->append(XLSDATA_SHORT,8);					// obj,type = picture
			$xls_biffobject->append(XLSDATA_SHORT,$this->objectid);
			$xls_biffobject->append(XLSDATA_SHORT,0x0614);
			$xls_biffobject->append(XLSDATA_SHORT,$this->firstcol);
			$xls_biffobject->append(XLSDATA_SHORT,0);
			$xls_biffobject->append(XLSDATA_SHORT,$this->firstrow);
			$xls_biffobject->append(XLSDATA_SHORT,0);
			$xls_biffobject->append(XLSDATA_SHORT,$this->lastcol);
			$xls_biffobject->append(XLSDATA_SHORT,$this->imageright);
			$xls_biffobject->append(XLSDATA_SHORT,$this->lastrow);
			$xls_biffobject->append(XLSDATA_SHORT,$this->imagebottom);
			$xls_biffobject->append(XLSDATA_SHORT,0);
			
			$xls_biffobject->append(XLSDATA_SHORT,0);
			$xls_biffobject->append(XLSDATA_SHORT,0x0005);
			$xls_biffobject->append(XLSDATA_SHORT,0);
			
			$xls_biffobject->append(XLSDATA_BYTE,$this->bgcolor);
			$xls_biffobject->append(XLSDATA_BYTE,$this->fgcolor);
			$xls_biffobject->append(XLSDATA_BYTE,0);
			$xls_biffobject->append(XLSDATA_BYTE,0);
			$xls_biffobject->append(XLSDATA_BYTE,8);
			$xls_biffobject->append(XLSDATA_BYTE,0xff);
			$xls_biffobject->append(XLSDATA_BYTE,1);
			$xls_biffobject->append(XLSDATA_BYTE,0);
			$xls_biffobject->append(XLSDATA_SHORT,0);
			$xls_biffobject->append(XLSDATA_SHORT,0x0009);
			//$xls_biffobject->append(XLSDATA_LONG,0x370510b9);
			$xls_biffobject->append(XLSDATA_LONG,0x00000000);
			$xls_biffobject->append(XLSDATA_SHORT,0);
			$xls_biffobject->append(XLSDATA_SHORT,0);
			$xls_biffobject->append(XLSDATA_SHORT,0x0001);
			$xls_biffobject->append(XLSDATA_LONG,0x0000);
			
			$s = "Picture".$this->objectid;
			$xls_biffobject->append(XLSDATA_STRING,$s);
			$xls_biffobject->save($filehandle);
			
			$xls_biffobject->clear(BIFF_IMAGEDATA);
			$xls_biffobject->append(XLSDATA_SHORT,0x0009);
			$xls_biffobject->append(XLSDATA_SHORT,0x0001);
			$xls_biffobject->append(XLSDATA_LONG,count($this->picture_array)+12);
			
			if (defined("PICTUREDEBUG")) {
				echo "picture size : ".count($this->picture_array)." (".dechex(count($this->picture_array)).")<br>\n";
			}
			$xls_biffobject->append(XLSDATA_LONG,0x0000000c);
			$xls_biffobject->append(XLSDATA_SHORT,$this->imagewidth);
			$xls_biffobject->append(XLSDATA_SHORT,$this->imageheight);
			$xls_biffobject->append(XLSDATA_SHORT,1);
			$xls_biffobject->append(XLSDATA_SHORT,24);
			
			foreach ($this->picture_array as $data) {
				$xls_biffobject->append(XLSDATA_BYTE,$data);
			}
			$xls_biffobject->save($filehandle);
		}
	}
?>