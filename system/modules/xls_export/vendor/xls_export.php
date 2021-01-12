<?php
	require_once "fat.php";
	
	define("CELL_STRING",1);
	define("CELL_FLOAT",2);
	define("CELL_PICTURE",3);
		
	define("XLSFILE_DEFAULT_FONTNAME","Albany");
	define("XLSFILE_DEFAULT_FONTHEIGHT",0x00c8);
	define("XLSFILE_DEFAULT_FGCOLOR",0x0008);
	define("XLSFILE_DEFAULT_ROWHEIGHT",0x012c);
	define("XLSFILE_DEFAULT_COLWIDTH",0x0924);
	define("XLSFILE_CHARACTERSET",0x00);		// = LATIN , see values in font.php XLSFONT_CHARACTERSET_*
	
	define("XLSCELLHALLIGN_LEFT",0);
	define("XLSCELLHALLIGN_RIGHT",1);
	define("XLSCELLHALLIGN_CENTER",2);
	
	define("FILE_HEADER_NUMBEROFFATSECTORS",0x2c);
	define("FILE_HEADER_DIRECTORYSTART",0x30);
	define("FILE_HEADER_MINIFATSTART",0x3c);
	define("FILE_HEADER_DIFCHAINSTART",0x40);
	define("FILE_HEADER_DIFSECTORSCOUNT",0x44);
	define("FILE_HEADER_FIRSTFATENTRY",0x4c);
	
	require_once "biff.php";
	require_once "palette.php";
	require_once "font.php";
	require_once "xf.php";
	require_once "picture.php";
	require_once "mergedcells.php";
	
	class xlsexport {
		
		protected $default_cell_allign  = XLSCELLHALLIGN_LEFT;
		
		var       $page_header = "&L&C&[TAB]&R";
		var       $page_footer = "&L&CPage &[PAGE]&R";
		
		// $xlsdocument struct
		// "worksheetid" => index of $worksheets
		// "worksheetname" => data
		//
		// data struct
		// array of $rows
		//
		// rows struct
		// array of $cells
		//
		// cell struct
		// $data, 
		// type : *xls_string/xls_int/xls_float/xls_expression, 
		// format,
		// font-index : *,
		// xf-index : *
		
		var $xlsfilehandle = null;
		var $file_header = null;
		var $xlsdocument = null;
		var $xlsstreamsize = null;
		var $rootstorageoffset = null;
		var $xls_bof = null;
		var $palette = null;
		var $xf = null;
		var $xls_bofstart = -1;
		var $sheetinformationpos = -1;
		var $objectcounter = 1;
		
		// array struct
		// (sheetname, first_row, last_row, first_col, last_col, sheetinfopos, fileoffset, colwidths, rowheights, defcolwidthoffset, rowrecordsoffset)
		var $worksheets = null;
		
		public function __construct() {
			$this->xlsdocument = array();
			$this->xls_bof = new xls_bof(0);
			$this->palette = new xls_palette();
			$this->font = new xls_font(0);
			$this->xf = new xls_xf(0);
		}
		
		public function addworksheet($sheetname) {
			if (isset($this->worksheets[$sheetname])) { return false; }
			$this->xlsdocument[$sheetname]["document"] = array();
			$this->xlsdocument[$sheetname]["worksheetid"] = count($this->worksheets);
			$this->worksheets[] = array("sheetname" => $sheetname, 
										"first_row" => 0xffffffff, 
										"last_row" => 0, 
										"first_col" => 0xffffffff, 
										"last_col" => 0, 
										"sheetinfopos" => -1, 
										"fileoffset" => -1, 
										"colwidths" => array(),
										"rowheights" => array(),
										"defcolwidthoffset" => 0xffffffff, 
										"rowrecordsoffset" => 0xffffffff);
			return true;
		}
		
		public function setcolwidth($sheetname, $acolidx, $awidth) {
			$sheetid = $this->xlsdocument[$sheetname]["worksheetid"];
			$this->worksheets[$sheetid]["colwidths"][$acolidx] = $awidth;
		}
		
		public function setrowheight($sheetname, $arowidx, $aheight) {
			$sheetid = $this->xlsdocument[$sheetname]["worksheetid"];
			$this->worksheets[$sheetid]["rowheights"][$arowidx] = $aheight;
		}
		
		public function merge_cells($sheetname, $firstrow, $lastrow, $firstcol, $lastcol) {
			if (defined("MERGEDEBUG")) {
				echo "merge cells , sheet : $sheetname , (fr,lr) : (fc,lc) = ($firstrow,$lastrow) : ($firstcol,$lastcol)<br>\n";
			}
			if (!isset($this->xlsdocument[$sheetname]["mergedcells"])) {
				$this->xlsdocument[$sheetname]["mergedcells"] = new xls_mergedcells();
			}
			$this->xlsdocument[$sheetname]["mergedcells"]->merge_cells($firstrow, $lastrow, $firstcol, $lastcol);
			if (defined("MERGEDEBUG")) {
				print_r($this->xlsdocument[$sheetname]["mergedcells"]);
			}
		}
		
		public function setcell($args) {
			if (($this->worksheets==null) || (count($this->worksheets)==0)) { return false; }
			if (!isset($args["data"])) { return false; }
			$sheetname = (isset($args["sheetname"]) ? $args["sheetname"] : $this->worksheets[0]["sheetname"]);
			if (isset($args["row"])) { $row = $args["row"]; }
			else {
				if (count($this->xlsdocument[$sheetname]["document"])<1) { 
					$row = 0;
					$col = 0;
				}
				else { $row = count($this->xlsdocument[$sheetname]["document"]); }
			}
			if (isset($args["col"]) && (!isset($col))) { $col = $args["col"]; }
			else {
				if (isset($this->xlsdocument[$sheetname]["document"][$row])) { $col = count($this->xlsdocument[$sheetname]["document"][$row]); }
				else { $col=0; }
			}
			/*
			if (!isset($this->worksheets[$this->xlsdocument[$sheetname]["worksheetid"]]["colwidths"][$col])) {
				$this->setcolwidth($sheetname,$col,XLSFILE_DEFAULT_COLWIDTH);
			}
			if (!isset($this->worksheets[$this->xlsdocument[$sheetname]["worksheetid"]]["rowheights"][$row])) {
				$this->setrowheight($sheetname,$row,XLSFILE_DEFAULT_ROWHEIGHT);
			}
			*/
			if (isset($this->xlsdocument[$sheetname]["mergedcells"])) {
				$pos = $this->xlsdocument[$sheetname]["mergedcells"]->findpos($row,$col);
				if ($pos!==false) {
					if (($pos["row"]!=$row) || ($pos["col"]!=$col)) { return false; }
				}
			}
			$cell		= array("type" => CELL_STRING, "xfindex" => -1);
			$xfrec 		= array();
			$fontrec 	= array();
			
			foreach ($args as $key => $param) {
				switch ($key) {
					case "data"		: 	
					case "type"		:	$cell[$key] = $param;
										break;
					case "bgcolor" :
					case "backgroundcolor" :
					case "background-color" : 	$xfrec["patternbgcolor"]=$param;
												break;
					case "textrotate" :			$xfrec["rotate"]=$param;
												break;
					case "textwrap" :
					case "hallign"	:
					case "vallign"	:
					case "bordertop" :
					case "borderleft" :
					case "borderright" :
					case "borderbottom" :
					case "bordertopcolor" :
					case "borderleftcolor" :
					case "borderrightcolor" :
					case "borderbottomcolor" :
					case "bordercolor" :
					case "border"	:		$xfrec[$key] = $param;
											break;
					
					case "fontname"		:	$fontrec["name"] = $param;
											break;
					case "fontheight"	:
					case "fontsize"		:	$fontrec["height"] = $param;
											break;
					case "fontweight"	:	$fontrec["weight"] = $param;
											break;
					case "color" :
					case "fgcolor" :
					case "fontcolor"	:	$fontrec["color"] = $param;
											break;
					case "fontstyle"	:	$fontrec["style"] = $param;
											break;
					case "fontfamily"	:	$fontrec["family"] = $param;
											break;
					case "fontescape"	:	$fontrec["escape"] = $param;
											break;
					case "underline"	:	$fontrec["underline"] = $param;
											break;
				}
			}
			if (($cell["type"]==CELL_PICTURE) && (isset($fontrec["color"]))) {
				$cell["color"]=$this->palette->getcoloridx($fontrec["color"]);
				$cell["xfindex"] = $this->xf->defaultxf;
			}
			else if ((count($xfrec)>0) || (count($fontrec)>0)) {
				$xfcheck = array("patternbgcolor","bordertopcolor","borderleftcolor","borderrightcolor","borderbottomcolor","bordercolor");
				foreach ($xfcheck as $key) {
					if (isset($xfrec[$key])) {
						$xfrec[$key] = $this->palette->getcoloridx($xfrec[$key]);
					}
				}
				
				if (isset($fontrec["color"])) { $fontrec["color"] = $this->palette->getcoloridx($fontrec["color"]); }
				
				$fontindex = $this->font->append($fontrec);
				if (defined("FONTDEBUG")) {
					if (count($fontrec)>0) {
						echo "fontrec :\n";
						print_r($fontrec);
						echo "fontindex : $fontindex\n";
					}
				}
				if ($fontindex!=0) {
					$xfrec["fontindex"] = $fontindex;
				}
				$xfindex = $this->xf->append($xfrec);
				$cell["xfindex"] = $xfindex;
				if (defined("CELLINSERTDEBUG")) {
					print_r($cell);
				}
			}
			else {
				$cell["xfindex"] = $this->xf->defaultxf;
			}
			
			$this->xlsdocument[$sheetname]["document"][$row][$col] = $cell;
			$this->worksheets[$this->xlsdocument[$sheetname]["worksheetid"]]["first_row"] = min($row,$this->worksheets[$this->xlsdocument[$sheetname]["worksheetid"]]["first_row"]);
			$this->worksheets[$this->xlsdocument[$sheetname]["worksheetid"]]["last_row"]  = max($row,$this->worksheets[$this->xlsdocument[$sheetname]["worksheetid"]]["last_row"]);
			$this->worksheets[$this->xlsdocument[$sheetname]["worksheetid"]]["first_col"] = min($col,$this->worksheets[$this->xlsdocument[$sheetname]["worksheetid"]]["first_col"]);
			$this->worksheets[$this->xlsdocument[$sheetname]["worksheetid"]]["last_col"]  = max($col,$this->worksheets[$this->xlsdocument[$sheetname]["worksheetid"]]["last_col"]);
			return true;
		}
	
	
	public function savefile($afilename) {
		if (file_exists($afilename)) {
			unlink($afilename);
		}
		
		$this->xlsfilehandle = fopen($afilename,"x+");
		$this->file_headerinit();
		
		$this->xls_bofstart = ftell($this->xlsfilehandle);
		$this->xls_bof->clear(XLS_BIFF5);
		$this->xls_bof->append(XLSDATA_SHORT,0x0500);
		$this->xls_bof->append(XLSDATA_SHORT,BIFF_WORKBOOKGLOBALS);
		$this->xls_bof->append(XLSDATA_SHORT,0x096c);					// build identifier
		$this->xls_bof->append(XLSDATA_SHORT,0x07c9);					// build year = 1993
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_INTERFACEHEADER);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_MENURECORDGROUP);
		$this->xls_bof->append(XLSDATA_BYTE,0);
		$this->xls_bof->append(XLSDATA_BYTE,0);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_TOOLBARHEADER);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_TOOLBAREND);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_INTERFACEEND);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_WRITEACCESS);
		$s = $this->xls_strpad("APACHE2/PHP-XLS Generator",31);
		$this->xls_bof->append(XLSDATA_STRING1,$s);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_CODEPAGE);
		$this->xls_bof->append(XLSDATA_SHORT,0x04e4);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_FUNCTIONGROUPCOUNT);
		$this->xls_bof->append(XLSDATA_SHORT,0x000e);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_writeexterninfo();
		
		foreach ($this->worksheets as $key => $sheetdata) {
			$this->xls_bof->clear(BIFF_DEFINEDNAME);
			$this->xls_bof->append(XLSDATA_SHORT,0x0020);			// built-in-name
			$this->xls_bof->append(XLSDATA_BYTE,0x00);				// keyboard shortcut
			$this->xls_bof->append(XLSDATA_BYTE,0x01);
			$this->xls_bof->append(XLSDATA_SHORT,strlen($sheetdata["sheetname"])+2);
			$this->xls_bof->append(XLSDATA_SHORT,$key+1);
			$this->xls_bof->append(XLSDATA_SHORT,$key+1);
			$this->xls_bof->append(XLSDATA_BYTE,0x00);				// length of menu text
			$this->xls_bof->append(XLSDATA_BYTE,0x00);				// length of description text
			$this->xls_bof->append(XLSDATA_BYTE,0x00);				// length of help topic text
			$this->xls_bof->append(XLSDATA_BYTE,0x00);				// length of status bar text
			$this->xls_bof->append(XLSDATA_BYTE,0x0c);				// ?
			$this->xls_bof->append(XLSDATA_BYTE,0x17);				// ?
			$this->xls_bof->append(XLSDATA_STRING,$sheetdata["sheetname"]);
			$this->xls_bof->save($this->xlsfilehandle);
			
			$this->xls_bof->clear(BIFF_DEFINEDNAME);
			$this->xls_bof->append(XLSDATA_SHORT,0x0020);			// built-in-name
			$this->xls_bof->append(XLSDATA_BYTE,0x00);				// keyboard shortcut
			$this->xls_bof->append(XLSDATA_BYTE,0x01);
			$this->xls_bof->append(XLSDATA_SHORT,0x0015);
			$this->xls_bof->append(XLSDATA_SHORT,$key+1);
			$this->xls_bof->append(XLSDATA_SHORT,$key+1);
			$this->xls_bof->append(XLSDATA_BYTE,0x00);				// length of menu text
			$this->xls_bof->append(XLSDATA_BYTE,0x00);				// length of description text
			$this->xls_bof->append(XLSDATA_BYTE,0x00);				// length of help topic text
			$this->xls_bof->append(XLSDATA_BYTE,0x00);				// length of status bar text
			$this->xls_bof->append(XLSDATA_BYTE,0x06);				// ?
			$this->xls_bof->append(XLSDATA_BYTE,0x3b);				// ?
			$this->xls_bof->append(XLSDATA_SHORT,0xffff-$key);				// ?
			$this->xls_bof->append(XLSDATA_SHORT,0x0000);				// ?
			$this->xls_bof->append(XLSDATA_SHORT,0x0000);				// ?
			$this->xls_bof->append(XLSDATA_SHORT,0x0000);				// ?
			$this->xls_bof->append(XLSDATA_SHORT,0x0000);				// ?
			$this->xls_bof->append(XLSDATA_SHORT,$key);
			$this->xls_bof->append(XLSDATA_SHORT,$key);
			$this->xls_bof->append(XLSDATA_LONG,0xffff0000);				// ?
			$this->xls_bof->append(XLSDATA_SHORT,0xff00);				// ?
			
			$this->xls_bof->save($this->xlsfilehandle);
		}
		
		$this->xls_bof->clear(BIFF_WINDOWPROTECT);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_PROTECT);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_PASSWORD);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_WINDOW1);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);					// Horizontal position of the document window
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);					// Vertical position of the document window
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);					// Width of the document window
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);					// Height of the document window
		$this->xls_bof->append(XLSDATA_SHORT,0x0038);					// Horz/vert scrollbars + Worksheet tab bar visible
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);					// active tab bar
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);					// Index of first visible tab in the worksheet tab bar
		$this->xls_bof->append(XLSDATA_SHORT,0x0001);					// Number of selected worksheets
		$this->xls_bof->append(XLSDATA_SHORT,0x0258);					// Width of worksheet tab bar (in 1/1000 of window width). 
																		// The remaining space is used by the horizontal scrollbar.
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_BACKUP);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);					// =1 if Excel should save a backup version of the file
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_OBJECTDISPLAYOPTIONS);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);					// show all
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_DATEMODE);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);					// 0 = Base date is 1899-Dec-31 (the cell value 1 represents 1900-Jan-01)
																		// 1 = Base date is 1904-Jan-01 (the cell value 1 represents 1904-Jan-02)
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_PRECISION);
		$this->xls_bof->append(XLSDATA_SHORT,0x0001);					// 0 = Use displayed values; 1 = Use real cell values
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_REFRESHALL);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);					// =1 then Refresh All should be done on all external data ranges and PivotTables when loading the workbook (the default is =0)
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_BOOKBOOL);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->font->save($this->xlsfilehandle,$this->xls_bof);
		
		$this->xls_workbookformat();
		
		$this->xf->save($this->xlsfilehandle,$this->xls_bof);
		//$this->xls_bof->workbookxfrecords($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_STYLEINFORMATION);
		$this->xls_bof->append(XLSDATA_SHORT,0x8010);
		$this->xls_bof->append(XLSDATA_BYTE,0x03);
		$this->xls_bof->append(XLSDATA_BYTE,0xff);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_STYLEINFORMATION);
		$this->xls_bof->append(XLSDATA_SHORT,0x8011);
		$this->xls_bof->append(XLSDATA_BYTE,0x06);
		$this->xls_bof->append(XLSDATA_BYTE,0xff);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_STYLEINFORMATION);
		$this->xls_bof->append(XLSDATA_SHORT,0x8012);
		$this->xls_bof->append(XLSDATA_BYTE,0x04);
		$this->xls_bof->append(XLSDATA_BYTE,0xff);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_STYLEINFORMATION);
		$this->xls_bof->append(XLSDATA_SHORT,0x8013);
		$this->xls_bof->append(XLSDATA_BYTE,0x07);
		$this->xls_bof->append(XLSDATA_BYTE,0xff);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_STYLEINFORMATION);
		$this->xls_bof->append(XLSDATA_SHORT,0x8000);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);
		$this->xls_bof->append(XLSDATA_BYTE,0xff);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_STYLEINFORMATION);
		$this->xls_bof->append(XLSDATA_SHORT,0x8014);
		$this->xls_bof->append(XLSDATA_BYTE,0x05);
		$this->xls_bof->append(XLSDATA_BYTE,0xff);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->palette->save($this->xlsfilehandle,$this->xls_bof);
		
		$this->sheetinformationpos = ftell($this->xlsfilehandle);
		$this->xls_writesheetinformationlist();
		
		$this->xls_bof->clear(XLS_BIFF_EOF);
		$this->xls_bof->save($this->xlsfilehandle);
		
		foreach ($this->worksheets as $sheetkey => $data) {
			$this->xls_writesheetdata($sheetkey);
		}
		$this->xlsstreamsize = ftell($this->xlsfilehandle)-0x0200;
		
		if ($this->xlsstreamsize>=0x1000) {
			$fsize = ftell($this->xlsfilehandle) & 0x01ff;
			$reqbytes = 0x0200 - $fsize;
		}
		else {
			$fsize = ftell($this->xlsfilehandle) & 0x003f;
			$reqbytes = 0x0040 - $fsize;
		}
		if ($reqbytes>0) {
			$s = str_repeat(chr(0),$reqbytes);
			fwrite($this->xlsfilehandle,$s,$reqbytes);
		}
		
		$this->rootstorageoffset=ftell($this->xlsfilehandle);
		$this->xls_writerootstorage();
		
		if ((ftell($this->xlsfilehandle) & 0x01ff)!=0) {
			$fsize = ftell($this->xlsfilehandle) & 0x01ff;
			$reqbytes = 0x0200 - $fsize;
			if ($reqbytes>0) {
				$s = str_repeat(chr(0),$reqbytes);
				fwrite($this->xlsfilehandle,$s,$reqbytes);
			}
		}
		
		if ($this->xlsstreamsize>=0x1000) {
			$rootsecid = $this->rootstorageoffset;
			$rootsecid = $rootsecid-0x200;
			$rootsecid = $rootsecid>>9;
			$rootminisecid = 0;
		}
		else { 
			$rootsecid = 0; 
			$rootminisecid = ($this->rootstorageoffset-0x0200) >> 6;
		};
		$this->xls_writeminifat();
		$dirsecid = ftell($this->xlsfilehandle);
		$dirsecid = $dirsecid-0x0200;
		$dirsecid = $dirsecid>>9;
		$this->xls_writedirentry("Root Entry",5,1,0xffffffff,$rootsecid,(($this->xlsstreamsize<0x1000) ? $this->rootstorageoffset-0x0100 : 0x0100));
		$this->xls_writedirentry("Book",2,1,3,0,$this->xlsstreamsize);
		$this->xls_writedirentry(chr(5)."DocumentSummaryInformation",2,1,0xffffffff,$rootminisecid,0x48);
		$this->xls_writedirentry(chr(5)."SummaryInformation",2,1,2,$rootminisecid+2,0x48);
		
		$fatsecid = ftell($this->xlsfilehandle);
		$fatsecid = $fatsecid-0x0200;
		$fatsecid = $fatsecid>>9;
		fseek($this->xlsfilehandle,FILE_HEADER_DIRECTORYSTART,SEEK_SET);
		fwrite($this->xlsfilehandle,pack("V",$dirsecid),4);
		fseek($this->xlsfilehandle,0,SEEK_END);
		
		$fat = new fat_class($this->xlsfilehandle, $this->xlsstreamsize, $this->rootstorageoffset);
		fseek($this->xlsfilehandle,FILE_HEADER_NUMBEROFFATSECTORS,SEEK_SET);
		$fatsectorcount = $fat->fatsectorcount;
		fwrite($this->xlsfilehandle,pack("V",$fatsectorcount),4);
		
		fseek($this->xlsfilehandle,FILE_HEADER_FIRSTFATENTRY,SEEK_SET);
		while ($fatsectorcount>0) {
			fwrite($this->xlsfilehandle,pack("V",$fatsecid),4);
			$fatsecid++;
			$fatsectorcount--;
		}
		
		fclose($this->xlsfilehandle);
	}
	
	private function file_headerinit() {
		// signature
		$this->file_header  = pack("c*",0xd0, 0xcf, 0x11, 0xe0, 0xa1, 0xb1, 0x1a, 0xe1);
		// clsid
		$this->file_header .= pack("c*",0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00);
		// minor.ver
		$this->file_header .= pack("v",0x003e);
		// major.ver
		$this->file_header .= pack("v",0x0003);
		// byteorder
		$this->file_header .= pack("v",0xfffe);
		// sectorshift
		$this->file_header .= pack("v",0x0009);
		// minisectorsh
		$this->file_header .= pack("v",0x0006);
		// reserved
		$this->file_header .= pack("vvvvv",0x0000,0x0000,0x0000,0x0000,0x0000);
		// fatsectors
		$this->file_header .= pack("V",0x00000001);
		// dirsectors
		$this->file_header .= pack("V",0x00000000);
		// signature
		$this->file_header .= pack("V",0x00000000);
		// ministrm.size
		$this->file_header .= pack("V",0x00001000);
		// first minifat sector
		$this->file_header .= pack("V",0x00000000);
		// minifat sectors
		$this->file_header .= pack("V",0x00000001);
		// dif sectors start
		$this->file_header .= pack("V",0xfffffffe);
		// dif sectors count
		$this->file_header .= pack("V",0x00000000);
		// first109fat
		$this->file_header .= str_repeat(chr(0xff),436);
		
		fwrite($this->xlsfilehandle, $this->file_header);
	}
	
	function xls_strpad($astr,$asize) {
		$strlength = strlen($astr);
		$output = chr($strlength).$astr;
		while (strlen($output)!=$asize) { $output .= " "; }
		return $output;
	}
	
	private function xls_workbookformat() {
		$s = pack("C*",0x1e,0x04,0x14,0x00,0x05,0x00,0x11,0x24,0x23,0x2c,0x23,0x23,0x30);
		$s.= pack("C*",0x5f,0x29,0x3b,0x28,0x24,0x23,0x2c,0x23,0x23,0x30,0x29,0x1e,0x04,0x19,0x00,0x06);
		$s.= pack("C*",0x00,0x16,0x24,0x23,0x2c,0x23,0x23,0x30,0x5f,0x29,0x3b,0x5b,0x52,0x65,0x64,0x5d);
		$s.= pack("C*",0x28,0x24,0x23,0x2c,0x23,0x23,0x30,0x29,0x1e,0x04,0x1a,0x00,0x07,0x00,0x17,0x24);
		$s.= pack("C*",0x23,0x2c,0x23,0x23,0x30,0x2e,0x30,0x30,0x5f,0x29,0x3b,0x28,0x24,0x23,0x2c,0x23);
		$s.= pack("C*",0x23,0x30,0x2e,0x30,0x30,0x29,0x1e,0x04,0x1f,0x00,0x08,0x00,0x1c,0x24,0x23,0x2c);
		$s.= pack("C*",0x23,0x23,0x30,0x2e,0x30,0x30,0x5f,0x29,0x3b,0x5b,0x52,0x65,0x64,0x5d,0x28,0x24);
		$s.= pack("C*",0x23,0x2c,0x23,0x23,0x30,0x2e,0x30,0x30,0x29,0x1e,0x04,0x2d,0x00,0x2a,0x00,0x2a);
		$s.= pack("C*",0x5f,0x28,0x24,0x2a,0x20,0x23,0x2c,0x23,0x23,0x30,0x5f,0x29,0x3b,0x5f,0x28,0x24);
		$s.= pack("C*",0x2a,0x20,0x28,0x23,0x2c,0x23,0x23,0x30,0x29,0x3b,0x5f,0x28,0x24,0x2a,0x20,0x22);
		$s.= pack("C*",0x2d,0x22,0x5f,0x29,0x3b,0x5f,0x28,0x40,0x5f,0x29,0x1e,0x04,0x2a,0x00,0x29,0x00);
		$s.= pack("C*",0x27,0x5f,0x28,0x2a,0x20,0x23,0x2c,0x23,0x23,0x30,0x5f,0x29,0x3b,0x5f,0x28,0x2a);
		$s.= pack("C*",0x20,0x28,0x23,0x2c,0x23,0x23,0x30,0x29,0x3b,0x5f,0x28,0x2a,0x20,0x22,0x2d,0x22);
		$s.= pack("C*",0x5f,0x29,0x3b,0x5f,0x28,0x40,0x5f,0x29,0x1e,0x04,0x35,0x00,0x2c,0x00,0x32,0x5f);
		$s.= pack("C*",0x28,0x24,0x2a,0x20,0x23,0x2c,0x23,0x23,0x30,0x2e,0x30,0x30,0x5f,0x29,0x3b,0x5f);
		$s.= pack("C*",0x28,0x24,0x2a,0x20,0x28,0x23,0x2c,0x23,0x23,0x30,0x2e,0x30,0x30,0x29,0x3b,0x5f);
		$s.= pack("C*",0x28,0x24,0x2a,0x20,0x22,0x2d,0x22,0x3f,0x3f,0x5f,0x29,0x3b,0x5f,0x28,0x40,0x5f);
		$s.= pack("C*",0x29,0x1e,0x04,0x32,0x00,0x2b,0x00,0x2f,0x5f,0x28,0x2a,0x20,0x23,0x2c,0x23,0x23);
		$s.= pack("C*",0x30,0x2e,0x30,0x30,0x5f,0x29,0x3b,0x5f,0x28,0x2a,0x20,0x28,0x23,0x2c,0x23,0x23);
		$s.= pack("C*",0x30,0x2e,0x30,0x30,0x29,0x3b,0x5f,0x28,0x2a,0x20,0x22,0x2d,0x22,0x3f,0x3f,0x5f);
		$s.= pack("C*",0x29,0x3b,0x5f,0x28,0x40,0x5f,0x29);
		fwrite($this->xlsfilehandle,$s);
	}
	
	private function xls_writesheetinformationlist() {
		foreach ($this->worksheets as $key => $data) {
			$this->xls_writeonesheetinformation($key);
		}
	}
	
	private function xls_writeonesheetinformation($sheetid) {
		$fpos = ftell($this->xlsfilehandle);
		if ($this->worksheets[$sheetid]["sheetinfopos"]!=-1) {
			fseek($this->xlsfilehandle,$this->worksheets[$sheetid]["sheetinfopos"],SEEK_SET);
		}
		$this->xls_bof->clear(BIFF_BOUNDSHEET);
		$this->xls_bof->append(XLSDATA_LONG,$this->worksheets[$sheetid]["fileoffset"]);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->append(XLSDATA_STRING,$this->worksheets[$sheetid]["sheetname"]);
		$this->xls_bof->save($this->xlsfilehandle);
		if ($this->worksheets[$sheetid]["sheetinfopos"]!=-1) {
			fseek($this->xlsfilehandle,$fpos,SEEK_SET);
		}
		else { $this->worksheets[$sheetid]["sheetinfopos"]=$fpos; }
	}
	
	private function xls_writeexterninfo() {
		$this->xls_bof->clear(BIFF_EXTERNALREFERENCESCOUNT);
		$this->xls_bof->append(XLSDATA_SHORT,count($this->worksheets)+2);			// = sheetcount+2 
		$this->xls_bof->save($this->xlsfilehandle);
		
		foreach ($this->worksheets as $key => $sheetdata) {
			$this->xls_bof->clear(BIFF_EXTERNSHEET);
			$this->xls_bof->append(XLSDATA_BYTE,strlen($sheetdata["sheetname"]));
			$this->xls_bof->append(XLSDATA_BYTE,3);
			$this->xls_bof->append(XLSDATA_STRING1,$sheetdata["sheetname"]);
			$this->xls_bof->save($this->xlsfilehandle);
		}
		
		$this->xls_bof->clear(BIFF_EXTERNSHEET);
		$this->xls_bof->append(XLSDATA_BYTE,0x01);
		$this->xls_bof->append(XLSDATA_BYTE,0x3a);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_EXTERNSHEET);
		$this->xls_bof->append(XLSDATA_BYTE,0x01);
		$this->xls_bof->append(XLSDATA_BYTE,0x04);
		$this->xls_bof->save($this->xlsfilehandle);
	}
	
	public function xls_picturecallback() {
		die("done.");
	}
	
	private function xls_writesheetdata($sheetid) {
		if (defined("DEBUG")) {
			echo "writesheet : $sheetid<br>";
		}
		$rowrecord_filepos = array();
		$rowpos = array();
		
		$this->worksheets[$sheetid]["fileoffset"] = ftell($this->xlsfilehandle)-0x0200;
		$this->xls_writeonesheetinformation($sheetid);
		$this->xls_bof->clear(XLS_BIFF5);
		$this->xls_bof->append(XLSDATA_SHORT,0x0500);
		$this->xls_bof->append(XLSDATA_SHORT,WORKBOOK_SHEET);
		$this->xls_bof->append(XLSDATA_SHORT,0x096c);					// build identifier
		$this->xls_bof->append(XLSDATA_SHORT,0x07c9);					// build year = 1993
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->worksheets[$sheetid]["rowrecordsoffset"] = ftell($this->xlsfilehandle)+2+2+4+2+2+4;
		
		$rowcount = $this->worksheets[$sheetid]["last_row"]-$this->worksheets[$sheetid]["first_row"];
		$dbcellcount = ($rowcount >> 5)+1;
		$this->xls_bof->clear(BIFF_INDEX);
		$this->xls_bof->append(XLSDATA_LONG,0x00000000);				// unused var.
		$this->xls_bof->append(XLSDATA_SHORT,$this->worksheets[$sheetid]["first_row"]);
		$this->xls_bof->append(XLSDATA_SHORT,$this->worksheets[$sheetid]["last_row"]+1);
		$this->xls_bof->append(XLSDATA_LONG,0x00000000);
		
		$i=0;
		while ($i!=$dbcellcount) {
			$this->xls_bof->append(XLSDATA_LONG,0xffffffff);
			$rowrecord_filepos[$i]=ftell($this->xlsfilehandle)+16+($i<<2);
			$i++;
		}
		
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_CALCMODE);
		$this->xls_bof->append(XLSDATA_SHORT,0x0001);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_CALCCOUNT);
		$this->xls_bof->append(XLSDATA_SHORT,0x0064);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_REFMODE);
		$this->xls_bof->append(XLSDATA_SHORT,0x0001);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_ITERATION);
		$this->xls_bof->append(XLSDATA_SHORT,0x0001);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_DELTA);
		$this->xls_bof->append(XLSDATA_BYTE,0xfc);
		$this->xls_bof->append(XLSDATA_BYTE,0xa9);
		$this->xls_bof->append(XLSDATA_BYTE,0xf1);
		$this->xls_bof->append(XLSDATA_BYTE,0xd2);
		$this->xls_bof->append(XLSDATA_BYTE,0x4d);
		$this->xls_bof->append(XLSDATA_BYTE,0x62);
		$this->xls_bof->append(XLSDATA_BYTE,0x50);
		$this->xls_bof->append(XLSDATA_BYTE,0x3f);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_SAVERECALC);
		$this->xls_bof->append(XLSDATA_SHORT,0x0001);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_PRINTHEADERS);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_PRINTGRIDLINES);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_GRIDSET);
		$this->xls_bof->append(XLSDATA_SHORT,0x0001);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_GUTS);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_DEFAULTROWHEIGHT);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->append(XLSDATA_SHORT,0x012c);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_COUNTRY);
		$this->xls_bof->append(XLSDATA_SHORT,0x0001);	// Windows country identifier of the user interface language of Excel
		$this->xls_bof->append(XLSDATA_SHORT,0x0001);	// Windows country identifier of the system regional settings
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_SHEETPR);
		$this->xls_bof->append(XLSDATA_SHORT,0x04c1);	/*	0 0001H 0 = Do not show automatic page breaks 
																	1 = Show automatic page breaks
															4 0010H 0 = Standard sheet 
																	1 = Dialogue sheet (BIFF5-BIFF8)
															5 0020H 0 = No automatic styles in outlines 
																	1 = Apply automatic styles to outlines
															6 0040H 0 = Outline buttons above outline group 
																	1 = Outline buttons below outline group
															7 0080H 0 = Outline buttons left of outline group 
																	1 = Outline buttons right of outline group
															8 0100H 0 = Scale printout in percent
																	1 = Fit printout to number of pages
															9 0200H 0 = Save external linked values (BIFF3-BIFF4 only)
																	1 = Do not save external linked values (BIFF3-BIFF4 only)
															10 0400H 0 = Do not show row outline symbols 1 = Show row outline symbols
															11 0800H 0 = Do not show column outline symbols 
																	1 = Show column outline symbols
															13-12 3000H These flags specify the arrangement of windows. They are stored in BIFF4 only.
																002 = Arrange windows tiled
																012 = Arrange windows horizontal
																102 = Arrange windows vertical
																112 = Arrange windows cascaded
															The following flags are valid for BIFF4-BIFF8 only:
															14 4000H 0 = Excel like expression evaluation 
																	1 = Lotus like expression evaluation
															15 8000H 0 = Excel like formula editing 
																	1 = Lotus like formula editing
														*/		
		$this->xls_bof->save($this->xlsfilehandle);
		
		/*
		$this->xls_bof->clear(BIFF_PAGEHEADER);
		$this->xls_bof->append(XLSDATA_LSTRING,$this->page_header);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_PAGEFOOTER);
		$this->xls_bof->append(XLSDATA_LSTRING,$this->page_footer);
		$this->xls_bof->save($this->xlsfilehandle);
		*/
		/*
		$this->xls_bof->clear(BIFF_PAGEHEADER);
		$this->xls_bof->append(XLSDATA_LSTRING,"");
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_PAGEFOOTER);
		$this->xls_bof->append(XLSDATA_LSTRING,"");
		$this->xls_bof->save($this->xlsfilehandle);
		*/
		$this->xls_bof->clear(BIFF_HCENTER);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_VCENTER);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_LEFTMARGIN);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);
		$this->xls_bof->append(XLSDATA_BYTE,0xf0);
		$this->xls_bof->append(XLSDATA_BYTE,0x3f);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_RIGHTMARGIN);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);
		$this->xls_bof->append(XLSDATA_BYTE,0xf0);
		$this->xls_bof->append(XLSDATA_BYTE,0x3f);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_TOPMARGIN);
		$this->xls_bof->append(XLSDATA_BYTE,0xab);
		$this->xls_bof->append(XLSDATA_BYTE,0xaa);
		$this->xls_bof->append(XLSDATA_BYTE,0xaa);
		$this->xls_bof->append(XLSDATA_BYTE,0xaa);
		$this->xls_bof->append(XLSDATA_BYTE,0xaa);
		$this->xls_bof->append(XLSDATA_BYTE,0xaa);
		$this->xls_bof->append(XLSDATA_BYTE,0xfa);
		$this->xls_bof->append(XLSDATA_BYTE,0x3f);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_BOTTOMMARGIN);
		$this->xls_bof->append(XLSDATA_BYTE,0xab);
		$this->xls_bof->append(XLSDATA_BYTE,0xaa);
		$this->xls_bof->append(XLSDATA_BYTE,0xaa);
		$this->xls_bof->append(XLSDATA_BYTE,0xaa);
		$this->xls_bof->append(XLSDATA_BYTE,0xaa);
		$this->xls_bof->append(XLSDATA_BYTE,0xaa);
		$this->xls_bof->append(XLSDATA_BYTE,0xfa);
		$this->xls_bof->append(XLSDATA_BYTE,0x3f);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_PAGESETUP);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);	/* Paper size
															0 Undefined 
															1 Letter 81/2½ × 11½ 
															2 Letter small 81/2½ × 11½ 
															3 Tabloid 11½ × 17½ 
															4 Ledger 17½ × 11½ 
															5 Legal 81/2½ × 14½ 
															6 Statement 51/2½ × 81/2½ 
															7 Executive 71/4½ × 101/2½ 
															8 A3 297mm × 420mm 
															9 A4 210mm × 297mm 
															10 A4 small 210mm × 297mm 
															11 A5 148mm × 210mm 
															12 B4 (JIS) 257mm × 364mm 
															13 B5 (JIS) 182mm × 257mm 
															14 Folio 81/2½ × 13½ 
															15 Quarto 215mm × 275mm 
															16 10×14 10½ × 14½ 
															17 11×17 11½ × 17½ 
															18 Note 81/2½ × 11½ 
															19 Envelope #9 37/8½ × 87/8½ 
															20 Envelope #10 41/8½ × 91/2½ 
															21 Envelope #11 41/2½ × 103/8½ 
															22 Envelope #12 43/4½ × 11½ 
															23 Envelope #14 5½ × 111/2½ 
															24 C 17½ × 22½ 
															25 D 22½ × 34½ 
															26 E 34½ × 44½ 
															27 Envelope DL 110mm × 220mm 
															28 Envelope C5 162mm × 229mm 
															29 Envelope C3 324mm × 458mm 
															30 Envelope C4 229mm × 324mm 
															31 Envelope C6 114mm × 162mm 
															32 Envelope C6/C5 114mm × 229mm 
															33 B4 (ISO) 250mm × 353mm 
															34 B5 (ISO) 176mm × 250mm 
															35 B6 (ISO) 125mm × 176mm 
															36 Envelope Italy 110mm × 230mm 
															37 Envelope Monarch 37/8½ × 71/2½ 
															38 63/4 Envelope 35/8½ × 61/2½ 
															39 US Standard Fanfold 147/8½ × 11½ 
															40 German Std. Fanfold 81/2½ × 12½ 
															41 German Legal Fanfold 81/2½ × 13½ 
															42 B4 (ISO) 250mm × 353mm 
															43 Japanese Postcard 100mm × 148mm
															44 9×11 9½ × 11½
															45 10×11 10½ × 11½
															46 15×11 15½ × 11½
															47 Envelope Invite 220mm × 220mm
															48 Undefined
															49 Undefined
															50 Letter Extra 91/2½ × 12½
															51 Legal Extra 91/2½ × 15½
															52 Tabloid Extra 1111/16½ × 18½
															53 A4 Extra 235mm × 322mm
															54 Letter Transverse 81/2½ × 11½
															55 A4 Transverse 210mm × 297mm
															56 Letter Extra Transv. 91/2½ × 12½
															57 Super A/A4 227mm × 356mm
															58 Super B/A3 305mm × 487mm
															59 Letter Plus 812½ × 1211/16½
															60 A4 Plus 210mm × 330mm
															61 A5 Transverse 148mm × 210mm
															62 B5 (JIS) Transverse 182mm × 257mm
															63 A3 Extra 322mm × 445mm
															64 A5 Extra 174mm × 235mm
															65 B5 (ISO) Extra 201mm × 276mm
															66 A2 420mm × 594mm
															67 A3 Transverse 297mm × 420mm
															68 A3 Extra Transverse 322mm × 445mm
															69 Dbl. Japanese Postcard 200mm × 148mm
															70 A6 105mm × 148mm
															71
															72
															73
															74
															75 Letter Rotated 11½ × 81/2½
															76 A3 Rotated 420mm × 297mm
															77 A4 Rotated 297mm × 210mm
															78 A5 Rotated 210mm × 148mm
															79 B4 (JIS) Rotated 364mm × 257mm
															80 B5 (JIS) Rotated 257mm × 182mm
															81 Japanese Postcard Rot. 148mm × 100mm
															82 Dbl. Jap. Postcard Rot. 148mm × 200mm
															83 A6 Rotated 148mm × 105mm
															84
															85
															86
															87
															88 B6 (JIS) 128mm × 182mm
															89 B6 (JIS) Rotated 182mm × 128mm
															90 12×11 12½ × 11½
														*/
		$this->xls_bof->append(XLSDATA_SHORT,0x0024);	// scaling factor in percent
		$this->xls_bof->append(XLSDATA_SHORT,0x0001);	// start page number
		$this->xls_bof->append(XLSDATA_SHORT,0x0001);	// Fit worksheet width to this number of pages (0 = use as many as needed)
		$this->xls_bof->append(XLSDATA_SHORT,0x0001);	// Fit worksheet height to this number of pages (0 = use as many as needed)
		$this->xls_bof->append(XLSDATA_SHORT,0x0146);	/* Print options
														   bit mask
															0 0001H 0 = Print pages in columns 
																	1 = Print pages in rows
															1 0002H 0 = Landscape 
																	1 = Portrait
															2 0004H 1 = Paper size, scaling factor, paper orientation (portrait/landscape),print resolution and number of copies are not initialised
															3 0008H 0 = Print coloured 
																	1 = Print black and white
															4 0010H 0 = Default print quality 
																	1 = Draft quality
															5 0020H 0 = Do not print cell notes 
																	1 = Print cell notes
															6 0040H 0 = Use paper orientation (portrait/landscape) flag above1 
																	1 = Use default paper orientation(landscape for chart sheets,portrait otherwise)
															7 0080H 0 = Automatic page numbers 
																	1 = Use start page number above The following flags are valid for BIFF8 only:
															9 0200H 0 = Print notes as displayed 
																	1 = Print notes at end of sheet
															11-10 0C00H 002 = Print errors as displayed
																		012 = Do not print errors
																		102 = Print errors as ?--?
																		112 = Print errors as ?#N/A?
														*/
		$this->xls_bof->append(XLSDATA_SHORT,0x0033);	// Print resolution in dpi 
		$this->xls_bof->append(XLSDATA_SHORT,0xff00);	// Vertical print resolution in dpi
		$this->xls_bof->append(XLSDATA_LONG,0x33333333);	// Header margin
		$this->xls_bof->append(XLSDATA_LONG,0x3fd33333);	// Header margin
		$this->xls_bof->append(XLSDATA_LONG,0x33333333);	// Footer margin
		$this->xls_bof->append(XLSDATA_LONG,0x3fd33333);	// Footer margin
		$this->xls_bof->append(XLSDATA_SHORT,0x00ff);		// Number of copies to print
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_writeexterninfo();
		
		$this->xls_bof->clear(BIFF_DEFCOLWIDTH);
		$this->xls_bof->append(XLSDATA_SHORT,0x0008);
		$this->xls_bof->save($this->xlsfilehandle);
		
		$i=-1;
		while ($i<$this->worksheets[$sheetid]["last_col"]) {
			$i++;
			if (isset($this->worksheets[$sheetid]["colwidths"][$i])) {
				$this->xls_bof->clear(BIFF_COLINFO);
				$this->xls_bof->append(XLSDATA_SHORT,$i);			// Index to first column in the range
				$this->xls_bof->append(XLSDATA_SHORT,$i);			// Index to last column in the range
				$this->xls_bof->append(XLSDATA_SHORT,$this->worksheets[$sheetid]["colwidths"][$i]);		// Width of the columns in 1/256 of the width of the zero character, using default font (first FONT record in the file)
				$this->xls_bof->append(XLSDATA_SHORT,$this->xf->defaultxf);
				//$this->xls_bof->append(XLSDATA_SHORT,(($i<=$this->worksheets[$sheetid]["first_col"]) ? 0x0002 : 0x0000));
				$this->xls_bof->append(XLSDATA_SHORT,0x0002);
																	/*	Offset Bits Mask Option Name Contents
																		0		0	 01h fHidden      =1 if the column range is hidden
																				7-1	 FEh (unused)
																		1		2-0	 07h iOutLevel	  Outline level of column range
																				3	 08h (Reserved)	  Reserved; must be 0 (zero)
																				4  10h fCollapsed   =1 if the column range is collapsed in outlining
																				7-5  E0h (Reserved)   Reserved; must be 0 (zero)
																	*/
				$this->xls_bof->append(XLSDATA_SHORT,0x0000);		// Not used
				$this->xls_bof->save($this->xlsfilehandle);
			}
		}
		
		$this->xls_bof->clear(BIFF_DIMENSIONS);
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);										// first row
		$this->xls_bof->append(XLSDATA_SHORT,$this->worksheets[$sheetid]["last_row"]+1);	// last used row + 1
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);										// first col
		$this->xls_bof->append(XLSDATA_SHORT,$this->worksheets[$sheetid]["last_col"]+1);	// last used col + 1
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);										// not used
		$this->xls_bof->save($this->xlsfilehandle);
		
		$rowrecord_count = 0;
		$i=0;
		$sheetdata = &$this->xlsdocument[$this->worksheets[$sheetid]["sheetname"]]["document"];
		if (count($sheetdata)>1) {
			ksort ($sheetdata,SORT_NUMERIC);
		}
		
		while ($i<$this->worksheets[$sheetid]["last_row"]+1) {
			$j=0;
			$rowrecord_start = ftell($this->xlsfilehandle);
			while (($j!=32) && ($i<$this->worksheets[$sheetid]["last_row"]+1)) {
				if (isset($sheetdata[$i])) {
					if (@isset($this->worksheets[$sheetid]["rowheights"][$i])) {
						$this->xls_bof->clear(BIFF_ROW);
						$this->xls_bof->append(XLSDATA_SHORT,$i);											// row no.
						$this->xls_bof->append(XLSDATA_SHORT,0x0000);										// first defined column
						$this->xls_bof->append(XLSDATA_SHORT,$this->worksheets[$sheetid]["last_col"]+1);	// last defined column +1
						$this->xls_bof->append(XLSDATA_SHORT,$this->worksheets[$sheetid]["rowheights"][$i]);		// row height
						$this->xls_bof->append(XLSDATA_SHORT,0x0000);										// Used by Excel to optimize loading the file; if you are creating a BIFF file, set irwMac to 0.
						$this->xls_bof->append(XLSDATA_SHORT,0x0000);										// reserved
						$this->xls_bof->append(XLSDATA_BYTE,($this->worksheets[$sheetid]["rowheights"][$i]==0xff ? 0 : 0x40));			// see options @ offset 0
						$this->xls_bof->append(XLSDATA_BYTE,0x01);			/* options
																			Offset	Bits	Mask	Name		Contents
																			0		2?0		07h		iOutLevel	Outline level of the row
																					3		08h		(Reserved)
																					4		10h		fCollapsed	=1 if the row is collapsed in outlining
																					5		20h		fDyZero		=1 if the row height is set to 0 (zero)
																					6		40h		fUnsynced	=1 if the font height and row height are not compatible
																					7		80h		fGhostDirty	=1 if the row has been formatted, even if it contains all blank cells
																			1		7?0		FFh		(Reserved) ????
																			*/
						$this->xls_bof->append(XLSDATA_SHORT,0x000f);		/*	If fGhostDirty=1 (see grbit field), this is the index to the XF record for the row.
																				Otherwise, this field is undefined. 
																				Note: ixfe uses only the low-order 12 bits of the field (bits 11?0). 
																				Bit 12 is fExAsc, bit 13 is fExDsc, and bits 14 and 15 are reserved. 
																				fExAsc and fExDsc are set to true if the row has a thick border on top or on bottom, 
																				respectively.
																			*/
						$this->xls_bof->save($this->xlsfilehandle);
					}
				}
				$i++;
				$j++;
			}
			$i=$i-$j;
			$j=0;
			$rowpos = array();
			while (($j!=32) && ($i<$this->worksheets[$sheetid]["last_row"]+1)) {
				if (isset($sheetdata[$i])) {
					$rowpos[]=ftell($this->xlsfilehandle);
					$rowdata = &$sheetdata[$i];
					if (count($rowdata)>1) {
						ksort($rowdata,SORT_NUMERIC);
					}
					foreach ($rowdata as $cellid => $celldata) {
						switch ($celldata["type"]) {
							case CELL_STRING :	
												$this->xls_bof->clear(BIFF_LABEL);
												$this->xls_bof->append(XLSDATA_SHORT,$i);						// rowid.
												$this->xls_bof->append(XLSDATA_SHORT,$cellid);					// cellid
												$this->xls_bof->append(XLSDATA_SHORT,$celldata["xfindex"]);		// XF index.
												$this->xls_bof->append(XLSDATA_LSTRING,$celldata["data"]);
												$this->xls_bof->save($this->xlsfilehandle);
												break;
							case CELL_FLOAT :
												$this->xls_bof->clear(BIFF_NUMBER);
												$this->xls_bof->append(XLSDATA_SHORT,$i);						// rowid.
												$this->xls_bof->append(XLSDATA_SHORT,$cellid);					// cellid
												$this->xls_bof->append(XLSDATA_SHORT,$celldata["xfindex"]);		// XF index.
												$this->xls_bof->append(XLSDATA_FLOAT,floatval($celldata["data"]));
												$this->xls_bof->save($this->xlsfilehandle);
												break;
							
							case CELL_PICTURE :	
												if (defined("PICTUREDEBUG")) {
													echo "picture inserted<br>\n";
												}
												$bgcolor = (isset($celldata["bgcolor"]) ? $celldata["bgcolor"] : 9);
												$fgcolor = (isset($celldata["color"]) ? $celldata["color"] : 9);
												$bgcolorrgb = $this->palette->palette_array[$bgcolor-8];
												$picture = new xls_picture($celldata["data"],$bgcolor,$fgcolor,$bgcolorrgb,$this->objectcounter,$i,$cellid);
												$picture->loaddata();
												
												$imageheight = ($picture->imageheight << 4)+$picture->imageheight;
												$imagewidth = ($picture->imagewidth << 5)+$picture->imagewidth;
												//$imageheight = intval(round($picture->imageheight*18));
												//$imagewidth = intval(round($picture->imagewidth*36));
												
												$picture->lastrow = $i;
												$picture->lastcol = $cellid;
												$rcnt = $i;
												$ok = false;
												if (defined("PICTUREDEBUG")) {
													echo "calc. space<br>\n";
													echo "start row : ".$picture->firstrow."<br>\n";
												}
												
												while (!$ok) {
													$tmprowheight = (isset($this->worksheets[$sheetid]["rowheights"][$rcnt]) ? $this->worksheets[$sheetid]["rowheights"][$rcnt] : XLSFILE_DEFAULT_ROWHEIGHT);
													if (defined("PICTUREDEBUG")) {
														echo "?last row : ".$picture->lastrow." , picture height : $imageheight , tmpheight : $tmprowheight<br>\n";
													}
													if ($tmprowheight>=$imageheight) {
														if (defined("PICTUREDEBUG")) {
															echo "height calc. finish<br>\n";
														}
														$picture->imagebottom = $imageheight;
														$ok=true;
														continue;
													}
													else {
														$picture->lastrow++;
														$imageheight -= ($tmprowheight+18);
														$rcnt++;
													}
												}
												
												if (defined("PICTUREDEBUG")) {
													echo "calc. space<br>\n";
													echo "start row : ".$picture->firstcol."<br>\n";
												}
												
												$ccnt = $cellid;
												$ok = false;
												while (!$ok) {
													$tmpcolwidth = (isset($this->worksheets[$sheetid]["colwidths"][$ccnt]) ? $this->worksheets[$sheetid]["colwidths"][$ccnt] : XLSFILE_DEFAULT_COLWIDTH);
													if (defined("PICTUREDEBUG")) {
														echo "?last row : ".$picture->lastcol." , picture width : $imagewidth , tmpwidth  : $tmpcolwidth<br>\n";
													}
													if ($tmpcolwidth>=$imagewidth) {
														if (defined("PICTUREDEBUG")) {
															echo "width calc. finish<br>\n";
														}
														$ok=true;
														$picture->imageright = $imagewidth;
													}
													else {
														$picture->lastcol++;
														$imagewidth -= ($tmpcolwidth+36);
														$ccnt++;
													}
												}
												/*
												$picture->imagebottom = intval(round($imageheight*($tmprowheight/1024)));
												$picture->imageright = intval(round($imagewidth*($tmpcolwidth/1024)));
												*/
												$picture->imagebottom = $imageheight / $tmprowheight * 256;
												$picture->imageright = $imagewidth / $tmpcolwidth * 1024;
												
												$picture->save($this->xlsfilehandle, $this->xls_bof);
												unset($picture);
												$this->objectcounter++;
												break;
												
							default :	die("Unknown cell data type");
						}
					}
				}
				$i++;
				$j++;
			}
			$filepos_backup = ftell($this->xlsfilehandle);
			fseek($this->xlsfilehandle,$rowrecord_filepos[$rowrecord_count],SEEK_SET);
			$s = pack("V",$filepos_backup-0x0200);
			fwrite($this->xlsfilehandle,$s);
			fseek($this->xlsfilehandle,$filepos_backup,SEEK_SET);
			
			
			$this->xls_bof->clear(BIFF_DBCELL);
			$this->xls_bof->append(XLSDATA_LONG,$filepos_backup-$rowrecord_start);
			$this->xls_bof->append(XLSDATA_SHORT,($rowpos[0]-$rowrecord_start)-0x14);
			foreach ($rowpos as $key => $data) {
				if ($key==count($rowpos)-1) {
					break;
				}
				$offset = $rowpos[0]-$data;
				$this->xls_bof->append(XLSDATA_SHORT,$offset);
			}
			$this->xls_bof->save($this->xlsfilehandle);
			unset($rowpos);
			$rowrecord_count++;
		}
		$this->xls_bof->clear(BIFF_WINDOW2);
		$sheetopt = ($sheetid==0 ? 0x06b6 : 0x00b6);
		$this->xls_bof->append(XLSDATA_SHORT,$sheetopt);	/* Bit Mask    Contents
																0  0001H 0 = Show formula results 1 = Show formulas
																1  0002H 0 = Do not show grid lines 1 = Show grid lines
																2  0004H 0 = Do not show sheet headers 1 = Show sheet headers
																3  0008H 0 = Panes are not frozen 1 = Panes are frozen (freeze)
																4  0010H 0 = Show zero values as empty cells 1 = Show zero values
																5  0020H 0 = Manual grid line colour 1 = Automatic grid line colour
																6  0040H 0 = Columns from left to right 1 = Columns from right to left
																7  0080H 0 = Do not show outline symbols 1 = Show outline symbols
																8  0100H 0 = Keep splits if pane freeze is removed 1 = Remove splits if pane freeze is removed
																9  0200H 0 = Sheet not selected 1 = Sheet selected (BIFF5-BIFF8)
																10  0400H 0 = Sheet not active 1 = Sheet active (BIFF5-BIFF8)
																11  0800H 0 = Show in normal view 1 = Show in page break preview (BIFF8)
															*/
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);		// Index to first visible row
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);		// Index to first visible column
		$this->xls_bof->append(XLSDATA_LONG,0x00000040);	// Grid line RGB colour
		$this->xls_bof->save($this->xlsfilehandle);
		
		$this->xls_bof->clear(BIFF_SELECTION);
		$this->xls_bof->append(XLSDATA_BYTE,0x03);
		$this->xls_bof->append(XLSDATA_SHORT,$this->worksheets[$sheetid]["first_row"]);		// Index to row of the active cell
		$this->xls_bof->append(XLSDATA_SHORT,$this->worksheets[$sheetid]["first_col"]);	// Index to column of the active cell
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);										// Index into the following cell range list to the entry that contains the active cell
		$this->xls_bof->append(XLSDATA_SHORT,0x0001);
		$this->xls_bof->append(XLSDATA_SHORT,0);		// $this->worksheets[$sheetid]["first_row"]
		$this->xls_bof->append(XLSDATA_SHORT,0);		// $this->worksheets[$sheetid]["last_row"]
		$this->xls_bof->append(XLSDATA_BYTE,0);			// $this->worksheets[$sheetid]["last_col"]
		$this->xls_bof->append(XLSDATA_BYTE,0);			// $this->worksheets[$sheetid]["last_col"]
		$this->xls_bof->save($this->xlsfilehandle);
		
		if (isset($this->xlsdocument[$this->worksheets[$sheetid]["sheetname"]]["mergedcells"])) {
			$this->xlsdocument[$this->worksheets[$sheetid]["sheetname"]]["mergedcells"]->save($this->xlsfilehandle, $this->xls_bof);
		}
		
		$this->xls_bof->clear(BIFF_SHEETPROTECTION);
		$this->xls_bof->append(XLSDATA_SHORT,BIFF_SHEETPROTECTION);
		$this->xls_bof->append(XLSDATA_BYTE,0x00);			// not used
		$this->xls_bof->append(XLSDATA_BYTE,0x00);			// not used
		$this->xls_bof->append(XLSDATA_BYTE,0x00);			// not used
		$this->xls_bof->append(XLSDATA_BYTE,0x00);			// not used
		$this->xls_bof->append(XLSDATA_BYTE,0x00);			// not used
		$this->xls_bof->append(XLSDATA_BYTE,0x00);			// not used
		$this->xls_bof->append(XLSDATA_BYTE,0x00);			// not used
		$this->xls_bof->append(XLSDATA_BYTE,0x00);			// not used
		$this->xls_bof->append(XLSDATA_BYTE,0x00);			// not used
		$this->xls_bof->append(XLSDATA_BYTE,0x00);			// not used
		
		$this->xls_bof->append(XLSDATA_BYTE,0x02);			// unknown data
		$this->xls_bof->append(XLSDATA_BYTE,0x00);			// unknown data
		$this->xls_bof->append(XLSDATA_BYTE,0x01);			// unknown data
		$this->xls_bof->append(XLSDATA_BYTE,0xff);			// unknown data
		$this->xls_bof->append(XLSDATA_BYTE,0xff);			// unknown data
		$this->xls_bof->append(XLSDATA_BYTE,0xff);			// unknown data
		$this->xls_bof->append(XLSDATA_BYTE,0xff);			// unknown data
		$this->xls_bof->append(XLSDATA_SHORT,0x4400);			// ???
		$this->xls_bof->append(XLSDATA_SHORT,0x0000);			// not used
		$this->xls_bof->save($this->xlsfilehandle);


		$this->xls_bof->clear(XLS_BIFF_EOF);
		$this->xls_bof->save($this->xlsfilehandle);
	}
	
	private function xls_writeminifat() {
		$minifat_start = ftell($this->xlsfilehandle);
		$minifat_sectorid = pack("V",($minifat_start-0x0200)/0x0200);
		if ($this->xlsstreamsize>=0x1000) {
			$s  = pack("V",0x00000001);
			$s .= pack("V",0xfffffffe);
			$s .= pack("V",0x00000003);
			$s .= pack("V",0xfffffffe);
			$s  = str_pad($s,512,chr(0xff));
			fwrite($this->xlsfilehandle,$s,512);
		}
		else {
			$s = "";
			$scount = $this->xlsstreamsize >> 6;
			$scount+= (($this->xlsstreamsize & 0x3f)>0 ? 1 : 0);
			$scount--;
			for ($i=0; $i!=$scount; $i++) {
				$s .= pack ("V",$i+1);
			}
			$scount++;
			$s .= pack("V",0xfffffffe);
			$scount++;
			$s .= pack("V",$scount);
			$scount++;
			$s .= pack("V",0xfffffffe);
			$scount++;
			$s .= pack("V",$scount);
			$scount++;
			$s .= pack("V",0xfffffffe);
			$s = str_pad($s,512,chr(0xff));
			fwrite($this->xlsfilehandle,$s,512);
		}
		$fpos = ftell($this->xlsfilehandle);
		fseek($this->xlsfilehandle,0x003c,SEEK_SET);
		fwrite($this->xlsfilehandle,$minifat_sectorid,4);
		fseek($this->xlsfilehandle,$fpos,SEEK_SET);
	}
	
	private function xls_writerootstorage() {
		$s="";
		$s.=pack("C*",0xfe,0xff,0x00,0x00,0x04,0x0a,0x02,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00);
		$s.=pack("C*",0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x01,0x00,0x00,0x00,0x02,0xd5,0xcd,0xd5);
		$s.=pack("C*",0x9c,0x2e,0x1b,0x10,0x93,0x97,0x08,0x00,0x2b,0x2c,0xf9,0xae,0x30,0x00,0x00,0x00);
		$s.=pack("C*",0x18,0x00,0x00,0x00,0x01,0x00,0x00,0x00,0x01,0x00,0x00,0x00,0x10,0x00,0x00,0x00);
		$s.=pack("C*",0x02,0x00,0x00,0x00,0xe4,0x04,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00);
		$s.=pack("C*",0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00);
		$s.=pack("C*",0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00);
		$s.=pack("C*",0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00);
		$s.=pack("C*",0xfe,0xff,0x00,0x00,0x04,0x0a,0x02,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00);
		$s.=pack("C*",0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x01,0x00,0x00,0x00,0xe0,0x85,0x9f,0xf2);
		$s.=pack("C*",0xf9,0x4f,0x68,0x10,0xab,0x91,0x08,0x00,0x2b,0x27,0xb3,0xd9,0x30,0x00,0x00,0x00);
		$s.=pack("C*",0x18,0x00,0x00,0x00,0x01,0x00,0x00,0x00,0x01,0x00,0x00,0x00,0x10,0x00,0x00,0x00);
		$s.=pack("C*",0x02,0x00,0x00,0x00,0xe4,0x04,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00);
		$s.=pack("C*",0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00);
		$s.=pack("C*",0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00);
		$s.=pack("C*",0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00);
		fwrite($this->xlsfilehandle,$s,256);
	}
	
