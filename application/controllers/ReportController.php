<?php

//include_once 'Report/Document.php';
//include_once 'Report/Page.php';

class ReportController extends Zend_Controller_Action
{
	public function preDispatch()
	{
		if (Zend_Auth::getInstance()->hasIdentity()) {
		} else {
			$url =  '/auth/login/' ;
			$this->_redirect($url);
		}
	}
	public function init()
	{
		//  $this->_helper->acl->allow(null);
	}
	public function prizeAction() {
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		
		$m = new Model_Report_Prize();
		$report = $m->createPrize($showid);
		header('Content-type: application/pdf');

		// It will be called cover.pdf
		header('Content-Disposition: attachment; filename="program.pdf"');
		echo $report->getDocument()->render();

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	public function accountAction() {
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		
		$m = new Model_Report_Account();
		$report = $m->createAccounts($showid, 0);
		
		header('Content-type: application/pdf');

		// It will be called cover.pdf
		header('Content-Disposition: attachment; filename="accounts.pdf"');
		echo $report->getDocument()->render();

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	
	public function myaccountAction() {
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		
		$teamid = $this->_request->getParam('team',0);
		$m = new Model_Report_Account();
		$report = $m->createAccounts($showid, $teamid);
		
		header('Content-type: application/pdf');

		header('Content-Disposition: attachment; filename="myaccount.pdf"');
		echo $report->getDocument()->render();

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	public function pointcardsAction() {
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		
		$m = new Model_Report_Pointcard();
		$report = $m->createPointcard();
		
		header('Content-type: application/pdf');

		// It will be called cover.pdf
		header('Content-Disposition: attachment; filename="pointcards.pdf"');
		echo $report->getDocument()->render();

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	public function imgAction() {
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		
		$m = new Model_Report_Img();
		$report = $m->createImage();
		header('Content-type: application/pdf');

		// It will be called cover.pdf
		header('Content-Disposition: attachment; filename="img.pdf"');
		echo $report->getDocument()->render();

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}

	public function coverAction() {
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		
		$m = new Model_Report_Cover();
		$report = $m->createCover($showid);
		header('Content-type: application/pdf');

		// It will be called cover.pdf
		header('Content-Disposition: attachment; filename="cover.pdf"');
		echo $report->getDocument()->render();

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	
	public function horsedrawAction() {
		$m = new Model_Report_Draw();
		$report = $m->createDrawsheets();
		header('Content-type: application/pdf');

		// It will be called horsedrawsheets.pdf
		header('Content-Disposition: attachment; filename="horsedrawsheets.pdf"');
		echo $report->getDocument()->render();

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	public function programAction() {
		$m = new Model_Report_Program();
		$report = $m->createProgram(1);

		header('Content-type: application/pdf');

		// It will be called showprogram.pdf
		header('Content-Disposition: attachment; filename="showprogram.pdf"');
		echo $report->getDocument()->render();

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}

	public function entriesAction() {
		$m = new Model_Report_Program();
		$report = $m->createProgram(2);

		header('Content-type: application/pdf');

		// It will be called entries.pdf
		header('Content-Disposition: attachment; filename="entries.pdf"');
		echo $report->getDocument()->render();

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}


	public function horsesAction() {
		$m = new Model_Report_Program();
		$report = $m->createProgram(3);

		header('Content-type: application/pdf');

		// It will be called entries.pdf
		header('Content-Disposition: attachment; filename="horses.pdf"');
		echo $report->getDocument()->render();

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	
	public function judgescardsAction() {
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		
		$m = new Model_Report_Judgescard();
		$report = $m->createJudgesCards($showid);

		header('Content-type: application/pdf');
		// It will be called entries.pdf
		header('Content-Disposition: attachment; filename="judgescards.pdf"');
		echo $report->getDocument()->render();

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	public function nopdfAction() {
 
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		
		$m = new Model_Report_Cover();
		$report = $m->createCover($showid);
		
//		header('Content-type: application/pdf');

		// It will be called cover.pdf
//		header('Content-Disposition: attachment; filename="cover.pdf"');
//		echo $report->getDocument()->render();
	
		echo 'jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj';
//		print_r($m->sections);
//		print_r ($m->show);

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

	}
	
    public function indexAction()
    {
        $this->view->title = "THE FOLLOWING SHOW REPORTS ARE READY TO PRINT:";
        $headtitle = "Print Prizelist, Horse drawsheets, Pointcards ";
        $this->view->headTitle($headtitle, 'PREPEND');
        
    }

}