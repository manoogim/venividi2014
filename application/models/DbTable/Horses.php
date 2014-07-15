<?php
class Model_DbTable_Horses extends Zend_Db_Table_Abstract {
	// name of the table that it will manage
	protected $_name='horses';

	public function fetchHorse($id) {
		$id = (int)$id;
		$row = $this->fetchRow('horse_id = ' . $id);
		if (!$row) {
			throw new Exception("Count not find row $id");
		}
		return $row->toArray();
	}

	public function addHorse($userid, $horsename, $desc, $wlimit, $hlimit, $barn,
	$breed, $color, $visina, $gender, $crop, $spurs) {
		$data = array(
				'user_id' => $userid,
				'horse_name' => $horsename,
				'horse_desc' => $desc,
				'weight_limit' => $wlimit == '' ? null : $wlimit,
				'height_limit' => $hlimit == '' ? null : $hlimit,
				'barn' => $barn,
				'breed' => $breed,
				'color' => $color,
				'height' => $visina,
				'gender' => $gender,
				'crop' => $crop,
				'spurs' => $spurs

		);
		$this->insert($data);
	}

	public function updateHorse($horseid, $userid, $horsename, $desc, $wlimit, $hlimit, $barn,
	$breed, $color, $visina, $gender, $crop, $spurs) {
		$data = array(
				'user_id' => $userid,
				'horse_name' => $horsename,
				'horse_desc' => $desc,
				'weight_limit' =>  $wlimit,
				'height_limit' =>  $hlimit,
				'barn' => $barn,
				'breed' => $breed,
				'color' => $color,
				'height' => $visina,
				'gender' => $gender,
				'crop' => $crop,
				'spurs' => $spurs
		);
		$this->update($data, 'horse_id = '. (int)$horseid);
	}

	public function deleteHorse($horseid)
	{
		$this->delete('HORSE_ID =' . (int)$horseid);
	}

	function logit($xtra) {
		// YYYYmmdd-hhii
		$tstamp = date('Ymd-hi' , time());
		$fname = "c:/aaa/mylog.log" ;
		//mkdir($fname);
		$logme = '===' . $xtra . ' ,=== ' . ";\n";
		$fh = fopen($fname,'a');

		if (flock($fh, LOCK_EX))
		{
			fwrite($fh, $logme) ;
			flock($fh, LOCK_UN);
		}

		fclose($fh);

	}
	/** NOT USED mtm sep-1 2010
	// all horses whose owner has a team who was invited to the given show (for drawgrid)
	// or horses owned by this user
	public function fetchHorsesShow($showid,$userid) {

		if (isset($showid)) {
			$db = $this->_db;
			$select = $db->select()
			->from('horses')
//			->join('user','horses.user_id=user.user_id',array('FIRST_NAME','LAST_NAME'))
//			->join('teams','teams.user_id=user.user_id','TEAM_NAME')
//			->where('horses.user_id in (select user_id from teams where team_id in (select team_id from show_team where show_id=' . $showid  . '))')
			->where('user_id=?', $userid)
			->order(array('BARN' , 'HORSE_NAME'));

			$ans = $db->fetchAll($select);
			return $ans;
		}
	} */
	// all horses for given user
	public function fetchHorsesUser($userid) {
		$db = $this->_db;
		$select = $db->select()
		->from('horses')
		->where('horses.user_id=? ', $userid)
		->order('barn')
		->order('HORSE_NAME');

		$ans = $db->fetchAll($select);
		return $ans;
	}

	/** NOT USED mtm sep-1 2010
	// alll horses which are on at least one draw sheet (descriptions)
	public function fetchHorsesDraw($showid) {
		$db = $this->_db;
		$select = $db->select()
		->from('horses')
//		->join('user','horses.user_id=user.user_id')
//		->join('teams','teams.user_id=user.user_id','TEAM_NAME')
		->where('horses.horse_id in (select horse_id from horse_draw where show_id =' . $showid .')')
		->order(array('BARN' , 'HORSE_NAME'));

		$ans = $db->fetchAll($select);
		return $ans;
	}
*/


}


