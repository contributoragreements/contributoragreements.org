<?php
	class xls_mergedcells {
		var $merged_array = null;
		
		public function xls_mergedcells() {
			$this->merged_array=array();
		}
		
		public function merge_cells($arowfirst, $arowlast, $acolfirst, $acollast) {
			$this->merged_array[]=array("rowfirst" => $arowfirst, "rowlast" => $arowlast, "colfirst" => $acolfirst, "collast" => $acollast);
		}
		
		public function findpos($arow,$acol) {
			foreach ($this->merged_array as $key => $data) {
				if ((($arow>=$data["rowfirst"]) && ($arow<=$data["rowlast"])) && (($acol>=$data["colfirst"]) && ($acol<=$data["collast"]))) {
					return array("row" => $data["rowfirst"],"col" => $data["colfirst"]);
				}
			}
			return false;
		}
		
		public function save($filehandle,$xls_biffobject) {
			$xls_biffobject->clear(BIFF_MERGEDCELLS);
			$xls_biffobject->append(XLSDATA_SHORT,count($this->merged_array));
			foreach ($this->merged_array as $key => $data) {
				$xls_biffobject->append(XLSDATA_SHORT,$data["rowfirst"]);
				$xls_biffobject->append(XLSDATA_SHORT,$data["rowlast"]);
				$xls_biffobject->append(XLSDATA_SHORT,$data["colfirst"]);
				$xls_biffobject->append(XLSDATA_SHORT,$data["collast"]);
			}
			$xls_biffobject->save($filehandle);
		}
	}
?>