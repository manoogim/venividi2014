<?php
class Model_DbTable_Students extends Zend_Db_Table_Abstract {
	// name of the table that it will manage
	protected $_name='students';
	//public $ownerId;

	public function getStudent($id) {
		$id = (int)$id;
		$row = $this->fetchRow('student_id = ' . $id);
		if (!$row) {
			throw new Exception("Count not find row $id");
		}
		return $row->toArray();
	}

	public function addStudent($data  ) {
		if (''==$data['back_number']) {
			$data['back_number'] = null;
		}
		if (''==$data['class1_id']) {
			$data['class1_id'] = null;
		}
		if (''==$data['class2_id']) {
			$data['class2_id'] = null;
		}

		$this->insert($data);
		$studentid = $this->_db->lastInsertId($this->_name,'STUDENT_ID');
		return $studentid;
	}


	public function updateStudent($studentId, $data) {
		if (''==$data['back_number']) {
			$data['back_number'] = null;
		}
		if (''==$data['class1_id']) {
			$data['class1_id'] = null;
		}
		if (''==$data['class2_id']) {
			$data['class2_id'] = null;
		}

		$this->update($data, 'student_id = '. (int)$studentId);
	}

	public function deleteStudent($studentId)
	{
		$this->delete('student_id =' . (int)$studentId);
	}

	public function fetchStudents($teamId) {
		$db = $this->_db;
		$select = $db->select()
		->from('students')
		->join('teams','students.team_id=teams.team_id',array('TEAM_NAME','EVENT_TYPE_ID','USER_ID'))
		->order( 'LAST_NAME ASC');
		if ($teamId>0) {
			$select = $select->where('students.team_id=?',$teamId);
		} else {
			$auth = Zend_Auth::getInstance();
			$userid = $auth->getIdentity()->USER_ID;
			if ($userid > 0) {
				$select = $select->where('teams.user_id=?', $userid);
			} else {
				$select = $select->where('students.team_id=?',-1);
			}
		}

		$students = $db->fetchAll($select);
		$classes=array();
		if (count($students)>0) {
			$eventtypeid = $students[0]['EVENT_TYPE_ID'];
			$c = new Model_DbTable_Classes();
			$classes = $c->fetchClasses($eventtypeid);
		}

		//		print_r($classes);
		$ans = array();
		foreach ($students as $s) {
			$cls = '';
			if (isset($s['CLASS1_ID'])) {
				$cls = $this->_formatClassname($classes, $s['CLASS1_ID']);
			}
			if (isset($s['CLASS2_ID'])) {
				$cls = $cls . ', ' . $this->_formatClassname($classes, $s['CLASS2_ID']);
			}

			$s['cls'] = $cls;
			$ans[] = $s;
		}

		return ($ans);
	}

	private function _formatClassname($classes, $classid) {
		foreach ($classes as $cls) {
			if ($cls->id == $classid) {
				$str = $cls->level . '-' . $cls->name;
				return $str;
			}
		}
	}

	public function hateTyping($typeid, $teamid,$hugetext) {
		$cm  = new Model_DbTable_Classes();
		$classes = $cm->fetchClasses($typeid);
		$lines = split("\n", $hugetext);
		$nn = count($lines);
		if ($nn>50) $nn=50;
		for ($jj=0; $jj<$nn; $jj++) {
			$this->_makestudentrecord($classes, $teamid, $lines[$jj]);
			//			$this->logit('$$%%%='. $line);
		}
	}

	private function _makestudentrecord ($classes, $teamid, $line) {
		$tokens = split(',', $line);
		// lets assume : fname, lname, cls1, cls2

		foreach ($tokens as $tok) {
			$ttok = trim(trim( trim($tok,'"'), "'")) ;
			if ($ttok == '') continue;
				
			if (is_numeric($ttok) && !isset($fname)) continue;
				
			if (!isset($fname) ) {
				$fname = $ttok;
				continue;
			}
				
			if (!isset($lname)) {
				$lname = $ttok;
				continue;
			}

			if (!isset($c2)) {
				$c2 = $ttok;
				continue;
			}
				
			if (!isset($c1)) {
				$c1 = $ttok;
				continue;
			}

		}
		if (!isset($fname) || !isset($lname)) {
			return; // no sense
		}

		foreach ($classes as $onec) {
			//			$this->logit(json_encode($onec));
			if ($onec->level == $c1) {
				if ($onec->ctype==1)	{
					$flat =$onec->id;
				} else {
					$fence = $onec->id;
				}
			}
			if ($onec->level == $c2) {
				if ($onec->ctype==1)	{
					$flat = $onec->id;;
				} else {
					$fence = $onec->id;
				}
			}
		}
		//		$this->logit($fname . ' ' . $lname . ' $$flat=' . $flat . ',$fence=' . $fence);
						$data = array(
					'first_name' => $fname,
					'last_name' => $lname,
					'team_id' => $teamid,
					'back_number' => null,
					'class1_id' => $flat,
					'class2_id' => $fence,
				);
		$this->addStudent($data);


	}

	private function logit($xtra) {
		// YYYYmmdd-hhii
		$tstamp = date('Ymd-hi' , time());
		$fname = "c:/aaa/mylog.log" ;
		//mkdir($fname);
		$logme = '==>' . $xtra . '<==' . ";\n";
		$fh = fopen($fname,'a');

		if (flock($fh, LOCK_EX))
		{
			fwrite($fh, $logme) ;
			flock($fh, LOCK_UN);
		}

		fclose($fh);

	}
}