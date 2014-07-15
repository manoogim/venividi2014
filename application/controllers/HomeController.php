<?php

/** these are actions for not loggedin user */
class HomeController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}
	public function downAction() {
		$this->_helper->layout->disableLayout();
	}

	public function healthAction() {

     $conf  = parse_ini_file(APPLICATION_PATH . '/configs/application.ini',null);
    $this->view->health = $conf;   
    try {
    $zones = new Model_DbTable_Zones();
   	$this->view->zones = $zones->fetchZones();
    } catch (Exception $e) {
    	$this->view->error = $e->getMessage();
    }

   $this->_helper->layout->disableLayout();
	}
	
	
	public function thanksAction() {
		$this->view->headTitle("Horse show software thank you", 'PREPEND');
	}
	
	public function nothanksAction() {
		$this->view->headTitle("Horse show software thank you", 'PREPEND');
	}
	
	public function forgotAction() {
			$this->view->title = "Forgot Password";
		$this->view->headTitle($this->view->title, 'PREPEND');

		if ($this->getRequest()->isPost()) {

				$email = $this->getRequest()->getPost('email_to');
				$model = new Model_DbTable_Users();
				$res = $model->sendPassword($email);
				echo $res[0];
			echo $res[1];
//			$this->_redirect('/');
		} else {

			// do nothing - it will just display the view
		}
	}
	
	public function termsAction() {
		
		$this->view->title = "Terms of service:";
		$this->view->headTitle($this->view->title, 'PREPEND');
		
	}
	public function helpAction() {
		$this->view->title = "User guide";
		$this->view->headTitle("Help for IHSA show secretary", 'PREPEND');
		
	}
	public function contactAction()
	{
		$auth = Zend_Auth::getInstance();
    	$model = new Model_DbTable_Contacts();
		$this->view->title = "Feedback";
		$this->view->headTitle("Horse show software feedback", 'PREPEND');
		
		$form = new Form_Contact();
		$this->view->form=$form;
		$this->view->title = 'Contact';

		if($this->getRequest()->isPost()){
			if($form->isValid($_POST)){
				$data = $form->getValues();

				$model->addComment($data);
				$this->_redirect('index/index');
			}
		} else { // we are rendering the form
			if ($auth->hasIdentity()) {
				$data = array(
				'USER_ID'=>$auth->getIdentity()->USER_ID,
				'FIRST_NAME'=>$auth->getIdentity()->FIRST_NAME,
				'LAST_NAME'=>$auth->getIdentity()->LAST_NAME,
				'EMAIL'=>$auth->getIdentity()->EMAIL,
				);
				$form->populate($data);
				//$form->FIRST_NAME = $auth->FIRST_NAME;
			}
		}
	}

	public function showsAction()
	{
		$this->view->title = "All Shows";
		$this->view->headTitle("Add, edit, delete show (hunter seat or western)", 'PREPEND');

		$rows = new Model_DbTable_Shows();
		$this->view->shows = $rows->fetchShows();
			
	}
	
	public function showdetailsAction() {


		$showid = $this->_getParam("show",0);
		//$m = new Model_DbTable_Shows();
		$u = new Model_DbTable_Users();
		$e = new Model_DbTable_EventTypes();
		$s = new Model_DbTable_Shows();
		$c = new Model_DbTable_Classes();

		$s->selectShow($showid);
		$show = $s->getShow($showid);

		$this->view->show = $show;
		$this->view->event = $e->fetchEventType($show['EVENT_TYPE_ID']);
		$this->view->user = $u->fetchUser($show['USER_ID']);
		$this->view->classes = $c->fetchClassesInOrder($showid);
		
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$userid = Zend_Auth::getInstance()->getIdentity()->USER_ID;
			$this->view->myinvitedteams = Model_DbTable_ShowTeams::getInstance()->fetchInvitedTeamsUser($userid, $showid);
		} else {
			$this->view->myinvitedteams = array();
		}

	}

}



