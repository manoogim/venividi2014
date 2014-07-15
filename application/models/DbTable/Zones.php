<?php
class Model_DbTable_Zones extends Zend_Db_Table_Abstract {
		// name of the table that it will manage
	protected $_name='zones';
	
	public static function getInstance() {
		return new Model_DbTable_Zones();
	}
	public function fetchZones() {

		$rows = $this->fetchAll();
		
		
		$ans = array();
		 foreach ($rows as $row) {
		 	$id = $row['ZONE_ID'];
		 	$name = $row['ZONE_NAME'];
		 	$ans[$id] = $name;
		 }
		 return $ans;
	}

}


