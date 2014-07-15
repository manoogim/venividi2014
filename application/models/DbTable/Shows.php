<?php

class Model_DbTable_Shows extends Zend_Db_Table_Abstract {
	// name of the table that it will manage
	protected $_name='shows';
	//public $ownerId=1;

	public static function getInstance() {
		return new Model_DbTable_Shows();
	}


	public function copyShow($showid)
	{
		$show = $this->getShow($showid);
		unset($show['SHOW_ID']);
		$newshowid =$this->addShow($show);

		return $newshowid;
	}

	public function getClassOrder($showid) {
		$s = new Model_DbTable_Shows();
		$show = $s->getShow($showid);
		$ord_arr = explode(',',$show['CLASS_ORDER']);
		return $ord_arr;
	}

	public function getShow($id) {
		$id = (int)$id;
		$row = $this->fetchRow('show_id = ' . $id);
		if ($row) {
			$ans= $row->toArray();
			return $ans;
		}

	}


	public function addShow($data) {

		$this->insert($data);
		$showid = $this->_db->lastInsertId($this->_name,'SHOW_ID');
		$this->selectShow($showid);

		// invite teams
		$this->_inviteTeams($showid,$data);
		return $showid;
	}


	public function updateShow($showid, $data) {
		$this->update($data, 'show_id = '. (int)$showid);

		// if we just updated the current show, update in session
		$ns = new Zend_Session_Namespace('myshows');
		if (isset($ns->showid) && ($showid==$ns->showid)) {
			$this->_selectShow($showid);
		}


		$this->_inviteTeams($showid,$data);

	}



	private function _inviteTeams($showid,$data) {
		$eventtypeid = $data['EVENT_TYPE_ID'];
		$invitedzone = $data['INVITED_ZONE'];
		$invitedrgn = $data['INVITED_RGN'];

		$model = new Model_DbTable_ShowTeams();
		if (-1==$invitedrgn) {
			$regions = array(1,2,3,4,5,6);
		} else {
			$regions = array($invitedrgn);
		}
		$model->inviteZones($showid,$eventtypeid, array($invitedzone),$regions);
	}

	public function deleteShow($showid)
	{
		$show = $this->getShow($showid);
		if ($show) {
			$typeid = $show['EVENT_TYPE_ID']; // remember event type
			$this->delete('SHOW_ID =' . (int)$showid);
			// if we just deleted the current show, set another
			$ownerid = Zend_Auth::getInstance()->getIdentity()->USER_ID;
			$ns = new Zend_Session_Namespace('myshows');
			if (isset($ns->showid) && ($showid==$ns->showid)) {
				unset($ns->showid);
				unset($ns->show);
				$this->selectMostRecentShow($ownerid);
			}
		}

	}



	public function fetchShows() {
		$db = $this->_db;
		$select = $db->select()
		->from('shows')

		->join('event_type','shows.event_type_id=event_type.event_type_id','EVENT_TYPE_NAME')
		->join('user','shows.user_id=user.user_id',array('FIRST_NAME','LAST_NAME'))
		//->where('shows.show_id in (select show_id from show_team)')
		->order('SHOW_DATE DESC')
		;
		$shows = $db->fetchAll($select);
		$ans = array();
		foreach ($shows as $show) {
			$show['STATUS'] = $this->_showStatus($show['CLOSE_DATE']);
			$zd = new Zend_Date($show['SHOW_DATE'],'yyyy-MM-dd');
			$show['SHOW_DATE']= $zd->get(Zend_Date::DATE_MEDIUM);
			$zd = new Zend_Date($show['CLOSE_DATE'],'yyyy-MM-dd');
			$show['CLOSE_DATE']= $zd->get(Zend_Date::DATE_MEDIUM);
			$show['ZONE'] = 'Z' . $show['INVITED_ZONE'] ;
			// _showinfo['INVITED_RGN'] ==-1 ? 'ALL' : $this->_showinfo['INVITED_RGN'];
			$show['RGN'] =  $show['INVITED_RGN'] == '-1' ? 'ALL' : 'R' . $show['INVITED_RGN'];
			//$show['LOGO_PATH'] = $this->getLogoPath($show['SHOW_ID']);
			$ans[] = $show;
		}
		return $ans;
	}
	private function _showStatus($closedate) {
		$closed = $this->_isClosed($closedate);
		return $closed ? 'CLOSED' : 'OPEN';
	}

	static function isAfterToday_ymd($closedate) {
		$nowtime = strtotime(date('Y-m-d'));
		$closetime = strtotime($closedate);

		if ($closetime < $nowtime) {
			return true;
		} else {
			return false;
		}
	}
	private function _isClosed($closedate) {

		return Model_DbTable_Shows::isAfterToday_ymd($closedate);
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
	public function selectMostRecentShow($ownerid) {
		$rows = $this->fetchAll('USER_ID='.$ownerid,'SHOW_DATE DESC',1);
		if (count($rows) > 0) {
			$row = reset($rows->toArray());
			$this->selectShow($row['SHOW_ID']);
		}

	}


	// set show in session if not already set
	public function selectShow($showid) {
		$ns = new Zend_Session_Namespace('myshows');
		if (isset($ns->showid) && ($showid==$ns->showid)) return $ns->show;

		// if we are here , get show from db
		return $this->_selectShow($showid);
	}

	private function _selectShow ($showid) {
		$showrow = $this->getShow($showid);
		$et = new Model_DbTable_EventTypes();
		$eventType = $et->fetchEventType($showrow['EVENT_TYPE_ID']);
		$usermodel = new Model_DbTable_Users();
		$user = $usermodel->fetchUser($showrow['USER_ID']);

		$show = new stdClass();
		$show->name = $showrow['SHOW_NAME'];

		$zd = new Zend_Date($showrow['SHOW_DATE'],'yyyy-MM-dd');
		$show->date = $zd->get(Zend_Date::DATE_FULL);

		$show->typeid = $eventType['EVENT_TYPE_ID'];
		$show->type = $eventType['EVENT_TYPE_NAME'];

		$zd = new Zend_Date($showrow['CLOSE_DATE'],'yyyy-MM-dd');
		$show->close = $zd->get(Zend_Date::DATE_FULL);

		$show->owner = $showrow['USER_ID'];
		$show->host = $user['FIRST_NAME'] . ' ' . $user['LAST_NAME'];
		$show->status =  $this->_showStatus($showrow['CLOSE_DATE']);
		$ns = new Zend_Session_Namespace('myshows');

		$ns->show = $show;
		$ns->showid = $showid;
		return $show;
	}

	static public function isMyShow($showid) {
		$model = new Model_DbTable_Shows();
		$showrow = $model->getShow($showid);
		$whoami = Zend_Auth::getInstance()->hasIdentity() ? Zend_Auth::getInstance()->getIdentity()->USER_ID : 0;
		$ans = ($whoami == $showrow['USER_ID']);
		return $ans;
	}

	static public function isClosed($showid) {
		$model = new Model_DbTable_Shows();
		$showrow = $model->getShow($showid);
		$ans = $model->_isClosed($showrow['CLOSE_DATE']);

		return $ans;
	}



}


