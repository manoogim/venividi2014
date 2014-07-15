<?php
class Model_Report_Draw  {

	private $alternates;
	private $sections;
	private $show;
	private $classnames ;
	

	private function populateDrawsheets($showid) {
		$m = new Model_DbTable_HorseDraw();
		$pair = $m->fetchSectionHorseDraws($showid);
		
		$map = array();	
		$amap = array();
		
		if (empty ($pair[0])) { // create empty drawsheets for each section
					
			$p = new Model_DbTable_Programs();			
			foreach ($p->fetchSections($showid) as $s) {
					$sectname = $s->sectname;
					$cnt = $s->cnt;
					if (! isset($map[$sectname])) {
						$map[$sectname] = array();
						$amap[$sectname] = array();
					}
					for( $i=0; $i<$cnt; $i++ ) {
						$map[$sectname][] = '_______' ;
					}
					for( $i=0; $i<2; $i++ ) {
						$amap[$sectname][] = '_______' ;
						
					}
			}
			$this->sections = $map;
			$this->alternates = $amap;
			
		} else {
			$map = $pair[0];
			$amap =$pair[1];

		}
		$this->sections = $map;
		$this->alternates =  $amap ;
		
		$this->classnames = array();
		$p = new Model_DbTable_Programs();
		foreach (array_keys($this->sections) as $sect) {
			$this->classnames[$sect] = $p->fetchClassName($showid, $sect);
			$this->alternates[$sect] = isset($amap[$sect]) ? $amap[$sect] : array();
			//$this->logit('$sect= ' . $sect . ' $this->alternates[$sect] ' . $this->alternates[$sect][0]);
		}
	}

	public function createDrawsheets() {
		// set the show
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		$this->show = $ns->show;

		// fetch records from db
		$this->populateDrawsheets($showid);
		$report = new Model_Report_Drawdocument();
		//
		foreach (array_keys($this->sections) as $sect) {
			
			$pages = $this->createOneDrawsheet($report, $sect);
			foreach ($pages as $page) {
				$report->addPage($page);
			}
		}


		return $report;
	}
	
	private function createOneDrawsheet($report, $sect) {
		$pages = array();
		$alters = $this->alternates[$sect];
		$horses = $this->sections[$sect];
		
		$remains = count($horses);
		$hoffset = 0;		
		
		while ($remains > 0) {
			$page = new Model_Report_Drawpage();			
			$NH = min($remains, 13); // at most 13

			$page->_startnum = $hoffset;
			$page->_showinfo = $this->show;
			$page->_sectname = $sect;
			$page->_classname = $this->classnames[$sect];
			$page->_horsenames = array_slice($horses,$hoffset,$NH);
			$page->_alternates = array();
				
			$hoffset += $NH;
			$remains -= $NH;
			$pages[] = $page;
			
		}
		//$this->logit(' $sect=' . $sect . ' $hoffset= ' . $hoffset . ' $NH=' . $NH . ' $remains=' . $remains);
		
		// after the loop $NH = number of lines printed on the last page
		// finish adding alternates on the same page		
		$aoffset = 0;
		$aremains = count ($alters);
		$NA = min ($aremains, 12-$NH);
		if ($NA > 0) {
			$page->_alternates = array_slice($alters, 0, $NA);
			$aoffset = $NA;
			$aremains = count ($alters) - $NA;			
		}
		//$this->logit( ' $sect=' . $sect . ' $NH=' . $NH . ' $NA=' . $NA . ' count(array_slice($alters, 0, $NA)=' . count(array_slice($alters, 0, $NA)));

		
		while ($aremains > 0) { 
			$page = new Model_Report_Drawpage();
			$NA = min($aremains, 13); // at most 13
			$page->_sectname = $sect;
			$page->_classname = $this->classnames[$sect];
			$page->_horsenames = array();
			$page->_alternates = array_slice($alters, $aoffset, $NA);
			$aoffset += $NA;
			$aremains -= $NA;
			
			$pages[] = $page;
		}
		//$alters = $this->alternates[$sect];
		//$nn = count($horses);
		//$na = count( $alters );
		//$totalspace = 190 + 50 * ($nn + $na); // out of 842
		return $pages;
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