<?php

/** these are actions for not loggedin user */
class HorseController extends Zend_Controller_Action
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



	public function indexAction()
	{
		$this->view->title = "Manage Horses";
		$this->view->headTitle('Add, edit,delete horse', 'PREPEND');

		$auth = Zend_Auth::getInstance();
		$userid = $auth->getIdentity()->USER_ID;

		$model = new Model_DbTable_Horses();

		$this->view->horses = $model->fetchHorsesUser($userid);

	}

	public function editAction()
	{
			
		$this->view->title = "Edit horse";
		$this->view->headTitle($this->view->title, 'PREPEND');

		// We instantiate our Form_Team,
		// set the label for the submit button to “Add”
		// and then assign to the view for rendering.
		$form = new Form_Horse();
		$form->submit->setLabel('Save');
		$this->view->form = $form;


		// If the request object's isPost() method is true,
		// then the form has been submitted
		// and so we retrieve the form data from the request
		// and check to see if it is valid using the isValid()
		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();
			
			if ($form->isValid($formData)) {
				$horseid = $form->getValue('HORSE_ID');
				$horsename = $form->getValue('HORSE_NAME');
				$horsedesc = $form->getValue('HORSE_DESC');
				$wlimit = $form->getValue('WEIGHT_LIMIT');
				$hlimit = $form->getValue('HEIGHT_LIMIT');
				$barn = $form->getValue('BARN');
				$breed = $form->getValue('BREED');
				$color = $form->getValue('COLOR');
				$visina = $form->getValue('HEIGHT');
				
				$gender = $form->getValue('GENDER');
				$crop = $form->getValue('CROP');
				$spurs = $form->getValue('SPURS');
				
				$userid = Zend_Auth::getInstance()->getIdentity()->USER_ID;
					
				$model = new Model_DbTable_Horses();
				$model->updateHorse($horseid,$userid,$horsename,$horsedesc,$wlimit, $hlimit, $barn,
							$breed, $color, $visina, $gender, $crop, $spurs);

				// after record was added, go back to the home page
				$this->_redirect('/horse/');

				// If not valid, populate with user entered in and redisplay.
			} else {
				$form->populate($formData);
			}
		}  // when displaying the form, retrieve the records from db database and populate the form.
		else {
			$id = $this->_request->getParam('horse',0);

			if ($id > 0) {
				$teams = new Model_DbTable_Horses();
				$form->populate($teams->fetchHorse($id));
			}
		}
			
	}

	public function addAction()
	{
		$this->view->title = "Add horse";
		$this->view->headTitle($this->view->title, 'PREPEND');

		$form = new Form_Horse();
		$form->submit->setLabel('Add');
		$this->view->form = $form;

		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				$horseid = $form->getValue('HORSE_ID');
				$horsename = $form->getValue('HORSE_NAME');
				$horsedesc = $form->getValue('HORSE_DESC');
				$wlimit = $form->getValue('WEIGHT_LIMIT');
				$hlimit = $form->getValue('HEIGHT_LIMIT');
				$barn = $form->getValue('BARN');
				$breed = $form->getValue('BREED');
				$color = $form->getValue('COLOR');
				$visina = $form->getValue('HEIGHT');
				
				$gender = $form->getValue('GENDER');
				$crop = $form->getValue('CROP');
				$spurs = $form->getValue('SPURS');
				
				$userid = Zend_Auth::getInstance()->getIdentity()->USER_ID;
					
				$model = new Model_DbTable_Horses();
				$model->addHorse($userid, $horsename, $horsedesc, $wlimit, $hlimit, $barn,
				 $breed, $color, $visina, $gender, $crop, $spurs);

				// after record was added, go back to the home page
				$this->_redirect('/horse/');

				// If not valid, populate with user entered in and redisplay.
			} else {
				$form->populate($formData);
			}
		}
	}

	public function deleteAction()
	{
		$model = new Model_DbTable_Horses();
		$this->view->title = "Delete horse";
		$this->view->headTitle($this->view->title, 'PREPEND');
		// shouldn't do an irreversible action using GET but should use POST
		if ($this->getRequest()->isPost()) {
			
			$del = $this->getRequest()->getPost('del');
			if ($del == 'Yes') {
				$id = $this->getRequest()->getPost('horseid');
				$model->deleteHorse($id);
			}
			$this->_redirect('/horse/');
		} else {
			$id = $this->_getParam('horse', 0);
			$this->view->horse = $model->fetchHorse($id);
		}
	}

	public function drawAction() {
	$this->view->title = 'Horse usage';
		$auth = Zend_Auth::getInstance();
		$userid = $auth->getIdentity()->USER_ID;
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;

		$d = new Model_DbTable_HorseDraw();
		$p = new Model_DbTable_Programs();
		
		if ($this->getRequest()->isPost()) {
			$del = $this->getRequest()->getPost('submit');
			$formData = $this->getRequest()->getPost();

			$d->updateHorseDraw($showid, $formData);
			$this->_redirect('/horse/');
			
		} else {
			$h = new Model_DbTable_Horses();
			$horses = $h->fetchHorsesUser($userid);			
			
			$sects = $p->fetchSections($showid);

			$multiarr = $d->fetchHorseDraws($showid, $horses, $sects);
			$cnts = $multiarr[0];
			$draws = $multiarr[1];
			
			$this->view->horses = $horses;
			$this->view->sects = $sects;
			$this->view->draws = $draws;
			$this->view->cnts = $cnts;

		}


	}

	function logit($xtra) {
		// YYYYmmdd-hhii
		$tstamp = date('Ymd-hi' , time());
		$fname = "c:/aaa/mylog.log" ;
		//mkdir($fname);
		$logme = '===' . $xtra . ' ,=== ' . ";\n";
		$fh = fopen($fname,'a');

		if (flock($fh, LOCK_EX))
		{
			fwrite($fh, $logme) ;
			flock($fh, LOCK_UN);
		}

		fclose($fh);

	}
}



