<?php
class Model_Report_Judgescard  {

	private $sections;
	private $show;
	private $numbersrow;
	private $xoffsets;


	private function populateJudgescards($showid) {
		$s = new Model_DbTable_Shows();
		$this->show = $s->getShow($showid);
		
		$p = new Model_DbTable_Programs();
		$this->sections = $p->fetchSections($showid);
		
		$emptyflat = new stdClass();
		$emptyflat->cnt = 0;
		$emptyflat->classname = '_______';
		$emptyflat->classtype= ' Flat Classes';
		$emptyflat->classtypeid=1;
		
		$emptyfences = new stdClass();
		$emptyfences->cnt = 0;
		$emptyfences->classname = '_______';
		$emptyfences->classtype= ' Classes Over Fences';
		$emptyfences->classtypeid=0;
		
		$this->sections[] = $emptyflat;
		$this->sections[] = $emptyfences;
	}
	

	public function createJudgesCards($showid) {
		// fetch records from db and init report
		$this->populateJudgescards($showid);
		$report = new Model_Report_Judgescardsdocument();
		//
		foreach (array_keys($this->sections) as $key) {
			$sect = $this->sections[$key];
			
			$page = new Model_Report_Judgescardpage();
			$page->_showinfo = $this->show;
			$page->_section = $sect;
			if ($sect->classtypeid % 2 ==0) {// even = fences
				$page->_numbersrow = array('NO.',1,2,3,4,5,6,7,8,9,10,11,12,' ',' ',' ');
			} else {
				$page->_numbersrow =  array('NO.','','','','','','','','','','','','','','','');//array('NO.','','','','','','');
			}
			$report->addPage($page);
			
		}
		return $report;
	}
	

}