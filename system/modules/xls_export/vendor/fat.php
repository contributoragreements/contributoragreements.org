<?php
	class fat_class {
		var $streamsize = null;
		var $rootstorageoffset = null;
		var $fatchain = array();
		var $fatsectorcount = null;
		
		public function __construct($afilehd, $astreamsize, $arootstorageoffset) {
			$this->streamsize = $astreamsize;
			$this->rootstorageoffset = $arootstorageoffset;
			
			if ($this->streamsize>=0x1000) {
				$streamsectors = $this->streamsize >> 9;
				$streamsectors += (($this->streamsize & 0x1ff)>0 ? 1 : 0);
			}
			else {
				$streamsectors = ($this->rootstorageoffset+0x100) >> 9;
				$streamsectors += ((($this->rootstorageoffset+0x100) & 0x1ff)>0 ? 1 : 0);
				$streamsectors--;
			}
			while ($streamsectors>1) {
				$this->fatchain[] = count($this->fatchain)+1;		// allocate next sector;
				$streamsectors--;
			}
			$this->fatchain[] = 0xfffffffe;							// last sector of stream
			if ($this->streamsize>=0x1000) {
				$this->fatchain[] = 0xfffffffe;							// rootstorage , used one sector
			}
			$this->fatchain[] = 0xfffffffe;							// minifat , used one sector
			$this->fatchain[] = 0xfffffffe;							// directory , used one sector
			
			$fatsize1 = (count($this->fatchain) >> 7) + (((count($this->fatchain) & 0x7f)>0) ? 1 : 0);
			$fatsize0 = $fatsize1;
			while ($fatsize1) {
				$this->fatchain[] = 0xfffffffd;							// fat , used nn sector
				$fatsize1--;
			}
			$this->fatsectorcount = (count($this->fatchain) >> 7)+(((count($this->fatchain) & 0x7f)>0) ? 1 : 0);
			if ($this->fatsectorcount!=$fatsize0) { $this->fatchain[] = 0xfffffffd; }
			$reqbytes = 128-(count($this->fatchain) & 0x7f);
			$fatfill = array_fill(count($this->fatchain),$reqbytes,0xffffffff);
			$this->fatchain = array_merge($this->fatchain,$fatfill);
			
			$output = "";
			$count = 0;
			foreach ($this->fatchain as $key => $sectorid) {
				$output .= pack("V",$sectorid);
				$count += 4;
			}
			fwrite($afilehd,$output,$count);
		}
	}
?>