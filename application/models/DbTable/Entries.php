<?php
class Model_DbTable_Entries extends Zend_Db_Table_Abstract {
	// name of the table that it will manage
	protected $_name='entries';

	public static function getInstance() {
		return new Model_DbTable_Entries();
	}

	public function deleteAllEntries($showid) {
		$where = 'show_id='  . $showid;
		$this->_db->delete($this->_name, $where);
	}

	public function updateEntriesAll($showid, $formdata) {
		// step 1 - prep formdata for calling insert
		$entries = array();
		$hiddens = array();
		foreach (array_keys($formdata) as $k) {

			$tags=explode('_', $k);
			if (count($tags)==3) { // s_59_2=>X, s_59_22=>''
				$studentid = $tags[1];
				$classid=$tags[2];
				if (!isset($entries[$studentid])) {
					$entries[$studentid] = array();
				}

				$val = $formdata[$k];
				if ('X'==$val) {
					$entries[$studentid][]=$classid;
				}
			} else if (count($tags)==2 && 'h'==$tags[0]) { // h_59=>247
				$studentid = $tags[1];
				$val = $formdata[$k];
				$hiddens[$studentid] = $val;
			}
		}

		// step2 - insert into db
		foreach (array_keys($entries) as $studentid) {
			$entryarr = $entries[$studentid];
			$backnum = $hiddens[$studentid];
			$this->updateEntries($showid, $studentid, $entryarr,$backnum);

		}
	}

	/** delete all , then re-insert */
	public function updateEntries($showid, $studentid, $entryarr, $backnumber) {
		$where = 'STUDENT_ID=' . $studentid . ' AND SHOW_ID=' . $showid;
		$this->delete($where);

		foreach ($entryarr as $e) {
			//$this->logit('$showid=' . $showid . ' $studentid=' . $studentid . ' $classid=' . $e)	;
			$row = array('SHOW_ID'=>$showid, 'STUDENT_ID'=>$studentid, 'CLASS_ID'=>$e, 'BACK_NUMBER'=>$backnumber);
			$this->insert($row);
		}

	}
	public function countEntries ($showid, $classid) {
		$where = 'show_id='  . $showid .  ' AND class_id=' . $classid;
		$rows = $this->fetchAll($where);
		return count ($rows);
	}

	public function countSections ($showid, $classid) {
		$sql = 'SELECT  class_id, count(sect_name) FROM programs , entries where programs.entry_id=entries.entry_id ' .
			' group by entries.show_id, class_id,sect_name ' . 
			' having entries.show_id=' . $showid . ' and entries.class_id= ' . $classid;

		$db = $this->_db;


		$stmt=$db->query($sql);
		$rows = $stmt->fetchAll();
		//$this->logit('countSections $sql=' . $sql . ' $rows=' . json_encode($rows));
		return count ($rows);
	}

	public function hasEntries ($showid) {
		if (!isset($showid)) return false;
		$where = 'show_id='  . $showid;
		$rows = $this->fetchAll($where,null,1);
		$ans = (count($rows) > 0);
		return $ans;
	}

