<?php
class Model_Report_Program  {

	
	private $titles;
	private $subtitle;
	public $sections;
	private $show;
	private $totals;
	public $sectiontotals;
	private $sectcounts;
	public $startpagenum = 1;
	
	private function populateTest($showid) {
	
		$map = array();

		for ( $counter = 0; $counter <= 20; $counter ++) {
			//print_r($horse);
			$key = $counter;	
			$desc =	'description ' . $key;

			$crop ='NO Crop';
			
			$spurs = ', NO Spurs';
			
			$desc = $desc . $crop . $spurs;
			$desc2 = '                      standard' ;
			$map[$key][] = array($key, $desc, $desc2 );
		}
		
		$titles=array();
		$ans=array();
		foreach (array_keys ($map) as $key) {
			$titles[] ='Horses from: ' .  $key;
			$ans[] = $map[$key];
		}
		//print_r($ans);
		$this->titles = $titles;
		$this->subtitle = array('name'=>'Horse','desc'=>'Description');
		$this->sections = $ans;
		$this->totals = $this->_makeHorsesLegend();
		$this->sectiontotals = array();
		$this->sectcounts = array();
		return $ans;
	}
	
private function populateTestCompact($showid) {
	
		$map = array();

		for ( $counter = 0; $counter <= 54; $counter ++) {
			//print_r($horse);
			$key = $counter;	
			$desc =	'description ' . $key;

			$crop ='NO Crop';
			
			$spurs = ', NO Spurs';
			
			$desc = $desc . $crop . $spurs;
			$desc2 = '                      standard' ;
			$map[0][$key] = array($key, $desc, $desc2 );
		}
		
		$titles=array();
		$ans=array();
		foreach (array_keys ($map) as $key) {
			$titles[] ='Horses from: ' .  $key;
			$ans[] = $map[$key];
		}
		//print_r($ans);
		$this->titles = $titles;
		$this->subtitle = array('name'=>'Horse','desc'=>'Description');
		$this->sections = $ans;
		$this->totals = $this->_makeHorsesLegend();
		$this->sectiontotals[] = $this->_makedashes(6);
		$this->sectcounts = array();
		return $ans;
	}
	private function populateHorses($showid) {
		$rows = new Model_DbTable_Horses();
		$userid = Zend_Auth::getInstance()->getIdentity()->USER_ID;
		$horses = $rows->fetchHorsesUser($userid);
		
		$map = array();
		foreach ($horses as $horse) {
			//print_r($horse);
			$key = $horse['BARN'];	
			$desc =	'';

			if ($horse['BREED']) {
				$desc = $desc . $horse['BREED'] . ', ';
			}
			if ($horse['COLOR']) {
				$desc = $desc . $horse['COLOR'] . ', ';
			}
			
			if ($horse['GENDER']) {
				$desc = $desc . $horse['GENDER'] . ', ';
			}
						
			if ($horse['HEIGHT']) {
				$desc = $desc . $horse['HEIGHT'] ;
			}

			if ($desc != '') {
				$desc = $desc . '.   ';
			}
			$crop = $horse['CROP'];
			if ($crop == 'A') $crop =' Crop optional';
			else if ($crop=='R') $crop = ' CROP ok';
			else $crop ='NO Crop';
			
			$spurs = $horse['SPURS'];
			if ($spurs=='Y') $spurs = ', SPURS';			
			else $spurs = ', NO Spurs';
			
			$desc = $desc . $crop . $spurs;
			$desc2 = '                     ' . $horse['HORSE_DESC'];
			$map[$key][] = array($horse['HORSE_NAME'], $desc, $desc2 );
		}
		
		$titles=array();
		$ans=array();
		foreach (array_keys ($map) as $key) {
			$titles[] ='Horses from: ' .  $key;
			$ans[] = $map[$key];
		}
		//print_r($ans);
		$this->titles = $titles;
		$this->subtitle = array('name'=>'Horse','desc'=>'Description');
		$this->sections = $ans;
		$this->totals = $this->_makeHorsesLegend();
		$this->sectiontotals = array();
		$this->sectcounts = array();
		return $ans;
	}
	
