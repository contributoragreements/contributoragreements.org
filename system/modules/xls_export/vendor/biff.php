<?php
	define ("XLSDATA_BYTE",     1,true);
	define ("XLSDATA_SHORT",    2,true);
	define ("XLSDATA_LONG",     3,true);
	define ("XLSDATA_FLOAT",    4,true);
	define ("XLSDATA_STRING",   5,true);
	define ("XLSDATA_LSTRING",  6,true);
	define ("XLSDATA_STRING1",  7,true);
	define ("XLSDATA_DATA32BIT",8,true);
	
	define ("XLS_BIFF5",0x0809);
	define ("XLS_BIFF_EOF",0x000a);
	define ("WORKBOOK_SHEET",0x0010);
	
	define ("BIFF_WORKBOOKGLOBALS",0x0005);
	define ("BIFF_CALCCOUNT",0x000c);
	define ("BIFF_CALCMODE",0x000d);
	define ("BIFF_PRECISION",0x000e);
	define ("BIFF_REFMODE",0x000f);
	define ("BIFF_DELTA",0x0010);
	define ("BIFF_ITERATION",0x0011);
	define ("BIFF_PROTECT",0x0012);
	define ("BIFF_PASSWORD",0x0013);
	define ("BIFF_PAGEHEADER",0x0014);
	define ("BIFF_PAGEFOOTER",0x0015);
	define ("BIFF_EXTERNALREFERENCESCOUNT",0x0016);
	define ("BIFF_EXTERNSHEET",0x0017);
	define ("BIFF_DEFINEDNAME",0x0018);
	define ("BIFF_WINDOWPROTECT",0x0019);
	define ("BIFF_SELECTION",0x001d);
	define ("BIFF_DATEMODE",0x0022);
	define ("BIFF_LEFTMARGIN",0x0026);
	define ("BIFF_RIGHTMARGIN",0x0027);
	define ("BIFF_TOPMARGIN",0x0028);
	define ("BIFF_BOTTOMMARGIN",0x0029);
	define ("BIFF_PRINTHEADERS",0x002a);
	define ("BIFF_PRINTGRIDLINES",0x002b);
	define ("BIFF_FONT",0x0031);
	define ("BIFF_CONTINUE",0x003c);
	define ("BIFF_WINDOW1",0x003d);
	define ("BIFF_BACKUP",0x0040);
	define ("BIFF_DEFCOLWIDTH",0x0055);
	define ("BIFF_WRITEACCESS",0x005c);
	define ("BIFF_OBJECT",0x005d);
	define ("BIFF_SAVERECALC",0x005f);
	define ("BIFF_COLINFO",0x007d);
	define ("BIFF_IMAGEDATA",0x007f);
	define ("BIFF_GUTS",0x0080);
	define ("BIFF_SHEETPR",0x0081);
	define ("BIFF_GRIDSET",0x0082);
	define ("BIFF_HCENTER",0x0083);
	define ("BIFF_VCENTER",0x0084);
	define ("BIFF_BOUNDSHEET",0x0085);
	define ("BIFF_COUNTRY",0x008c);
	define ("BIFF_OBJECTDISPLAYOPTIONS",0x008d);
	define ("BIFF_PALETTE",0x0092);
	define ("BIFF_FUNCTIONGROUPCOUNT",0x009c);
	define ("BIFF_PAGESETUP",0x00a1);
	define ("BIFF_TOOLBARHEADER",0x00bf);			// undocumented
	define ("BIFF_TOOLBAREND",0x00c0);				// undocumented
	define ("BIFF_MENURECORDGROUP",0x00c1);
	define ("BIFF_DBCELL",0x00d7);
	define ("BIFF_BOOKBOOL",0x00da);
	define ("BIFF_XFRECORD",0x00e0);
	define ("BIFF_INTERFACEHEADER",0x00e1);
	define ("BIFF_INTERFACEEND",0x00e2);
	define ("BIFF_MERGEDCELLS",0x00e5);
	
