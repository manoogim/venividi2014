<?php
class Model_DbTable_Programs extends Zend_Db_Table_Abstract {
	// name of the table that it will manage
	protected $_name='programs';


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

	/** for each entry, delete previous then insert new */
	function insertPrograms($showid, $classid, $entries) {
		$ms = new Model_DbTable_Shows();
		$showdet = $ms->getShow($showid);
		$classordar = split(',',$showdet['CLASS_ORDER']);
		
		$mc = new Model_DbTable_Classes();
		$cls = $mc->fetchClass($classid);
		

		$letters = range('A','Z');
		$idx=0;
		foreach ($entries as $section) {

			$sectname = $cls['LEVEL'] . '-' . $letters[$idx++];
			
			foreach ( $section as $entry) {
				// first delete existing
				$this->delete('entry_id=' . $entry);

				$data = array(
					'show_id' => $showid,
					'sect_name' => $sectname,				
					'entry_id' => $entry,
				);

				$this->insert($data);
				
			}
		}

	}

	/** 1-to print programs and 2-to display in programs web page */
	function fetchPrograms($showid, $classid) {
		$db = $this->_db;
		$select = $db->select()->from('programs')
		->join('entries','programs.entry_id=entries.entry_id','BACK_NUMBER')
		->join('classes','classes.class_id=entries.class_id',array('CLASS_ID','CLASS_NAME','LEVEL'))
		->join('students','students.student_id=entries.student_id',array('FIRST_NAME','LAST_NAME'))
		->join('teams','teams.team_id=students.team_id','TEAM_NAME')
		->where('entries.show_id=?',$showid)
		->order('SECT_NAME')
		->order('LAST_NAME')
		;
		if ($classid > 0) {
			$select = $select->where('entries.class_id=?',$classid);
		}
		$rows= $db->fetchAll($select);
		// what will be the order of classes
		$shows = new Model_DbTable_Shows();
		$show = $shows->getShow($showid);
		$ord_arr = explode(',',$show['CLASS_ORDER']);
		
		// make associative arrays
		$arr = array();
		foreach ($rows as $row) {
			$sect = $row['SECT_NAME'];
			if (!isset($arr[$sect])) $arr[$sect]= array();

			$myentry = new stdClass();
			$myentry->sectname = $sect;
			$myentry->clazzname = $row['CLASS_NAME'];
			$myentry->backnumber = $row['BACK_NUMBER'];
			$myentry->firstname = $row['FIRST_NAME'];
			$myentry->lastname = $row['LAST_NAME'];
			$myentry->teamname = $row['TEAM_NAME'];
			$myentry->ord = array_search($row['LEVEL'], $ord_arr);
			array_push($arr[$sect], $myentry);

		}
	
		uasort($arr, 'order_sects');
	
		return $arr;
	}

	/** used in horse usage web page for top row across */
	/** also in judges cards report */
	/** also to create empty draw sheets */
	public function fetchSections($showid) {
		
		$db = $this->_db;
		$select = $db->select()->from('programs')
		->join('entries','programs.entry_id=entries.entry_id')
		->join('classes','classes.class_id=entries.class_id',array('CLASS_NAME','CLASS_TYPE_ID'))
		->join('class_type','classes.class_type_id=class_type.class_type_id','CLASS_TITLE')
		->where('entries.show_id=?',$showid)
		->order('SECT_NAME ASC')
		;
		$rows= $db->fetchAll($select);

		// what will be the order of sections
		$shows = new Model_DbTable_Shows();
		$show = $shows->getShow($showid);
		$ord_arr = explode(',',$show['CLASS_ORDER']);
		
		$arr = array();
		foreach ($rows as $row) {
			$sect = $row['SECT_NAME'];
			if (!isset($arr[$sect])) {
				$arr[$sect]= new stdClass();
				$mysect = $arr[$sect];
				$mysect->cnt = 0;
				$mysect->classname = $row['CLASS_NAME'];
				$mysect->classtype = $row['CLASS_TITLE'];
				$mysect->classtypeid = $row['CLASS_TYPE_ID'];
			}
			$mysect = $arr[$sect];
			$mysect->sectname = $sect;
			$mysect->cnt ++;
			$tags = explode('-',$sect);
			$mysect->ord = array_search($tags[0], $ord_arr);
			
		}
		uasort($arr, 'order_sect');

		return $arr;
	}

	public function fetchClassName ($showid, $sectname) {
		$db = $this->_db;
		$select = $db->select()->from('programs')
		->join('entries','programs.entry_id=entries.entry_id')
		->join('classes','classes.class_id=entries.class_id','CLASS_NAME')
		->where('entries.show_id=?',$showid)
		->where('sect_name=?', $sectname)
		;
		//$this->logit($select);
		$rows= $db->fetchAll($select);
		if (count($rows)>0) {
			$row = next($rows);
			$classname = $row['CLASS_NAME'];
		} else {
			$classname='unknown';
		}
		return $classname;
	}
	


}
//	function logme($xtra) {
//		// YYYYmmdd-hhii
//		$tstamp = date('Ymd-hi' , time());
//		$fname = "c:/aaa/mylog.log" ;
//		//mkdir($fname);
//		$logme = 'xtra=' . $xtra . ' ,nnnnn ' . ";\n";
//		$fh = fopen($fname,'a');
//
//		if (flock($fh, LOCK_EX))
//		{
//			fwrite($fh, $logme) ;
//			flock($fh, LOCK_UN);
//		}
//
//		fclose($fh);
//
//	}
function order_sects($s1_arr, $s2_arr) {

	$s1 = $s1_arr[0];
	$s2 = $s2_arr[0];
	if      ($s1->ord < $s2->ord) $ans = -1;
	else if ($s1->ord > $s2->ord) $ans = 1;
	else if ($s1->sectname < $s2->sectname) $ans =-1;
	else if ($s1->sectname > $s2->sectname) $ans =1;
	else $ans = 0;
	return $ans;
}

function order_sect($s1, $s2) {
	if      ($s1->ord < $s2->ord) $ans = -1;
	else if ($s1->ord > $s2->ord) $ans = 1;
	else if ($s1->sectname < $s2->sectname) $ans =-1;
	else if ($s1->sectname > $s2->sectname) $ans =1;
	else $ans = 0;
	return $ans;
}