	private function _makeHorsesLegend() {
		$title = 'EXPLANATION';
		$sex = 'M = Mare, G = Gelding, S = Stallion';
	//	$crop = 'CROP = allowed, CROP* = recommended, NO Crop: crop not allowed';
	//	$spurs = 'SPURS= spurs are allowed, if blank: do not use spurs';

		$ans = array($title, $sex ); //, $crop, $spurs);
		return $ans;
	}
	private function populateEntries($showid) {	
		
		// subtitle is constant
		$subtitle = array('backnumber'=>'Back Num','thename'=>'Name','teamname'=>'Entries');
		$this->subtitle= $subtitle;
		
		// first loop : fetch all entries 
		$model = new Model_DbTable_Entries();
		$entries = $model->fetchEntries($showid);

		$teams = array();
		$teamrides = array();		
		foreach ($entries as $entry) {

			$teamid= $entry['TEAM_ID'];
			$studentid= $entry['STUDENT_ID'];
			if (! isset($teams[$teamid])) {
				$teams[$teamid] = array();
				$teamrides[$teamid] = 0;
			}
			$team = $teams[$teamid];
			

			if (!isset ($team[$studentid])) {
				$stud = new stdClass();
				$stud->backnumber = $entry['BACK_NUMBER'];
				$stud->firstname = $entry['FIRST_NAME'];
				$stud->lastname= $entry['LAST_NAME'];
				$stud->teamname = $entry['TEAM_NAME'];
				$teams[$teamid][$studentid] = $stud;
			} else {
				$stud = $team[$studentid];
			}

			if (!isset( $stud->classes )) {
				$stud->classes = '';
				$comma = '';
			} else {
				$comma = ", ";
			}
			$stud->classes =$stud->classes . $comma . $entry['CLASS_NAME'];
			$teamrides[$teamid] += 1;
		}

		// second loop : put info in the format for the page to consume
		$ans = array();
		$titles=array();
		$sectionTotals = array();
		foreach (array_values($teams) as  $team) {
			$lines = array();
			foreach (array_values($team) as $tt) {
				$title =$tt->teamname;
				$line = array($tt->backnumber, $tt->firstname . ' ' . $tt->lastname, $tt->classes);
				$lines[] = $line;
			}
			$bottom = 'Total Students / Rides = ' . count ($lines) . ' / ' . current($teamrides);
			$titles[] =  $title;
			$ans[] = $lines;
			$sectionTotals[] = $bottom;
		// $this->logit('$sectionTotals[]=' . $bottom);
			next($teamrides);
			
		}
		
		$this->titles = $titles;
		$this->sectiontotals = $sectionTotals;
		$this->sections = $ans;
		$this->totals = array();
		return $ans;
	}
	
	/** populate report variables from db */
	private function populateSections($showid ) {
		
		// subtitle is constant
		$subtitle = array('backnumber'=>'Back Num','firstname'=>'Name','teamname'=>'Team Name');
		$this->subtitle= $subtitle;
		
		// fetch all sections 
		$model = new Model_DbTable_Programs();
		$sections= $model->fetchPrograms($showid, 0);

		$ans = array();
		$titles=array();
		$sectionTotals = array();
		$total = 0;
		foreach ($sections as $sect) {
			
			$lines = array();
			foreach ($sect as $cc) {
				$title ='Section ' . $cc->sectname . ' : ' . $cc->clazzname;
				$line = array($cc->backnumber, $cc->firstname . ' '. $cc->lastname, $cc->teamname);
				$lines[] = $line;
				$total++;
			}
			$titles[] =  $title;
			$ans[] = $lines;
			$sectionTotals[] = $this->_makedashes(6);
			
		}
		$this->titles = $titles;
		$this->sections = $ans;
		$this->sectiontotals = $sectionTotals;
		
		$this->totals = $this->_makeProgramTotal($total);
		return $ans;
		
	}

	private function _makeProgramTotal($total) {
		$tmsg = 'Total number of entries = ' . $total;
		$high1 = 'HIGH POINT COLLEGE: _________________________________________';
		$high2 = 'RESERVE HIGH POINT COLLEGE: ________________________________';
		$high3 = 'HIGH POINT RIDER: ___________________________________________';
		$high4 = 'RESERVE HIGH POINT RIDER: __________________________________';
		$ans = array($tmsg, $high1, $high2, $high3, $high4);
		return $ans;
	}
	private function _makedashes($num) {
		$dashes = '';
		for ($kk=0; $kk<$num; $kk++)  {
			$d = (1+$kk) . '. ';
			$dashes =$dashes .  $d . ' ________      ';
		}
		//$this->logit('$dashes=' . $dashes);
		return $dashes;
	}
	
