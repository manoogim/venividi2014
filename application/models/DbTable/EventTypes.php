<?php
class Model_DbTable_EventTypes extends Zend_Db_Table_Abstract {
	// name of the table that it will manage
	protected $_name='event_type';

	static function getInstance() {
		return new Model_DbTable_EventTypes();
	}
	public function fetchEventType($id) {
		$id = (int)$id;
		$row = $this->fetchRow('event_type_id = ' . $id);
		if (!$row) {
			throw new Exception("Count not find row $id");
		}
		return $row->toArray();
	}
	public function fetchEventTypes() {
		$where = 'EVENT_TYPE_ID>0';
		$ans = $this->fetchAll($where);

			
		return $ans;
	}


}


