<?php
class Model_Report_Account  {

//	private $show;
	public $clazzes;
	public $teams;
	private $coaches;
	public $riders;
	private $cnts;
	private $amts;
	private $totalcnts;
	private $totalamts;

	private function populate($showid, $oneteamid) {

		$m = new Model_DbTable_Users();
		$t = new Model_DbTable_ShowTeams();
		$e = new Model_DbTable_Entries();
		$s = new Model_DbTable_Shows();
		$show = $s->getShow($showid);
		$fee = $show['ENTRY_FEE'];
		
		$rows = $t->fetchInvitedTeamsEntered($showid);
		foreach ($rows as $row) {
			$teamid = $row['TEAM_ID'];
			if ($oneteamid ==0 || $oneteamid == $teamid) {
				$this->teams[$teamid] = $row['TEAM_NAME'];
				$userid = $row['USER_ID'];
				$user = $m->fetchUser($userid);
				$this->coaches[$teamid] = $user['FIRST_NAME'] . ' ' . $user['LAST_NAME'];
					
				$this->clazzes[$teamid] = array();
				$this->riders[$teamid] = array();
				$this->cnts[$teamid] = array();
				$this->amts[$teamid] = array();
				$this->totalamts[$teamid] = 0;
				$this->totalcnts[$teamid] = 0;
			}
		}
		
		$entries = $e->fetchEntries($showid);
		foreach ($entries as $e) {
			$teamid = $e['TEAM_ID'];
			$sid = $e['STUDENT_ID'];
			
			if (!isset($this->riders[$teamid][$sid])) {
				$this->riders[$teamid][$sid] = $e['FIRST_NAME'] . ' ' . $e['LAST_NAME'];
				$this->clazzes[$teamid][$sid] = '';
				$this->cnts[$teamid][$sid] = 0;
				$this->amts[$teamid][$sid] = 0;
			} 
				$this->clazzes[$teamid][$sid]  = $this->clazzes[$teamid][$sid] . $e['CLASS_NAME'] . ', ';
				$this->cnts[$teamid][$sid] ++;
				$this->amts[$teamid][$sid] +=  $fee;
				
				$this->totalcnts[$teamid] ++;
				$this->totalamts[$teamid] += $fee;
			
		}
	}

	public function createAccounts($showid, $oneteamid) {
		$ns = new Zend_Session_Namespace('myshows');
		//$showmodel = new Model_DbTable_Shows();
		$show = $ns->show;
		$this->populate($showid, $oneteamid);

		$report = new Model_Report_Accountdoc();
		$cnt =0;
		foreach (array_keys($this->teams) as $teamid) {
			
			$page = new Model_Report_Accountpage();
			$page->_showinfo = $show;
			$page->_clazzes = array_values($this->clazzes[$teamid]);
			$page->_riders = array_values($this->riders[$teamid]);
			$page->_cnts = array_values($this->cnts[$teamid]);
			$page->_amts = array_values($this->amts[$teamid]);
			$page->_teaminfo = $this->teams[$teamid];
			$page->_coachinfo = $this->coaches[$teamid];			
			$page->_totalcnt = $this->totalcnts[$teamid];
			$page->_totalamt = $this->totalamts[$teamid];
			
			$page->_pagenum = ++$cnt;
			$report->addPage($page);
		}

		
		return $report;
	}



	function logit($xtra) {
		// YYYYmmdd-hhii
		$tstamp = date('Ymd-hi' , time());
		$fname = "c:/aaa/mylog.log" ;
		//mkdir($fname);
		$logme = 'ZZZ!!=' . $xtra . ' ,nnnnn ' . ";\n";
		$fh = fopen($fname,'a');

		if (flock($fh, LOCK_EX))
		{
			fwrite($fh, $logme) ;
			flock($fh, LOCK_UN);
		}

		fclose($fh);

	}
}