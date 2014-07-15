<?php
class Model_Report_Prize  {

	
	public function createPrize1($showid) {

		$report = new Model_Report_Prizedoc();
	


		$m = new Model_Report_Program();


		/** entries */
		$epages = $m->createProgramPages(1);
		foreach ($epages as $page) {
			$report->addPage($page);
		}


				
		return $report;
	}

	public function createPrize($showid) {

		$report = new Model_Report_Prizedoc();
		$m = new Model_Report_Cover();
		$i = new Model_Report_Img();
		$ipage = $i->createImagePage('images/perri.png');
		$cpages = $m->createCoverPages($showid);

		$report->addPage($cpages[0]);
		$report->addPage($ipage);
		$report->addPage($cpages[1]);


		$m = new Model_Report_Program();
		/** horses */
		$hpages = $m->createProgramPages(3);
		foreach ($hpages as $page) {
			$report->addPage($page);
		}

		/** entries */
		$epages = $m->createProgramPages(2);
		foreach ($epages as $page) {
			$report->addPage($page);
		}

		/** sections */
		$spages = $m->createProgramPages(1);
		foreach ($spages as $page) {
			$report->addPage($page);
		}

		$ipage = $i->createImagePage('images/brookledge.png');
		$report->addPage($ipage);
		
		$ipage = $i->createImagePage('images/ride_ribbon.png');
		$report->addPage($ipage);
		
		$ipage = $i->createImagePage('images/aqha.png');
		$report->addPage($ipage);
		
		$ipage = $i->createImagePage('images/collegiate.png');
		$report->addPage($ipage);
				
		return $report;
	}
	

	function logit($xtra) {
		// YYYYmmdd-hhii
		$tstamp = date('Ymd-hi' , time());
		$fname = "images/aaamylog.log" ;
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