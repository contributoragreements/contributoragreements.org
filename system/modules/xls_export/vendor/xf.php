<?php
	define ("XLSXF_TYPE_PROT_CELLLOCKED",    0x01);
	define ("XLSXF_TYPE_PROT_FORMULAHIDDEN", 0x02);
	define ("XLSXF_TYPE_PROT_STYLEXF",       0x04);
	define ("XLSXF_TYPE_PROT_F123PREFIX",    0x08);
	
	define ("XLSXF_USEDATTRIB_ATRNUM"  ,0x04);
	define ("XLSXF_USEDATTRIB_ATRFONT" ,0x08);
	define ("XLSXF_USEDATTRIB_ATRALC"  ,0x10);
	define ("XLSXF_USEDATTRIB_ATRBDR"  ,0x20);
	define ("XLSXF_USEDATTRIB_ATRPAT"  ,0x40);
	define ("XLSXF_USEDATTRIB_ATRPROT" ,0x80);
	
	// horizontal allignment
	define ("XLSXF_HALLIGN_GENERAL", 0x00);
	define ("XLSXF_HALLIGN_LEFT"   , 0x01);
	define ("XLSXF_HALLIGN_CENTER" , 0x02);
	define ("XLSXF_HALLIGN_RIGHT"  , 0x03);
	define ("XLSXF_HALLIGN_FILL"   , 0x04);
	define ("XLSXF_HALLIGN_JUSTIFY", 0x05);
	define ("XLSXF_HALLIGN_CACROSS", 0x06);	// center across selection
	//define ("XLSXF_HALLIGN_DISTRIBUTED", 0x07); not used in BIFF5 , available in Excel 10.0 (Excel XP) and later
	
	// vertical allignment
	define ("XLSXF_VALLIGN_TOP"    , 0x00);
	define ("XLSXF_VALLIGN_CENTER" , 0x10);
	define ("XLSXF_VALLIGN_BOTTOM" , 0x20);
	define ("XLSXF_VALLIGN_JUSTIFY", 0x30);
	//define ("XLSXF_VALLIGN_DISTRIBUTED", 0x40); not used in BIFF5 , available in Excel 10.0 (Excel XP) and later
	
	define ("XLSXF_WRAPTEXT", 0x08);	// wrap text at right border
	
	define ("XLSXF_TEXTROTATION_NOROTATION",       0x00);
	define ("XLSXF_TEXTROTATION_UPRIGHT",          0x01);	// text appears top-to-bottom; letters are upright.
	define ("XLSXF_TEXTROTATION_COUNTERCLOCKWISE", 0x02); // text is rotated 90 degrees counterclockwise
	define ("XLSXF_TEXTROTATION_CLOCKWISE",        0x03); // text is rotated 90 degrees clockwise
	
	define ("XLSXF_BORDER_NOBORDER", 0x00);
	define ("XLSXF_BORDER_THIN",     0x01);
	define ("XLSXF_BORDER_MEDIUM",   0x02);
	define ("XLSXF_BORDER_DASHED",   0x03);
	define ("XLSXF_BORDER_DOTTED",   0x04);
	define ("XLSXF_BORDER_THICK",    0x05);
	define ("XLSXF_BORDER_DOUBLE",   0x06);
	define ("XLSXF_BORDER_HAIR",     0x07);
	
	/*
	not used in BIFF5 , available in BIFF8 (Excel 10.0 / Excel XP) and later
	define ("XLSXF_BORDER_MEDIUMDASHED",     0x08);
	define ("XLSXF_BORDER_THINDASHDOT",      0x09);
	define ("XLSXF_BORDER_MEDIUMDASHDOT",    0x0a);
	define ("XLSXF_BORDER_THINDASHDOTDOT",   0x0b);
	define ("XLSXF_BORDER_MEDIUMDASHDOTDOT", 0x0c);
	define ("XLSXF_BORDER_SLANTEDDASHDOT",   0x0d);
	*/	
	
	class xls_xf {
		var	$xf_array = null;
		var $defaultxf = null;
		
		public function xls_xf() {
			$this->xf_array = array();
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4, "fontindex" => 1));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4, "fontindex" => 1));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4, "fontindex" => 2));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4, "fontindex" => 2));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf4));
			$this->append(array("forced"=>1));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf8, "fontindex" => 1, "formatindex" => 0x2b));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf8, "fontindex" => 1, "formatindex" => 0x29));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf8, "fontindex" => 1, "formatindex" => 0x2c));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf8, "fontindex" => 1, "formatindex" => 0x2a));
			$this->append(array("forced"=>1, "parentxfindex" => 0xffffff, "xftype" => XLSXF_TYPE_PROT_STYLEXF, "flagsforce" => 0xf8, "fontindex" => 1, "formatindex" => 0x09));
			$this->defaultxf = $this->append(array("forced"=>1, "flagsforce" => 0x50, "patternbgcolor" => 0x88, "patterncolor" => 0x08, "fillpattern" => 0));
		}
		
		public function append($params) {
			$xf = array("fontindex" => 0x00,
						"formatindex" => 0x00,
						"parentxfindex" => 0x0,
						"xftype" => XLSXF_TYPE_PROT_CELLLOCKED,
						"allign" => XLSXF_VALLIGN_BOTTOM | XLSXF_HALLIGN_GENERAL,
						"flags"  => 0x00,
						"rotate" => XLSXF_TEXTROTATION_NOROTATION,
						"patterncolor" 		=> 0x40,
						"patternbgcolor" 	=> 0xc0,
						"fillpattern" 		=> 0x00,
						"bordertop" 		=> XLSXF_BORDER_NOBORDER,
						"bordertopcolor" 	=> 0x0000,
						"borderbottom" 		=> XLSXF_BORDER_NOBORDER,
						"borderbottomcolor" => 0x0000,
						"borderleft" 		=> XLSXF_BORDER_NOBORDER,
						"borderleftcolor" 	=> 0x0000,
						"borderright" 		=> XLSXF_BORDER_NOBORDER,
						"borderrightcolor" 	=> 0x0000
					   );
			if (is_array($params)) {
				foreach ($params as $key => $param) {
					switch ($key) {
						case "formatindex"		:	$xf[$key]=$param;
													$xf["flags"]=$xf["flags"] | XLSXF_USEDATTRIB_ATRNUM;
													break;
													
						case "fontindex"		:	$xf[$key]=$param;
													$xf["flags"]=$xf["flags"] | XLSXF_USEDATTRIB_ATRFONT;
													break;
													
						case "rotate"			:	$xf[$key]=$param;
													$xf["flags"]=$xf["flags"] | XLSXF_USEDATTRIB_ATRALC;
													break;
						case "xftype"			:	
													$xf[$key]=$xf[$key] | $param;
													break;
													
						case "parentxfindex"	:	$xf[$key]=$param;
													$xf["flags"]=$xf["flags"] | XLSXF_USEDATTRIB_ATRNUM;
													break;
													
						case "patterncolor"		:	$xf[$key] = $param;	// foreground color definied with font
													break;
						
						case "patternbgcolor"	:	$xf[$key]=$param;
													$xf["patterncolor"]=0x08;
													if ($xf["fillpattern"]==0x00) { $xf["fillpattern"]=0x01; }
													$xf["flags"]=$xf["flags"] | XLSXF_USEDATTRIB_ATRPAT | XLSXF_USEDATTRIB_ATRALC;
													break;
						case "fillpattern"		:	$xf[$key]=$param;
													$xf["flags"]=$xf["flags"] | XLSXF_USEDATTRIB_ATRPAT;
													break;
													
						case "hallign"			:	
													$i = $xf["allign"]&0xf8;
													$i = $i | $param;
													$xf["allign"] = $i;
													$xf["flags"]=$xf["flags"] | XLSXF_USEDATTRIB_ATRFONT;
													break;
													
						case "vallign"			:	$i = $xf["allign"]&0x1f;
													$i = $i | $param;
													$xf["allign"] = $i;
													$xf["flags"]=$xf["flags"] | XLSXF_USEDATTRIB_ATRFONT;
													break;
													
						case "textwrap"			:	$xf["allign"]=$xf["allign"] | XLSXF_WRAPTEXT;
													$xf["flags"]=$xf["flags"] | XLSXF_USEDATTRIB_ATRFONT;
													break;
						
						case "border"			:	$xf["bordertop"]=$param;
													$xf["borderbottom"]=$param;
													$xf["borderleft"]=$param;
													$xf["borderright"]=$param;
													$param=0x0008;					// default border color = black
						case "bordercolor"		:	$xf["bordertopcolor"]=$param;
													$xf["borderbottomcolor"]=$param;
													$xf["borderleftcolor"]=$param;
													$xf["borderrightcolor"]=$param;
													$xf["flags"]=$xf["flags"] | XLSXF_USEDATTRIB_ATRBDR;
													break;
													
						case "bordertop"		:
						case "borderbottom"		:
						case "borderleft"		:
						case "borderright"		:
													$xf[$key]=$param;
													$colorkey = $key."color";
													if ($xf[$colorkey]==0x0000) { $xf[$colorkey]=0x0009; }
													$xf["flags"]=$xf["flags"] | XLSXF_USEDATTRIB_ATRBDR;
													break;
						case "bordertopcolor"	:
						case "borderbottomcolor":
						case "borderleftcolor":
						case "borderrightcolor":
													$xf[$key]=$param;
													$xf["flags"]=$xf["flags"] | XLSXF_USEDATTRIB_ATRBDR;
													break;
					}
				}
				if (count($this->xf_array)==0) {
					$xf["flags"] = 0;
				}
				if ($xf["parentxfindex"]==0xffffff) {
					$xf["flags"] = $xf["flags"] & 0xf7;
				}
				if (isset($params["flagsforce"])) {
					$xf["flags"] = $params["flagsforce"];
				}
			}
			$s = serialize($xf);
			$xfindex = array_search($s,$this->xf_array);
			if (($xfindex===false) || (isset($params["forced"]))) {
				$xfindex = count($this->xf_array);
				$this->xf_array[] = $s;
				if (defined("XFDEBUG")) {
					$xfindex = array_search($s,$this->xf_array);
					echo "new XF record , index : $xfindex\n";
					echo "new XF record : $s \n";
				}
			}
			return $xfindex;
		}
		
		public function save($filehandle,$xls_biffobject) {
			foreach ($this->xf_array as $xfindex => $tmp) {
				$xfrec = unserialize($tmp);
				$xls_biffobject->clear(BIFF_XFRECORD);
				$xls_biffobject->append(XLSDATA_SHORT,$xfrec["fontindex"]);
				$xls_biffobject->append(XLSDATA_SHORT,$xfrec["formatindex"]);
				$i = ($xfrec["parentxfindex"]<<4) | $xfrec["xftype"];
				$xls_biffobject->append(XLSDATA_SHORT,$i);
				$xls_biffobject->append(XLSDATA_BYTE,$xfrec["allign"]);
				$i = $xfrec["rotate"] | $xfrec["flags"];
				$xls_biffobject->append(XLSDATA_BYTE,$i);
				
				$i = $xfrec["patternbgcolor"] | ($xfrec["patterncolor"] << 7);
				$i = (($i == 0) ? 0x20c0 : $i);										// magic number if no colors defined
				$i = $i | ($xfrec["fillpattern"] << 16);
				$i = $i | ($xfrec["borderbottom"] << 22);
				$i = $i | ($xfrec["borderbottomcolor"] << 25);
				$xls_biffobject->append(XLSDATA_LONG,$i);
				
				$i = $xfrec["bordertop"] | ($xfrec["borderleft"] << 3) | ($xfrec["borderright"] << 6);
				$i = $i | ($xfrec["bordertopcolor"] << 9) | ($xfrec["borderleftcolor"] << 16) | ($xfrec["borderrightcolor"] << 23);
				$xls_biffobject->append(XLSDATA_LONG,$i);
				$xls_biffobject->save($filehandle);
			}
		}
	}
?>