	private function logit($xtra) {
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

	/** used to print program */
	public function fetchEntries ($showid) {
		$db = $this->_db;
		$select = $db->select()->from('entries')
		->join('students','entries.student_id=students.student_id',array('STUDENT_ID','FIRST_NAME','LAST_NAME','TEAM_ID'))
		->join('teams','students.team_id=teams.team_id','TEAM_NAME')
		->join('classes','classes.class_id=entries.class_id', array('LEVEL','CLASS_NAME'))
		->where('show_id=?', $showid)
		->order('TEAM_NAME')
		->order('LAST_NAME')
		;

		$rows = $db->fetchAll($select);

		//		print_r($rows);
		return $rows;
	}

	public function fetchClassEntries ($classid, $showid) {
		$db = $this->_db;
		$select = $db->select()->from('entries')
		->join('students','entries.student_id=students.student_id',array('FIRST_NAME','LAST_NAME'))
		->join('teams','students.team_id=teams.team_id','TEAM_NAME')
		->where('show_id=?', $showid);
		if ($classid>0) {
			$select = $select->where('class_id=?', $classid);
		}
		$rows = $db->fetchAll($select);
		return $rows;
	}
	/**
	 * for this student and this show return array of entries
	 * each elem is structure with properties: classid, classname and checked */
	//	public function fetchStudentEntries($studentId, $showId) {
	//		// get all possible classes for ths show type
	//		$bb = new Model_DbTable_Shows();
	//		$show= $bb->getShow($showId);
	//		$cc = new Model_DbTable_Classes();
	//		$classes = $cc->fetchClasses($show['EVENT_TYPE_ID']);
	//
	//		$ans = array();
	//		foreach ($classes as $row) {
	//			$classid = $row->id;
	//			$entry = new stdClass();
	//			$entry->classid = $classid;
	//			$entry->level = $row->level;
	//			$entry->checked = '';
	//			$entry->classname = $row->name;
	//			$entry->backnumber= '';
	//			$ans[$classid]	= $entry;
	//		}
	//		//$this->logit('BEFORE student rows ..' . json_encode($ans));
	//
	//		// get entries for this student for this show
	//		$where = 'STUDENT_ID=' . $studentId . ' AND SHOW_ID=' . $showId;
	//		$myrows = $this->fetchAll($where);
	//		//$this->logit('where=' . $where . ' FETCHED student rows ..' . count($myrows));
	//
	//		$backnumber ='';
	//		foreach ($myrows as $row)		{
	//			$classid = $row['CLASS_ID'];
	//			//$this->logit('in loop classid=' . $classid);
	//			if (isset($row['BACK_NUMBER'])) {
	//				$backnumber = $row['BACK_NUMBER'];
	//			}
	//
	//			$ans[$classid]->checked = 'checked';
	//		}
	//
	//		// set backnumber into every element
	//
	//		if ("" == trim ($backnumber) ) {
	//			$model = new Model_DbTable_Students();
	//			$stud = $model->getStudent($studentId);
	//			$backnumber = $stud['BACK_NUMBER'];
	//		}
	//		if ($backnumber==0) {
	//			$backnumber = '';
	//		}
	//		foreach ($ans as $entry) {
	//			$entry->backnumber = $backnumber;
	//		}
	//
	//		return $ans;
	//	}

	/** this is for displaying entry grid */
	function fetchTeamEntries($showid, $students, $clazzes) {
		$entries = $this->fetchAll('show_id=' . $showid)->toArray();
		$ans=array();
		foreach ($students as $s) {
			$sid= $s['STUDENT_ID'];
			foreach ($clazzes as $c) {
				$val = '';
				foreach ($entries as $row) {
					if ($row['STUDENT_ID']==$sid && $row['CLASS_ID']== $c->id) {
						$val = 'X';
					}
				}
				$ans[$sid][$c->id] = $val;
			}
		}

		return $ans;
	}

	/** for confirm page */
	public function fetchStudentEntries($showid, $oneteamid) {
		$select = $this->_db->select()
		->from('entries')
		->join('show_team','show_team.show_id=entries.show_id','TEAM_ID')
		->join('teams','teams.team_id=show_team.team_id','TEAM_NAME')
		->join('user','teams.user_id=user.user_id','EMAIL')
		->where('entries.show_id=?', $showid)

		->order('TEAM_NAME')
		;

		// array of teams
		$teams = array();
		foreach ( $this->_db->fetchAll($select) as $row) {
			$teamid = $row['TEAM_ID'];
			if ($teamid == $oneteamid || $oneteamid==0) {
				if (!isset($teams[$teamid])) {
					$teamstruct= new stdClass();
					$teamstruct->teamid = $teamid;
					$teamstruct->teamname=$row['TEAM_NAME'];
					$teamstruct->coachemail = $row['EMAIL'];
					$teamstruct->riders = array();
					$teams[$teamid] = $teamstruct;
				}
			}
		}
		// array of riders
		$select = $this->_db->select()
		->from('entries')
		->join('students','students.student_id=entries.student_id',array('FIRST_NAME','LAST_NAME','TEAM_ID'))
		->join('classes','classes.class_id=entries.class_id',array('LEVEL','CLASS_NAME'))
		->where('entries.show_id=?', $showid)
		->order('LAST_NAME')
		;
		foreach ( $this->_db->fetchAll($select) as $row) {
			$teamid = $row['TEAM_ID'];
			$studentid = $row['STUDENT_ID'];
			if (isset($teams[$teamid])) {
				$teamstruct = $teams[$teamid];
				if (!isset($teamstruct->riders[$studentid])) {
					$riderstruct = new stdClass();
					$riderstruct->ridername = $row['FIRST_NAME'] . ' ' . $row['LAST_NAME'];
					$riderstruct->classes = $row['LEVEL'] . ' - ' . $row['CLASS_NAME'];
					$teamstruct->riders[$studentid] = $riderstruct;
				} else {
					$riderstruct  = $teamstruct->riders[$studentid];
					$riderstruct->classes = $riderstruct->classes . ', ' . $row['LEVEL'] . ' - ' . $row['CLASS_NAME'];
				}
			}
		}
		return $teams;

	}
	function splitClass ($showid, $classid, $cnt) {

		if ((int)$cnt<=0) return null;
		// get all entries for this class
		$db = $this->_db;
		$select = $db->select()->from('entries')
		->join('students','entries.student_id=students.student_id','team_id')
		->where('show_id=?', $showid)->where('class_id=?', $classid);

		$entries = $db->fetchAll($select);

		// group entries by team
		foreach ($entries as $entry) {
			$t = $entry['team_id'];
			if (! isset($groups[$t])) {
				$groups[$t] = array();
			}
			array_push($groups[$t], $entry['ENTRY_ID']);
		}

		$sections = $this->shuffleGroups($cnt, array_values($groups));

		$prog = new Model_DbTable_Programs();
		//$zz=json_encode($sections);
		//$this->logit($zz);
		$prog->insertPrograms($showid, $classid, $sections);
		//return $sections;
	}


	/**
	 * split entries into N buckets, distributing each group evenly
	 * $cnt = number of resulting buckets
	 * $groups = array of arrays of entries
	 *
	 * returns : array of $cnt buckets, each bucket an array of entries
	 * sanity check =
	 * count of items in all buckets must equal count of items in all groups
	 * example:
	 $t1 = array(5 , 6, 7, 8);
	 $t2 = array('a','b','c');
	 $t3 = array(11,12,18,15,19);
	 $groups = array($t1, $t2, $t3);

	 $buckets = shuffleGroups(3, $groups);
	 [0] => Array
	 (
	 [0] => 19
	 [1] => 12
	 [2] => 7
	 [3] => c
	 )

	 [1] => Array
	 (
	 [0] => 15
	 [1] => 11
	 [2] => 6
	 [3] => b
	 )

	 [2] => Array
	 (
	 [0] => 18
	 [1] => 8
	 [2] => 5
	 [3] => a
	 )

	 * */
	function shuffleGroups ($cnt, $groups ) {
		// create empty buckets
		for ( $jj =0; $jj<$cnt; $jj++) {
			$buckets[$jj] = array();
		}
		// sort groups by largest number of items
		usort ($groups, 'mycmp');

		// main loop
		$ptr = 0;

		foreach ($groups as $group) {

			while (true ) {
					
				$item = array_pop($group); // remove item from group
				if ($item == null) break;
					
				array_push($buckets[$ptr], $item); // put item in current bucket
					
				if ($ptr == $cnt-1) { // next iteration buckets indexing
					$ptr = 0;
				} else {
					$ptr ++;
				}
					
			} // end while (for one group)

		} // end main loop

		return $buckets;
	}


}
/** compare two arrays, based on the larger element count */
function mycmp ($aa, $bb) {
	$cnta = count($aa);
	$cntb = count($bb);

	if ($cnta == $cntb)
	$ans =0;
	else if ($cnta > $cntb)
	$ans = -1;
	else
	$ans = 1;
	return $ans;
}

