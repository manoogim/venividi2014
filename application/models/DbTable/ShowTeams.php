<?php
class Model_DbTable_ShowTeams extends Zend_Db_Table_Abstract {
		// name of the table that it will manage
	protected $_name='show_team';

	static function getInstance() {
		return new Model_DbTable_ShowTeams();
	}
	
	/** delete all , then re-insert */
	function inviteZones($showid,$eventtypeid, $zones, $rgns) {
		$where = 'SHOW_ID = ' . $showid;
		$this->delete ($where);

		$model = new Model_DbTable_Teams();
		foreach ($zones as $zoneid) {
			foreach ($rgns as $rgnid) {
				$teams = $model->fetchZoneTeams($zoneid, $rgnid);
				foreach ($teams as $team) {
					//$this->logit('EVENT_TYPE_ID=' . $team['EVENT_TYPE_ID'] . ' '. $team['TEAM_NAME']);
					if ($eventtypeid==$team['EVENT_TYPE_ID']) {						
						$this->inviteOne($showid, $team['TEAM_ID']);
					}
				}
			}
		}
	}
	
	function inviteOne ($showid, $teamid) {
		$row = array('SHOW_ID'=>$showid, 'TEAM_ID'=>$teamid);
		$this->insert($row);
	}
	
	/** in show detail page, decide to display or not display [register now] link */
	function fetchInvitedTeamsUser($userid, $showid) {
		$db = $this->_db;
		$select = $db->select()->from($this->_name)
		->join('teams','teams.team_id=show_team.team_id','TEAM_NAME')
		->where('user_id=?', $userid)
		->where('show_id=?', $showid)
		;
		$rows = $db->fetchAll($select);
		return $rows;
	}
	
	/** used  for email lists */
	function fetchInvitedTeamsAll($showid) {
		$db = $this->_db;
		$select = $db->select()->from($this->_name)
		->join('teams','teams.team_id=show_team.team_id',array('TEAM_NAME','USER_ID'))
		->join('user','user.user_id=teams.user_id',array('FIRST_NAME','LAST_NAME','EMAIL'))
		->where('show_id=?', $showid)
		->order('TEAM_NAME')
		;
		$rows = $db->fetchAll($select);
		return $rows;
	}
	/** used in reports: pointcard, account, and cover, */
	function fetchInvitedTeamsEntered($showid) {
		$db = $this->_db;
		$select = $db->select()->from($this->_name)
		->join('teams','teams.team_id=show_team.team_id',array('TEAM_NAME','USER_ID'))
		->join('user','user.user_id=teams.user_id',array('FIRST_NAME','LAST_NAME','EMAIL'))
		->where('show_id=?', $showid)
		->order('TEAM_NAME')
		;
		$rows1 = $db->fetchAll($select);
		
		/** these teams have actual entries in the show */
		$select2 = $db->select()->from('entries')
		->join('students', 'students.student_id=entries.student_id','TEAM_ID')
		->where('show_id=?', $showid);		
		$rows2 = $db->fetchAll($select2);
		
		$rows = array();
		foreach ($rows1 as $row1) {
			$teamid = $row1['TEAM_ID'];
			foreach ($rows2 as $row2) {
				if ($teamid == $row2['TEAM_ID']) {
					$rows[] = $row1;
					break;
				}
			}
		}
		
		return $rows;
	}
	/** used on team index page to display or not display the link to enter show */
	function fetchInvitedTeamIds($showid) {
		$db = $this->_db;
		$select = 
		$db->select()
		->from($this->_name)
		->where('show_id=?', $showid)
		;
		$rows = $db->fetchAll($select);
		$ans = array();
		foreach ($rows as $row) {
			$ans[] = $row['TEAM_ID'];
		}
		return $ans;
	}	
	

	static public function isInvited($teamid, $showid) {
		$model = new Model_DbTable_ShowTeams();
		$rows = $model->fetchAll('TEAM_ID=' . $teamid . ' AND SHOW_ID=' . $showid);
		return count($rows) > 0;
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


