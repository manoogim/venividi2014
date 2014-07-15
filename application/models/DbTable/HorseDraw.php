<?php
class Model_DbTable_HorseDraw extends Zend_Db_Table_Abstract {
	// name of the table that it will manage
	protected $_name='horse_draw';

	static function getInstance() {
		return new Model_DbTable_HorseDraw();
	}

	function fetchHorseDraws($showid, $horses, $sects) {
		$retsects = array();
		$cnts = array();
		foreach ($horses as $h) {
			$hid = $h['HORSE_ID'];
			$horsearr = $this->fetchOneHorseDraw($showid,$hid,$sects);
			$cnts[$hid] = $horsearr[0];
			$retsects[$hid] = $horsearr[1];
		}
		return array($cnts,$retsects);
	}
	/** input sects are array like so '11-A', '11-B' etc
	 * return is array like so ('11-A'=>'X','11-B'=>'')
	 * */
	private function fetchOneHorseDraw($showid, $horseid, $sects) {
		$db = $this->_db;
		$select = $db->select()
		->from($this->_name)
		->where('show_id=?', $showid)
		->where('horse_id=?',$horseid)
		;

		$rows = $db->fetchAll($select);
		if (count($rows)==0) {
			$draws = array();
			$altdraws = array();
		} else {
			$row = reset($rows);
			$draws = explode(' ',$row['SECT_NAMES']);
			$altdraws = explode(' ',$row['ALT_SECT_NAMES']);
		}

		$ans = array();
		foreach ($sects as $sect) {
			$s = $sect->sectname;
			//$this->logit('$horseid=' . $horseid . '$s=' . $s);
			if (in_array($s,$draws)) {
				$val = 'X';
			} else if (in_array($s,$altdraws)) {
				$val =  'A';
			} else {
				$val = '';
			}
			$ans[$s] = $val;
		}
		return array(count($draws), $ans );
	}

	/** delete all , then re-insert */
	function updateHorseDraw($showid, $data) {
		// first delete existing
		$where = 'SHOW_ID = ' . $showid;
		$this->delete ($where);

		// decode form inputs and insert records in horse_draw table
		//$this->logit('$showid=' . $showid . ' data=' . json_encode($data));
		$arr = explode(',',trim(trim(json_encode($data),'{'),'}'));
		$sectnames = array();
		$altsectnames = array();
		foreach ($arr as $a) {									// "hid4_16-A":"X"
			$pair= explode(':',$a);
			$key = trim($pair[0],'"'); 							// hid4_16-A
			$val = trim($pair[1],'"'); 							// X
			//$this->logit('$key=' . $key . ' $val=' . $val);
			if (''!=$val && 'hid'== substr($key,0,3)) {

				$horse_sect = explode('_', $key);
				$horseid=str_replace('hid','',$horse_sect[0]); // hid4 --> 4
				$sect = $horse_sect[1]; 						// 16-A
				//$this->logit('$$horseid=' . $horseid . ' $$sect=' . $sect . '$$$val=' . $val);
				if ('X' == $val) {
					$sectnames[$horseid] = $sectnames[$horseid] . ' ' . $sect;  // 16-A 14-B
				} else {
					$altsectnames[$horseid] = $altsectnames[$horseid] . ' ' . $sect;  // 16-A 14-B
				}
				//$this->logit('$horseid='.$horseid . '$sectnames= '. $sectnames[$horseid] . ' $altsectnames='. $altsectnames[$horseid]);
			}
		}
		//$this->logit('$altsectnames=' . json_encode($altsectnames));
		// now we know what to insert
		foreach (array_keys($sectnames) as $hid) {
			$altsects = trim($altsectnames[$hid],' ');
			$sects = trim($sectnames[$hid],' ');
			$row = array('SHOW_ID'=>$showid, 'HORSE_ID'=>$hid, 'SECT_NAMES'=>$sects, 'ALT_SECT_NAMES'=>$altsects );
			$this->insert($row);
		}
		foreach (array_keys($altsectnames) as $hid) {
			if (!in_array($hid,array_keys($sectnames))) {

				$altsects = trim($altsectnames[$hid],' ');
				$row = array('SHOW_ID'=>$showid, 'HORSE_ID'=>$hid, 'SECT_NAMES'=>'', 'ALT_SECT_NAMES'=>$altsects );
				$this->insert($row);
			}
		}
	}

	function fetchSectionHorseDraws ($showid) {
		$db = $this->_db;
		$select = $db->select()
		->from($this->_name)
		->join('horses','horses.horse_id=horse_draw.horse_id','HORSE_NAME')
		->where('show_id=?', $showid)
		;
		$map = array();
		$amap = array();
		$rows = $db->fetchAll($select);
		foreach ($rows as $row) {
			$sects = explode(' ', $row['SECT_NAMES']);
			foreach ($sects as $sect) {
				if (''  != $sect) {
					if (! isset($map[$sect])) {
						$map[$sect] = array();
					}
					$map[$sect][] = $row['HORSE_NAME'];
				}
			}
				
			$alters = explode(' ', $row['ALT_SECT_NAMES']);
			foreach ($alters as $alt) {
				if ('' != $alt) {
					if (!isset($amap[$alt])) {
						$amap[$alt] = array();
					}
					$amap[$alt][] = $row['HORSE_NAME'];
				}
			}
		}

		return array($map, $amap);
	}

	
	function logit($xtra) {
		// YYYYmmdd-hhii
		$tstamp = date('Ymd-hi' , time());
		$fname = "c:/aaa/mylog.log" ;
		//mkdir($fname);
		$logme = 'xtra=' . $xtra . ' ,nnnnn ' . ";\n";
		$fh = fopen($fname,'a');

		if (flock($fh, LOCK_EX))
		{
			fwrite($fh, $logme) ;
			flock($fh, LOCK_UN);
		}

		fclose($fh);

	}


}


