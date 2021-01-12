<?php
	define ("XLSFONT_NORMAL",0x0190);
	define ("XLSFONT_BOLD",0x02bc);
	
	define ("XLSFONT_ESCAPE_NONE",0x0000);
	define ("XLSFONT_ESCAPE_SUPERSCRIPT",0x0001);
	define ("XLSFONT_ESCAPE_SUBSCRIPT",0x0002);
	
	define ("XLSFONT_UNDERLINE_NONE",0x00);
	define ("XLSFONT_UNDERLINE_SINGLE",0x01);
	define ("XLSFONT_UNDERLINE_DOUBLE",0x02);
	define ("XLSFONT_UNDERLINE_SINGLEACC",0x21);
	define ("XLSFONT_UNDERLINE_DOUBLEACC",0x22);
	
	define ("XLSFONT_FAMILY_NORMAL",0x00);
	define ("XLSFONT_FAMILY_ROMAN",0x01);
	define ("XLSFONT_FAMILY_SWISS",0x02);
	define ("XLSFONT_FAMILY_MODERN",0x03);
	define ("XLSFONT_FAMILY_SCRIPT",0x04);
	define ("XLSFONT_FAMILY_DECORATIVE",0x05);
	
	define ("XLSFONT_STYLE_ITALIC",0x0002);
	define ("XLSFONT_STYLE_STRIKEOUT",0x0008);
	define ("XLSFONT_STYLE_OUTLINED",0x0010);
	define ("XLSFONT_STYLE_SHADOWED",0x0020);
	define ("XLSFONT_STYLE_CONDENSED",0x0040);
	
	define ("XLSFONT_CHARACTERSET_LATIN",0x00);
	define ("XLSFONT_CHARACTERSET_SYSTEMDEFAULT",0x01);
	define ("XLSFONT_CHARACTERSET_SYMBOL",0x02);
	define ("XLSFONT_CHARACTERSET_ROMAN",0x4d);
	define ("XLSFONT_CHARACTERSET_JAPANESE",0x80);
	define ("XLSFONT_CHARACTERSET_KOREAN_HANGUL",0x81);
	define ("XLSFONT_CHARACTERSET_KOREAN_JOHAB",0x82);
	define ("XLSFONT_CHARACTERSET_CHINESE_SIMPLIFIED",0x86);
	define ("XLSFONT_CHARACTERSET_CHINESE_TRADITIONAL",0x88);
	define ("XLSFONT_CHARACTERSET_GREEK",0xa1);
	define ("XLSFONT_CHARACTERSET_TURKISH",0xa2);
	define ("XLSFONT_CHARACTERSET_VIETNAMESE",0xa3);
	define ("XLSFONT_CHARACTERSET_HEBREW",0xb1);
	define ("XLSFONT_CHARACTERSET_ARABIC",0xb2);
	define ("XLSFONT_CHARACTERSET_BALTIC",0xba);
	define ("XLSFONT_CHARACTERSET_CYRILLIC",0xcc);
	define ("XLSFONT_CHARACTERSET_THAI",0xde);
	define ("XLSFONT_CHARACTERSET_LATIN2",0xee);		// central european
	define ("XLSFONT_CHARACTERSET_LATIN1",0xff);
	
	class xls_font {
		var $font_array = null;
		
		public function xls_font() {		// constructor
			$this->font_array = array();
			$this->append(null);
		}
		
		public function append($params) {
			$font["name"] = (isset($params["name"]) ? $params["name"] : XLSFILE_DEFAULT_FONTNAME);
			$font["height"] = (isset($params["height"]) ? $params["height"]*20 : XLSFILE_DEFAULT_FONTHEIGHT);
			$font["weight"] = (isset($params["weight"]) ? $params["weight"] : XLSFONT_NORMAL);
			$font["color"] = (isset($params["color"]) ? $params["color"] : XLSFILE_DEFAULT_FGCOLOR);
			$font["underline"] = (isset($params["underline"]) ? $params["underline"] : XLSFONT_UNDERLINE_NONE);
			$font["escapement"] = (isset($params["escapement"]) ? $params["escapement"] : XLSFONT_ESCAPE_NONE);
			$font["family"] = (isset($params["family"]) ? $params["family"] : XLSFONT_FAMILY_NORMAL);
			$font["style"] = (isset($params["style"]) ? $params["style"] : 0x0000);
			$font["characterset"] = XLSFILE_CHARACTERSET;
			$s = serialize($font);
			unset($font);
			$fontidx = array_search($s,$this->font_array);
			if ($fontidx===false) {
				$this->font_array[] = $s;
				$fontidx = array_search($s,$this->font_array);
			}
			if ($fontidx>0) {
				$fontidx += 5;	// first 5 reserved, +5 for user defined font
			}
			$fontidx += (($fontidx==4) ? 1 : 0);
			return $fontidx;
		}
		
		public function save($filehandle,$xls_biffobject) {
			$font0 = unserialize($this->font_array[0]);
			$xls_biffobject->clear(BIFF_FONT);
			$xls_biffobject->append(XLSDATA_SHORT,$font0["height"]);
			$xls_biffobject->append(XLSDATA_SHORT,$font0["style"]);
			$xls_biffobject->append(XLSDATA_SHORT,$font0["color"]);
			$xls_biffobject->append(XLSDATA_SHORT,$font0["weight"]);
			$xls_biffobject->append(XLSDATA_SHORT,$font0["escapement"]);
			$xls_biffobject->append(XLSDATA_BYTE,$font0["underline"]);
			$xls_biffobject->append(XLSDATA_BYTE,$font0["family"]);
			$xls_biffobject->append(XLSDATA_BYTE,$font0["characterset"]);		
			$xls_biffobject->append(XLSDATA_BYTE,0x0000);
			$xls_biffobject->append(XLSDATA_STRING,$font0["name"]);
			$repeatdefault = 4;
			while ($repeatdefault>0) {
				$xls_biffobject->save($filehandle);
				$repeatdefault--;
			}
			$i=0;
			while ($i!=count($this->font_array)) {
				$font0 = unserialize($this->font_array[$i]);
				$xls_biffobject->clear(BIFF_FONT);
				$xls_biffobject->append(XLSDATA_SHORT,$font0["height"]);
				$xls_biffobject->append(XLSDATA_SHORT,$font0["style"]);
				$xls_biffobject->append(XLSDATA_SHORT,$font0["color"]);
				$xls_biffobject->append(XLSDATA_SHORT,$font0["weight"]);
				$xls_biffobject->append(XLSDATA_SHORT,$font0["escapement"]);
				$xls_biffobject->append(XLSDATA_BYTE,$font0["underline"]);
				$xls_biffobject->append(XLSDATA_BYTE,$font0["family"]);
				$xls_biffobject->append(XLSDATA_BYTE,$font0["characterset"]);		
				$xls_biffobject->append(XLSDATA_BYTE,0x0000);		
				$xls_biffobject->append(XLSDATA_STRING,$font0["name"]);
				$xls_biffobject->save($filehandle);
				$i++;
			}
		}
	}
?>