	public function createProgram($FLAG) {
		// instantiate report
		$report = new Model_Report_Programdoc();
		$pages = $this->createProgramPages($FLAG);
		foreach ($pages as $page) {
			$report->addPage($page);
		}
		return $report;
	}

	
	private function dump () {
		foreach ($this->sections as $sect) {
			$space = 120 + 12 * count($sect);
			$str = current($this->titles) . ' count = ' . count($sect) . ' space=' . $space;
			$this->logit($str);
			next ($this->titles);
		}
	}

	public function createProgramPages($FLAG) {
		define('MAX_PAGE_SPACE' , 775);
		// set the show
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		$this->show = $ns->show;
		
		// fetch records from db
		if ($FLAG==3) {
			$this->populateHorses($showid);
			
		} else if ($FLAG==2) {
			$this->populateEntries($showid);
			
		} else if ($FLAG==1) {
			$this->populateSections($showid);
			
		} else if ($FLAG==4) {
			$this->populateTest($showidw);
		} else {
			$this->populateTestCompact($showid);
		}
		
		$sectcounts = array();
		foreach ($this->sections as $sect) {
			$sectcounts[] = count($sect);
		}

		$sections = array();
		$titles = array();
		$totals = array();
		$totaUsedSpace = 0;
		$num = $this->startpagenum;
		$pages = array();
		//$this->logit ('$sectcounts= ' . json_encode($sectcounts) );
		foreach ($this->sections as $sect) {
			
			$sectPageSpace = 120 + 12 * count($sect);
			$totaUsedSpace += $sectPageSpace;
			
			$nextSectSpace = 120 + 12 *  next($sectcounts);
			
			
			$sections[] = $sect;
			$titles[] = current($this->titles) ;
			$totals[] = current($this->sectiontotals);
				
		   $ispagefull = $totaUsedSpace + $nextSectSpace > constant('MAX_PAGE_SPACE');
			if ($ispagefull) {
				//$this->logit ('finalizing page b/c page is full, will use ' . $totaUsedSpace . ' and remains =' . $nextSectSpace . json_encode($titles) );
				$page = $this->finalizePage($titles, $this->subtitle, $sections, $totals, $num);				
				$pages[] = $page	;
							
				$sections = array();
				$titles = array();
				$totals = array();
				$totaUsedSpace = 0; // $nextSectSpace;
				
				$num += 1;
			}

			next($this->titles);
			next($this->sectiontotals);

		}
		// add last page
		//$this->logit ('finalizing page b/c it is the last page ' . json_encode($titles));
		$lastpage = $this->finalizePage($titles, $this->subtitle, $sections, $totals, $num);	
		
		// write total number of entries 	
		$footerneedspace = 60 + 30 * count($this->totals)		;
		
	//	$this->logit ( ' $totaUsedSpace=' . $totaUsedSpace . ' $footerneedspace=' . $footerneedspace);
		// does totals need to be alone on the next page ?
		if 	($totaUsedSpace + $footerneedspace > constant('MAX_PAGE_SPACE')) {
		//	$this->logit('we need xtra page for totals !!');
			$pages[] = $lastpage;				
			$lastpage = $this->finalizePage(array(),array(),array(),array(),++$num);

		}
		$lastpage->totals = $this->totals;
		$pages[] = $lastpage;


		
		return $pages;
	}
	private function finalizePage($titles, $subtitles, $sections, $totals, $num) {
		//$this->logit ('-----------------------------');
		$page1 = new Model_Report_Programpage();
		$page1->setTitles($titles);
		$page1->setLines ($sections);
		$page1->setSubTitle($subtitles);
		$page1->_showinfo = $this->show;
		$page1->_pagenum = $num;		
		$page1->sectiontotals = $totals;
		$page1->totals = array();
		
		$this->startpagenum = $num + 1;
		return $page1;
	}
	

function logit($xtra) {
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