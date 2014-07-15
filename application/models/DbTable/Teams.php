<?php
class Model_DbTable_Teams extends Zend_Db_Table_Abstract {
	// name of the table that it will manage
	protected $_name='teams';

	public function fetchTeam($id) {
		$id = (int)$id;
		$row = $this->fetchRow('team_id = ' . $id);
		if ($row) {
			return $row->toArray();
		}
	}

	public function addTeam($data) {
//		$data = array(
//				'user_id' => $userid,
//				'team_name' => $teamname,
//				'event_type_id' => $teamtype,
//				'zone_id' => $zoneid,
//				'rgn_id' => $rgnid,
//
//		);
		$this->insert($data);
		$teamid = $this->_db->lastInsertId($this->_name,'TEAM_ID');
		// invite to all non-closed shows of the same type and same zone, same rgn
		$db = $this->_db;
		$select = $db->select()
		->from('shows')
		->where('event_type_id=?', $data['event_type_id'])
		->where('invited_zone=?', $data['zone_id'])
		->where('invited_rgn=?',$data['rgn_id'])
		;
		$model = new Model_DbTable_ShowTeams();
		$shows = $db->fetchAll($select);
		foreach ($shows as $show) {			
			if (!Model_DbTable_Shows::isAfterToday_ymd($show['CLOSE_DATE'])) {
				Model_DbTable_ShowTeams::getInstance()->inviteOne($show['SHOW_ID'], $teamid);
			}
		}
		return $teamid;
	}

	public function updateTeam($teamId, $data) {
//		$data = array(
//				'team_name' => $teamname,
//				'event_type_id' => $teamtype,
//				'user_id' => $userid,
//				'zone_id' => $zoneid,
//				'rgn_id' => $rgnid,				
//		);
		$this->update($data, 'team_id = '. (int)$teamId);
	}

	public function deleteTeam($teamId)
	{
		$this->delete('TEAM_ID =' . (int)$teamId);
	}

	private function logit($xtra) {
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
	public function fetchTeams() {
		$db = $this->_db;
		$select = $db->select()
		->from('teams')
		->join('user','user.user_id=teams.user_id',array('FIRST_NAME','LAST_NAME','EMAIL'))
		->order('TEAM_NAME');

		$ans = $db->fetchAll($select);
		return $ans;
	}

	public function fetchZoneTeams($zoneid, $rgnid) {
		$db = $this->_db;
		$select = $db->select()
		->from('teams')
		->where('zone_id=?', $zoneid)
		->where('rgn_id=?', $rgnid);
		$ans = $db->fetchAll($select);
		return $ans;
	}
	


}


