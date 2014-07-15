<?php

class EntryController extends Zend_Controller_Action
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
		/* Initialize action controller here */
	}

	/** for selected class, displays entered students, and split button */
	public function splitAction() {
		$this->view->title = "Class Entries";
		$this->view->headTitle($this->view->title, 'PREPEND');
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;

		if ($this->getRequest()->isPost())
		{ // perform the split
			$classid = $this->getRequest()->getPost('clazz');
			$classname = $this->getRequest()->getPost('clazzname');
			$cnt = $this->getRequest()->getPost('splitcnt');
			$model = new Model_DbTable_Entries();
			$model->splitClass($showid, $classid, $cnt);
			//$url = '/entry/split/clazz/' . $classid . '/clazzname/'. $classname;
			$url = '/clazz/index';
			$this->_redirect($url);
		}
		else
		{ // we are rendering the form here
			$classname = $this->_request->getParam('clazzname','');
			$classid = $this->_request->getParam('clazz',0);
			 
			$model = new Model_DbTable_Programs();
			$entries = $model->fetchPrograms($showid, $classid);
			$splitcnt =count($entries) +1;
			$this->view->entries = $entries;
			$this->view->clazz=$classid;
			$this->view->clazzname = $classname;
			$this->view->splitcnt = $splitcnt;
		}

	}


	public function listAction() {
		$this->view->title = "Class Entries";
		$this->view->headTitle($this->view->title, 'PREPEND');

		$classname=$this->_request->getParam('clazzname','');
		$classid = $this->_request->getParam('clazz',0);
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;

		$model = new Model_DbTable_Entries();

		$this->view->entries= $model->fetchClassEntries($classid, $showid);
		$this->view->clazzname = $this->_request->getParam('clazzname','');
		$this->view->clazzid=$classid;

	}



	/** for selected student displays entry form*/
//	public function updateAction()
//	{
//		$this->view->title = "Select Class Entries";
//		$this->view->headTitle($this->view->title, 'PREPEND');
//		// we are submitting form here
//		
//		if ($this->getRequest()->isPost()) {
//			$save = $this->getRequest()->getPost('save');
//
//			$showid = $this->getRequest()->getPost('showid');
//			$studentid = $this->getRequest()->getPost('studentid');
//			$entryarr = $this->getRequest()->getPost('myentry');
//			$backnumber = $this->getRequest()->getPost('backnumber');
//			$model = new Model_DbTable_Entries();
//			$model->updateEntries($showid,$studentid, $entryarr,$backnumber);
//			//$url = '/student/index/owner/2/team/3/teamName/blah';
//			
//			$url= $this->view->nexturl;
//			$url = '/student/index/owner/' . $this->_getParam('owner',0) .
//					'/team/' . $this->_getParam('team',0) .
//					'/teamName/' . $this->view->teamname;
//			//$url = $this->getRequest()->getPost('nexturl');
//			$this->_redirect($url);
//
//		} else { // we are rendering the form here
//			$ns = new Zend_Session_Namespace('myshows');
//			$showid = $ns->showid;
//			$studentid = $this->_getParam('student',0);
//			$studentname = $this->_getParam('studentname','');
//			
//			$this->view->owner = $this->_getParam('owner',0);
//			$this->view->team =  $this->_getParam('team',0);
//			$this->view->teamname= $this->_getParam('teamName','blah');
//			$model = new Model_DbTable_Entries();
//			$entries = $model->fetchStudentEntries($studentid, $showid);
//			$this->view->clazzes = $entries;
//			$this->view->showid = $showid;
//			$this->view->studentid = $studentid;
//			$this->view->studentname = $studentname;
//			if (count($entries)>0) {
//				$first=reset($entries);
//				$this->view->backnumber = $first->backnumber;				
//			} 
//			
//			
//		}
//	}
	
	public function entryAction() {
		//  we arecomming from selecting a show row ..
		// make this show current
		$ownerid = $this->_getParam('owner',0);
		$showid = $this->_getParam('show',0);
		$teamid=$this->_getParam('team', 0);
		$type = $this->_getParam('type',777);
		$teamname = $this->_getparam('teamName','myteam');
//		$gdeposle = 'confirm';//$this->_getParam('where','kozna')	;
		$shows = new Model_DbTable_Shows();
		$selshow = $shows->selectShow($showid);
		
		$e = new Model_DbTable_Entries();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			//$this->logit(json_encode($formData));
			
			$e->updateEntriesAll($showid, $formData);
			$this->_redirect('/team/confirm/show/' . $showid .'/team/' . $teamid);
			
		} else {
			
			// load classes and students and pass to the view
			$c = new Model_DbTable_Classes();
			$clazzes = $c->fetchClasses($selshow->typeid);

			$s = new Model_DbTable_Students();
			$students = $s->fetchStudents($teamid);
			
			$entries = $e->fetchTeamEntries($showid, $students, $clazzes);
			$this->view->ownerid = $ownerid;
			$this->view->teamid = $teamid;
			$this->view->teamname = $teamname;
			$this->view->type =$type;
			$this->view->clazzes = $clazzes;
			$this->view->students = $students;
			$this->view->entries = $entries;
		}
	}

	public function deleteAction()
	{
		$ns = new Zend_Session_Namespace('myshows');
		$this->view->title = "Delete show entries";
		$this->view->headTitle($this->view->title, 'PREPEND');
		$this->view->showname = $ns->show->name;
		
		// shouldn't do an irreversible action using GET but should use POST
		 
		if ($this->getRequest()->isPost()) {
			$del = $this->getRequest()->getPost('del');
			if ($del == 'Yes') {
				$showid = $ns->showid;
				$m = new Model_DbTable_Entries();
				$m->deleteAllEntries($showid);
				$this->_redirect('/show/');
			} else {
				$this->_redirect('/clazz/');
			}

		} else {
				
		}
	}
	
	
}