/*
struct StructuredStorageDirectoryEntry { 
	//                             [offset from start in bytes, length in bytes] 
	BYTE _ab[32*sizeof(WCHAR)]; // [000H,64] 64 bytes. The Element name in Unicode, padded with zeros to fill this byte array 
	WORD _cb; 					// [040H,02] Length of the Element name in characters, not bytes 
	BYTE _mse; 					// [042H,01] Type of object: value taken from the STGTY enumeration 
									STGTY_INVALID   = 0, 
									STGTY_STORAGE   = 1, 
									STGTY_STREAM    = 2, 
									STGTY_LOCKBYTES = 3, 
									STGTY_PROPERTY  = 4, 
									STGTY_ROOT      = 5,	
	BYTE _bflags; 				// [043H,01] Value taken from DECOLOR enumeration. 
									DE_RED = 0, 
									DE_BLACK = 1,
	SID _sidLeftSib; 			// [044H,04] SID of the left-sibling of this entry in the directory tree 
	SID _sidRightSib; 			// [048H,04] SID of the right-sibling of this entry in the directory tree 
	SID _sidChild; 				// [04CH,04] SID of the child acting as the root of all the children of this element (if _mse=STGTY_STORAGE) 
	GUID _clsId; 				// [050H,16] CLSID of this storage (if _mse=STGTY_STORAGE) 
	DWORD _dwUserFlags; 		// [060H,04] User flags of this storage (if _mse=STGTY_STORAGE) 
	TIME_T _time[2]; 			// [064H,16] Create/Modify time-stamps (if _mse=STGTY_STORAGE) 
	SECT _sectStart; 			// [074H,04] starting SECT of the stream (if _mse=STGTY_STREAM) 
	ULONG _ulSize; 				// [078H,04] size of stream in bytes (if _mse=STGTY_STREAM) 
	DFPROPTYPE _dptPropType; 	// [07CH,02] Reserved for future use. Must be zero. 
};
*/
	private function xls_writedirentry($adirname,$adirtype,$adecolor,$arightchild,$asectorid,$asize) {
		$dirname = "";
		for ($i=0; $i<strlen($adirname); $i++) {
			$dirname .= $adirname{$i}.chr(0);
		}
		$dirname = str_pad($dirname,64,chr(0));
		$namelen = (strlen($adirname)*2)+2;
		fwrite($this->xlsfilehandle,$dirname,64);
		fwrite($this->xlsfilehandle,pack("v",$namelen));
		fwrite($this->xlsfilehandle,pack("C",$adirtype));
		fwrite($this->xlsfilehandle,pack("C",$adecolor));
		fwrite($this->xlsfilehandle,pack("V",0xffffffff));
		fwrite($this->xlsfilehandle,pack("V",$arightchild));
		fwrite($this->xlsfilehandle,pack("V",($adirname=="Root Entry" ? 0x00000001 : 0xffffffff)));
		$s = str_repeat(chr(0),16);
		fwrite($this->xlsfilehandle,$s,16);
		fwrite($this->xlsfilehandle,pack("V",0x00000000));
		fwrite($this->xlsfilehandle,$s,16);
		fwrite($this->xlsfilehandle,pack("V",$asectorid));
		fwrite($this->xlsfilehandle,pack("V",$asize));
		fwrite($this->xlsfilehandle,pack("v",0x0000));
		fwrite($this->xlsfilehandle,pack("v",0x0000));
	}
	
	public function sendfile($afilename) {
		// 2009-10-22 - modified by Helmut Schottmüller
		// Change path to TYPOlight temp dir
		$currentdir = getcwd();
		chdir(TL_ROOT . '/system/tmp');
		// 2009-10-22 - end modification
		$tmpname = "tmp".date("YmdHi").".xls";
		$this->savefile($tmpname);
		header("Content-Type:application/force-download");
		$headerfilename=sprintf("Content-Disposition: attachment; filename=%s",$afilename);
		header($headerfilename);
		header("Content-Transfer-Encoding: binary");
		$dlfilesize = filesize($tmpname);
		header("Content-Length: ".$dlfilesize);
		@readfile($tmpname,$afilename);
		unlink($tmpname);
		// 2009-10-22 - modified by Helmut Schottmüller
		// Change path back to current dir
		chdir($currentdir);
		// 2009-10-22 - end modification
	}

	// additional getters
	// 2010-02-02 - modified by Helmut Schottmüller, methods by Georg Rehfeld
	/**
	 * Returns the width of the given column.
	 *
	 * The return value is in terms of 256 times the width of the 0 (zero) character in
	 * the default font (the first one defined). When devided by 256 it is
	 * roughly comparable to CSS 'en' units.
	 */
	public function getcolwidth($sheetname, $acolidx) {
		$sheetid = $this->xlsdocument[$sheetname]["worksheetid"];
		if (isset($this->worksheets[$sheetid]["colwidths"][$acolidx]))
		{
			return $this->worksheets[$sheetid]["colwidths"][$acolidx];
		}
		return XLSFILE_DEFAULT_COLWIDTH; // 0x0924 = 2340 = 9.14 en ?
	}
	
	/**
	 * Returns the height of the given row.
	 *
	 * The return value is in terms of 256 times the width of the 0 (zero) character in
	 * the default font (the first one defined). When devided by 256 it is
	 * roughly comparable to CSS 'en' units.
	 *
	 * @TODO: there seems to be a need for some scale factor, when used with cw/ccw text?
	 */
	public function getrowheight($sheetname, $arowidx) {
		$sheetid = $this->xlsdocument[$sheetname]["worksheetid"];
		if (isset($this->worksheets[$sheetid]["rowheights"][$arowidx]))
		{
			return $this->worksheets[$sheetid]["rowheights"][$arowidx];
		}
		return XLSFILE_DEFAULT_ROWHEIGHT; // 0x012c = 300 = 1.17 en ?
	}
	// 2010-02-02 - end modification
	
}
?>
