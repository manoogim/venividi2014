<?php
class Model_DbTable_Regions extends Zend_Db_Table_Abstract {
		// name of the table that it will manage
	protected $_name='regions';
	public static function getInstance() {
		return new Model_DbTable_Regions();
	}
	public function fetchRegions() {

		$rows = $this->fetchAll();
		
		
		$ans = array();
		 foreach ($rows as $row) {
		 	$id = $row['RGN_ID'];
		 	$name = $row['RGN_NAME'];
		 	$ans[$id] = $name;
		 }
		 return $ans;
	}

}


