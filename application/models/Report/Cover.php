<?php
class Model_Report_Cover  {

	private $show;
	private $teams;
	private $stewards;
	private $horseholders;
	private $hostedby;
	private $logoimg;
	private $logoimg2;
	
	public $page;

	private function _prepareCover ( $showid ) {
		
		$m = new Model_DbTable_Shows();
		
		$show = $m->getShow($showid);
		$this->show = $show;
		$zd = new Zend_Date($show['SHOW_DATE'],'yyyy-MM-dd');
		$this->show['SHOW_DATE'] = $zd->get(Zend_Date::DATE_FULL);
		
		$this->stewards = array_map('trim',explode(',',$show['STEWARD']));		
		$this->horseholders = array_map('trim',explode(',',$show['HORSEHOLDER']));

		$this->logoimg = Model_DbTable_Users::getUserLogo( $show['USER_ID'] , $show['EVENT_TYPE_ID']);
		$this->logoimg2 = Zend_Pdf_Image::imageWithPath('images/ushja.png');
		
		$this->teams = array();
		$rows = Model_DbTable_ShowTeams::getInstance()->fetchInvitedTeamsEntered($showid);
		//$this->logit('$showid=' . $showid . ' count($rows=' . count($rows));
		foreach ($rows as $row) {
			//$this->logit('team=' . $row['TEAM_NAME']);
			$this->teams[] = $row['TEAM_NAME'];
			if ($row['USER_ID'] == $show['USER_ID']) {
				$this->hostedby = $row['TEAM_NAME'];
			}
		}
		
		
	}
	public function createCoverPages($showid) {
		
		// set the show info
		$this->_prepareCover($showid);
		
		$page1 = $this->createCoverPage($showid, 1);
		$page2 = $this->createCoverPage($showid, 2);
		
		return array($page2, $page1);
	}
	public function createCover($showid) {
		$report = new Model_Report_Coverdoc();
		$pages = $this->createCoverPages($showid);

		$report->addPage($pages[0]);
		$report->addPage($pages[1]);
		return $report;
	}
	private function createCoverPage($showid, $flag) {
		
		$page = new Model_Report_Coverpage($flag);

		$page->_image = $this->logoimg;
		$page->_image2 = $this->logoimg2;
		
		$page->_showinfo = $this->show;
		$page->_teams = $this->teams;
		$page->_judge= trim($this->show['JUDGE']);
		$page->_stewards = $this->stewards;
		$page->_horseholders = $this->horseholders;
		$page->_announcer = trim($this->show['ANNOUNCER']);
		$page->_padockmaster = trim($this->show['PADOCK_MASTER']);
		$page->_jumpingmaster = trim($this->show['JUMPING_MASTER']);
		$page->_medical = trim($this->show['MEDICAL']);
		$page->_hostedby = $this->hostedby;

		return $page;
		
	}
	
	
	
 private function logit($xtra) {
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