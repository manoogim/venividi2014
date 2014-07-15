<?php
class Model_DbTable_Lookup extends Zend_Db_Table_Abstract {
		// name of the table that it will manage
	protected $_name='lookups';
	public static function getInstance() {
		return new Model_DbTable_Lookup();
	}
	public function fetchLookups($table) {

		$db = $this->_db;
		$select = $db->select()
		->from('lookups')
		->where('LOOKUP_TABLE=?', $table);
		;
		$rows = $db->fetchAll($select);

		
		$ans = array();
		 foreach ($rows as $row) {
		 	
		 	$id = $row['LOOKUP_NAME'];
		 	$name = $row['LOOKUP_LABEL'];
		 	$ans[$id] = $name;
		 }
	
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
}


