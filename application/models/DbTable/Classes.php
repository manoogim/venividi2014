<?php
class Model_DbTable_Classes extends Zend_Db_Table_Abstract {
		// name of the table that it will manage
	protected $_name='classes';

	public function fetchClass($id) {
		$id = (int)$id;
		$row = $this->fetchRow('class_id = ' . $id);
		if (!$row) {
			throw new Exception("Count not find row $id");
		}
		return $row->toArray();
	}
	

	
	public function fetchClasses($eventTypeId) {
		if ($eventTypeId > 0 ) {
			$rows = $this->fetchAll('EVENT_TYPE_ID=' . $eventTypeId,'LEVEL ASC');
		} else {
			$rows = $this->fetchAll();
		}
		
		$ans = array();
		 foreach ($rows as $row) {
		 	$clazz = new stdClass();
		 	$clazz->id=$row['CLASS_ID'];
		 	$clazz->level = $row['LEVEL'];
		 	$clazz->name = $row['CLASS_NAME'];
		 	$clazz->ctype = $row['CLASS_TYPE_ID'];
		 	$clazz->entrycnt = '';
		 	$ans[] = $clazz;
		 }
		 return $ans;
	}

	public function fetchClassesInOrder( $showid) {
		$s = new Model_DbTable_Shows();
		$ord_arr = $s->getClassOrder($showid);
		$show = $s->getShow($showid);
		
		$classes = $this->fetchClasses($show['EVENT_TYPE_ID']);
		$myclasses = array();
		foreach ($classes as $c) {
			$myclasses[$c->level] = $c->name;
		}
		
		$ans = array();
		foreach ($ord_arr as $ord) {
			$cc = new stdClass();
			$oo = str_replace(" ",'', $ord);
			$cc->level = $oo;
			$cc->name =  $myclasses[$oo];
			$ans[] = $cc;
		}

		return $ans;
	}
	
	public function fetchClassesAndCounts( $showid) {
		$s = new Model_DbTable_Shows();
		$show = $s->getShow($showid);
		$ord_arr = explode(',',$show['CLASS_ORDER']);

		$classes = $this->fetchClasses($show['EVENT_TYPE_ID']);
		$entrymodel = new Model_DbTable_Entries();
		foreach ($classes as $c) {
			$c->entrycnt = $entrymodel->countEntries($showid,$c->id);
			$c->sectcnt = $entrymodel->countSections($showid,$c->id);
			$c->ord = array_search($c->level, $ord_arr);
		}
		// order by desired order for this show
		usort($classes,'order_clazz');

		return $classes;
	}
	
	/** 
	 $rowclicked = a number from 1 to N (number of classes) 
	 positive means move up, negative means move down
	 so 3 means permute classes 2 and 3, -3 means permute classes 3 and 4 
	 * */
	public function permuteClasses ($showid, $rowclicked) {
		$classes = $this->fetchClassesAndCounts($showid);
		$nn = count($classes);
		$idx = abs($rowclicked);
		if ($rowclicked<= 0) {
			$idxa = $idx;
			$idxb = $idx + 1;
		} else {
			$idxa = $idx -1;
			$idxb = $idx;
		}
		if ($idxa < 0 || $idxb > $nn) {
			$idxa = 0;
			$idxb = 0;
		}
		
		// sort the classes and extract the field to update the show field
		$this->_permute ($classes[$idxa], $classes[$idxb]);
		usort($classes,'order_clazz');
		
		$showval = $classes[0]->level;
		for ($jj=1; $jj<$nn; $jj++) {
			$showval = $showval  . ',' . $classes[$jj]->level ;
		}
		
		// update show
		$s = new Model_DbTable_Shows();
		$s->update(array('CLASS_ORDER'=>$showval),'show_id=' . $showid);
		
		// same classes
		return  $classes;
	}
	
	private function _permute ($clsa, $clsb) {
		$tmp = $clsa->ord;
		$clsa->ord = $clsb->ord;
		$clsb->ord = $tmp;
		
	}
}

function order_clazz($c1, $c2) {
	
	$l1 = $c1->ord;
	$l2 = $c2->ord;
	if ($l1 < $l2) return -1;
	else if ($l1 > $l2) return 1;
	else return 0;
}
