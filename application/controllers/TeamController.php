<?php

class TeamController extends Zend_Controller_Action
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

	public function inviteAction() {
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		$teamid = $this->_getParam('team',0);
		$st = new Model_DbTable_ShowTeams();
		$st->inviteOne($showid,$teamid);
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$this->_redirect('/team/index');
	}
	public function indexAction()
	{
		$this->view->title = "Manage Teams";
		$this->view->headTitle('Add, edit,delete equestrian team', 'PREPEND');

		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		 
		$teams = new Model_DbTable_Teams();
		$st = new Model_DbTable_ShowTeams();
		$this->view->teams = $teams->fetchTeams();
		if (isset($showid)) {
			$this->view->invitedteams = $st->fetchInvitedTeamIds( $showid);
		}
			
	}

	public function editAction()
	{
		 
		$this->view->title = "Edit team";
		$this->view->headTitle($this->view->title, 'PREPEND');

		// We instantiate our Form_Team,
		// set the label for the submit button to “Add”
		// and then assign to the view for rendering.
		$form = new Form_Team();
		$form->submit->setLabel('Save');
		$this->view->form = $form;


		// If the request object's isPost() method is true,
		// then the form has been submitted
		// and so we retrieve the form data from the request
		// and check to see if it is valid using the isValid()
		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				$teamId = $form->getValue('TEAM_ID');
				$teamName = $form->getValue('TEAM_NAME');
				$teamtype = $form->getValue('EVENT_TYPE_ID');
				$zoneid = $form->getValue('ZONE_ID');
				$rgnid = $form->getValue('RGN_ID');
				$model = new Model_DbTable_Teams();
				$auth = Zend_Auth::getInstance();
				$userid = $auth->getIdentity()->USER_ID;
				$data = array(
					'team_name' => $teamName,
					'event_type_id' => $teamtype,
					'user_id' => $userid,
					'zone_id' => $zoneid,
					'rgn_id' => $rgnid,				
				);
				$model->updateTeam($teamId,$data);

				// after record was added, go back to the home page
				$this->_redirect('/team/');

				// If not valid, populate with user entered in and redisplay.
			} else {
				$form->populate($formData);
			}
		}  // when displaying the form, retrieve the records from db database and populate the form.
		else {
			$id = $this->_request->getParam('team',0);

			if ($id > 0) {
				$teams = new Model_DbTable_Teams();
				$form->populate($teams->fetchTeam($id));
			}
		}
		 
	}

	public function addAction()
	{
		$this->view->title = "Add team";
		$this->view->headTitle($this->view->title, 'PREPEND');

		// We instantiate our Form_Team,
		// set the label for the submit button to “Add”
		// and then assign to the view for rendering.
		$form = new Form_Team();
		$form->submit->setLabel('Add');
		$this->view->form = $form;


		// If the request object's isPost() method is true,
		// then the form has been submitted
		// and so we retrieve the form data from the request
		// and check to see if it is valid using the isValid()
		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				$teamName = $form->getValue('TEAM_NAME');
				$teamtype = $form->getValue('EVENT_TYPE_ID');
				$zoneid = $form->getValue('ZONE_ID');
				$rgnid = $form->getValue('RGN_ID');
				$teams = new Model_DbTable_Teams();
				$auth = Zend_Auth::getInstance();
				$userid = $auth->getIdentity()->USER_ID;
				$data = array(
					'user_id' => $userid,
					'team_name' => $teamName,
					'event_type_id' => $teamtype,
					'zone_id' => $zoneid,
					'rgn_id' => $rgnid,

				);
				$teams->addTeam($data );

				// after record was added, go back to the home page
				$this->_redirect('/team/');

				// If not valid, populate with user entered in and redisplay.
			} else {
				$form->populate($formData);
			}
		}
	}

	public function deleteAction()
	{
		$this->view->title = "Delete team";
		$this->view->headTitle($this->view->title, 'PREPEND');
		// shouldn't do an irreversible action using GET but should use POST
		if ($this->getRequest()->isPost()) {
			$del = $this->getRequest()->getPost('del');
			if ($del == 'Yes') {
				$id = $this->getRequest()->getPost('teamid');
				$teams = new Model_DbTable_Teams();
				$teams->deleteTeam($id);
			}
			$this->_redirect('/team/');
		} else {
			$id = $this->_getParam('team', 0);

			$teams = new Model_DbTable_Teams();
			$this->view->team = $teams->fetchTeam($id);
		}
	}
	
	public function confirmAction() {
		$showid = $this->_getParam("show",0);
		$teamid = $this->_getParam("team",0);
		$this->view->title='Confirmed Entries :';
		$mm = new Model_DbTable_Entries();
		$teams = $mm->fetchStudentEntries($showid, $teamid);

		$this->view->teams = $teams;
		$this->view->showid = $showid;

	}

}