/*
016FH = 367 = ASCII
01B5H = 437 = IBM PC CP-437 (US)
02D0H = 720 = IBM PC CP-720 (OEM Arabic)
02E1H = 737 = IBM PC CP-737 (Greek)
0307H = 775 = IBM PC CP-775 (Baltic)
0352H = 850 = IBM PC CP-850 (Latin I)
0354H = 852 = IBM PC CP-852 (Latin II (Central European))
0357H = 855 = IBM PC CP-855 (Cyrillic)
0359H = 857 = IBM PC CP-857 (Turkish)
035AH = 858 = IBM PC CP-858 (Multilingual Latin I with Euro)
035CH = 860 = IBM PC CP-860 (Portuguese)
035DH = 861 = IBM PC CP-861 (Icelandic)
035EH = 862 = IBM PC CP-862 (Hebrew)
035FH = 863 = IBM PC CP-863 (Canadian (French))
0360H = 864 = IBM PC CP-864 (Arabic)
0361H = 865 = IBM PC CP-865 (Nordic)
0362H = 866 = IBM PC CP-866 (Cyrillic (Russian))
0365H = 869 = IBM PC CP-869 (Greek (Modern))
036AH = 874 = Windows CP-874 (Thai)
03A4H = 932 = Windows CP-932 (Japanese Shift-JIS)
03A8H = 936 = Windows CP-936 (Chinese Simplified GBK)
03B5H = 949 = Windows CP-949 (Korean (Wansung))
03B6H = 950 = Windows CP-950 (Chinese Traditional BIG5)
04B0H = 1200 = UTF-16 (BIFF8)
04E2H = 1250 = Windows CP-1250 (Latin II) (Central European)
04E3H = 1251 = Windows CP-1251 (Cyrillic)
04E4H = 1252 = Windows CP-1252 (Latin I) (BIFF4-BIFF5)
04E5H = 1253 = Windows CP-1253 (Greek)
04E6H = 1254 = Windows CP-1254 (Turkish)
04E7H = 1255 = Windows CP-1255 (Hebrew)
04E8H = 1256 = Windows CP-1256 (Arabic)
04E9H = 1257 = Windows CP-1257 (Baltic)
04EAH = 1258 = Windows CP-1258 (Vietnamese)
0551H = 1361 = Windows CP-1361 (Korean (Johab))
2710H = 10000 = Apple Roman
8000H = 32768 = Apple Roman
8001H = 32769 = Windows CP-1252 (Latin I) (BIFF2-BIFF3)
*/
	define ("BIFF_CODEPAGE",0x0042);
	
	define ("BIFF_REFRESHALL",0x01b7);
	define ("BIFF_DIMENSIONS",0x0200);
	define ("BIFF_NUMBER",0x0203);
	define ("BIFF_LABEL",0x0204);
	define ("BIFF_ROW",0x0208);
	define ("BIFF_INDEX",0x020b);
	define ("BIFF_DEFAULTROWHEIGHT",0x0225);
	define ("BIFF_WINDOW2",0x023e);
	define ("BIFF_STYLEINFORMATION",0x0293);
	define ("BIFF_FORMAT",0x041e);
	define ("BIFF_SHEETPROTECTION",0x0867);
	
	class xls_bof {
		var $data = null;
		var $size = 0;
		var $type = null;
		
		public function __construct($aboftype=-1) {
			if ($aboftype==-1) { die("Error"); }
			$this->data = array();
			$this->type = $aboftype;
		}
		
		public function append($adatatype, $adatarec) {
			$new = array();
			$new["type"] = $adatatype;
			$new["data"] = $adatarec;
			$this->data[] = $new;
		}
		
		public function clear($aboftype=-1) {
			if ($aboftype==-1) { die("Error"); }
			$this->type = $aboftype;
			$this->data = array();
			return true;
		}
		
		public function fetch() {
			$output = "";
			foreach ($this->data as $key => $datarec) {
				switch ($datarec["type"]) {
					case XLSDATA_BYTE :	$output .= pack("C",$datarec["data"]);
										$this->size+=1;
										break;
										
					case XLSDATA_SHORT:	$output .= pack("v",$datarec["data"]);
										$this->size+=2;
										break;

					case XLSDATA_LONG : $output .= pack("V",$datarec["data"]);
										$this->size+=4;
										break;
					
					case XLSDATA_FLOAT: $output .= pack("d",$datarec["data"]);
										$this->size+=8;
										break;
					
					case XLSDATA_DATA32BIT : 
										$ldata = $datarec["data"];
										$b3 = $ldata & 0xff;
										$b2 = ($ldata >> 8) & 0xff;
										$b1 = ($ldata >> 16) & 0xff;
										$b0 = ($ldata >> 24) & 0xff;
										$output .= pack("CCCC",$b0,$b1,$b2,$b3);
										$this->size+=4;
										break;
					
					case XLSDATA_STRING:
										$output .= pack("C",strlen($datarec["data"]));
										$this->size++;
										if (strlen($datarec["data"])==0) {
											$output .= pack("C",0x00);
											$this->size++;
											break;
										}
					
					case XLSDATA_STRING1:
										$this->size+=strlen($datarec["data"]);
										//$output .= pack("C",$datarec["data"]);
										$output .= $datarec["data"];
										break;
					case XLSDATA_LSTRING:
										$output .= pack("v",strlen($datarec["data"]));
										$this->size++;
										$this->size++;
										$this->size+=strlen($datarec["data"]);
										//$output .= pack("C",$datarec["data"]);
										$output .= $datarec["data"];
										break;
				}
			}
			return pack("vv",$this->bof,$this->size).$output;
		}
		
		public function save($afilehd) {
			$this->bof = $this->type;
			$this->size = 0;
			$tmp = $this->fetch();
			if ($this->size<2080) {
				fwrite($afilehd,$tmp);
			}
			else {
				if (defined("BIFFDEBUG")) {
					echo "long record - size : ".$this->size."<br>";
				}
				$wcount = $this->size;
				$tmptype = $this->type;
				$tmp = substr($tmp,4);
				$tmparr = str_split($tmp,2076);
				if (defined("BIFFDEBUG")) {
					echo "create parts : ".count($tmparr)."<br>";
				}
				$tmpout = "";
				foreach ($tmparr as $key => $datapart) {
					if (defined("BIFFDEBUG")) {
						echo "part ($key) size : ".strlen($datapart)."<br>";
					}
					$tmpout = pack("vv",$tmptype,strlen($datapart)).$datapart;
					fwrite($afilehd,$tmpout);
					$tmptype = BIFF_CONTINUE;
				}
				unset($tmparr);
			}
			return true;
		}
		
		public function workbookxfrecords($afilehd) {
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0xfff5,0x00,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0001,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0001,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0002,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0002,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0xfff5,0xf4,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0x0001,0x00,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0001,0x002b,0xfff5,0xf8,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0001,0x0029,0xfff5,0xf8,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0001,0x002c,0xfff5,0xf8,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0001,0x002a,0xfff5,0xf8,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0001,0x0009,0xfff5,0xf8,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0x0001,0x10,0x000020c0);
			$this->workbookonexfdata($afilehd,0x0000,0x0000,0x0001,0x50,0x00000488);
		}
		
		public function workbookonexfdata($afilehd, $fontindex, $formatindex, $xftype, $textorientation, $colorindexs) {
			$this->clear(BIFF_XFRECORD);
			$this->append(XLSDATA_SHORT,$fontindex);
			$this->append(XLSDATA_SHORT,$formatindex);
			$this->append(XLSDATA_SHORT,$xftype);
			$this->append(XLSDATA_BYTE,0x20);				/* alignment
														   bits mask
															2-0 07H XF_HOR_ALIGN ? Horizontal alignment
																0 General
																1 Left
																2 Centred
																3 Right
																4 Filled
																5 Justified (BIFF4-BIFF8)
																6 Centred across selection (BIFF4-BIFF8)
																7 Distributed (BIFF8, available in Excel 10.0 (Excel XP) and later only)
															3 08H 1 = Text is wrapped at right border
															6-4 70H XF_VERT_ALIGN ? Vertical alignment
																0 Top
																1 Centred
																2 Bottom
																3 Justified (BIFF5-BIFF8)
																4 Distributed (BIFF8, available in Excel 10.0 (Excel XP) and later only)
															*/			
			$this->append(XLSDATA_BYTE,$textorientation);	/* 
															bits mask
															1-0 03H XF_ORIENTATION ? Text orientation
																0 Not rotated
																1 Letters are stacked top-to-bottom, but not rotated
																2 Text is rotated 90 degrees counterclockwise
																3 Text is rotated 90 degrees clockwise
															7-2 FCH XF_USED_ATTRIB ? Used attributes
																0 01H Flag for number format
																1 02H Flag for font
																2 04H Flag for horizontal and vertical alignment, text wrap, indentation, orientation, rotation, and text direction
																3 08H Flag for border lines
																4 10H Flag for background area style
																5 20H Flag for cell protection (cell locked and formula hidden)
															*/
			$this->append(XLSDATA_LONG,$colorindexs);		/* Cell border lines and background area:
															Bit     Mask       Contents
															6-0   0000007FH Colour index for pattern colour
															13-7  00003F80H Colour index for pattern background
															21-16 003F0000H Fill pattern 
															24-22 01C00000H Bottom line style 
															31-25 FE000000H Colour index for bottom line colour
															*/
			$this->append(XLSDATA_LONG,0);					/* Line styles
															Bit     Mask       Contents
															2-0   00000007H Top line style
															5-3   00000038H Left line style
															8-6   000001C0H Right line style
															15-9  0000FE00H Colour index for top line colour
															22-16 007F0000H Colour index for left line colour
															29-23 3F800000H Colour index for right line colour
															*/
			$this->save($afilehd);
		}
		
	}
?>