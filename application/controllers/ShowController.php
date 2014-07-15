<?php

class ShowController extends Zend_Controller_Action
{
	public function preDispatch()
	{
		if (Zend_Auth::getInstance()->hasIdentity()) {
		} else {
			$url =  '/auth/login/' ;
			foreach ($this->_request->getParams() as $par) {
				$url = $url .  $par . '/' . $this->_request->getParam($par) . '/';
			}

			$this->_redirect($url);
		}
	}
	public function init()
	{
		/* Initialize action controller here */
	}




	public function editAction()
	{
			
		$this->view->title = "Edit show";
		$this->view->headTitle($this->view->title, 'PREPEND');

		$form = new Form_Show();
		$form->submit->setLabel('Save');
		$this->view->form = $form;

		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				$userid = Zend_Auth::getInstance()->getIdentity()->USER_ID;
				$showid = $form->getValue('SHOW_ID');
				$eventtypeid = $form->getValue('EVENT_TYPE_ID');
				$showname = $form->getValue('SHOW_NAME');
				$showdate = $form->getValue('SHOW_DATE');
				$closedate = $form->getValue('CLOSE_DATE');
				$invitedzone = $form->getValue('ZONE_ID');
				$invitedrgn = $form->getValue('RGN_ID');
				$address = $form->getValue('ADDRESS');
				$judge = $form->getValue('JUDGE');
				$steward = $form->getValue('STEWARD');
				$hh = $form->getValue('HORSEHOLDER');
				$anno = $form->getValue('ANNOUNCER');
				$padock = $form->getValue('PADOCK_MASTER');
				$jumping = $form->getValue('JUMPING_MASTER');
				$medical = $form->getValue('MEDICAL');
				$misc = $form->getValue('MISC_OTHER');
				$showtime = $form->getValue('SHOW_TIME');
				$regtime = $form->getValue('REGISTRATION_TIME');
				$entryfee = $form->getValue('ENTRY_FEE');
				$checkpayable = $form->getValue('CHECK_PAYABLE');
				$classorder = $form->getValue('CLASS_ORDER');
				$rows = new Model_DbTable_Shows();

				$data = array(
					'user_id' => $userid,
					'EVENT_TYPE_ID' => $eventtypeid,
					'show_name' => $showname,
					'show_time' => $showtime,
				'registration_time' => $regtime,
					'show_date' => $showdate,
					'close_date' => $closedate,
					'INVITED_ZONE' => $invitedzone,
					'INVITED_RGN' => $invitedrgn,
					'entry_fee' => $entryfee,
				'CHECK_PAYABLE' => $checkpayable,
					'class_order' => $classorder,
					'address' => $address,
					'judge' => $judge,
					'steward' => $steward,
					'horseholder' => $hh,
					'announcer' => $anno,
					'padock_master' => $padock,
					'jumping_master' => $jumping,
					'medical' => $medical,
					'misc_other' => $misc
				);
				$rows->updateShow($showid, $data);
				// after record was added, go back to the home page
				$this->_redirect('/home/showdetails/show/' . $showid);

				// If not valid, populate with user entered in and redisplay.
			} else {
				$form->populate($formData);
			}
		}  // when displaying the form, retrieve the records from db database and populate the form.
		else {
			$id = $this->_request->getParam('show',0);

			if ($id > 0) {

				$shows = new Model_DbTable_Shows();
				$sel = $shows->selectShow($id);
				$theshow = $shows->getShow($id);
				$form->populate($theshow);
				$form->getElement('ZONE_ID')->setValue($theshow['INVITED_ZONE']);
				$form->getElement('RGN_ID')->setValue($theshow['INVITED_RGN']);
				// can't change show type on editing
				//				$form->removeElement('EVENT_TYPE_ID');
			}
		}
			
	}

	public function addAction()
	{
		$this->view->title = "Add show";
		$this->view->headTitle($this->view->title, 'PREPEND');

		// We instantiate our Form_Team,
		// set the label for the submit button to “Add”
		// and then assign to the view for rendering.
		$form = new Form_Show();
		$form->submit->setLabel('Add');
		$this->view->form = $form;


		// If the request object's isPost() method is true,
		// then the form has been submitted
		// and so we retrieve the form data from the request
		// and check to see if it is valid using the isValid()
		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				$showname = $form->getValue('SHOW_NAME');
				$showdate = $form->getValue('SHOW_DATE');
				$closedate = $form->getValue('CLOSE_DATE');
				$eventtypeid = $form->getValue('EVENT_TYPE_ID');
				$invitedzone = $form->getValue('ZONE_ID');
				$invitedrgn = $form->getValue('RGN_ID');
				$address = $form->getValue('ADDRESS');
				$judge = $form->getValue('JUDGE');
				$steward = $form->getValue('STEWARD');
				$hh = $form->getValue('HORSEHOLDER');
				$anno = $form->getValue('ANNOUNCER');
				$padock = $form->getValue('PADOCK_MASTER');
				$jumping = $form->getValue('JUMPING_MASTER');
				$medical = $form->getValue('MEDICAL');
				$showtime = $form->getValue('SHOW_TIME');
				$regtime = $form->getValue('REGISTRATION_TIME');
				$entryfee = $form->getValue('ENTRY_FEE');
				$checkpayable = $form->getValue('CHECK_PAYABLE');
				$classorder = $form->getValue('CLASS_ORDER');
				$misc = $form->getValue('MISC_OTHER');
				$rows = new Model_DbTable_Shows();
				$userid=Zend_Auth::getInstance()->getIdentity()->USER_ID;
				$data = array(
					'user_id' => $userid,
					'EVENT_TYPE_ID' => $eventtypeid,
					'show_name' => $showname,
					'show_time' => $showtime,
				'registration_time' => $regtime,
					'show_date' => $showdate,
					'close_date' => $closedate,
					'INVITED_ZONE' => $invitedzone,
					'INVITED_RGN' => $invitedrgn,
					'entry_fee' => $entryfee,
					'CHECK_PAYABLE' => $checkpayable,
					'class_order' => $classorder,
					'address' => $address,
					'judge' => $judge,
					'steward' => $steward,
					'horseholder' => $hh,
					'announcer' => $anno,
					'padock_master' => $padock,
					'jumping_master' => $jumping,
					'medical' => $medical,
					'misc_other' => $misc
				);

				$rows->addShow($data);
				// after record was added, go back to the home page
				$this->_redirect('/home/shows/');

				// If not valid, populate with user entered in and redisplay.
			} else {
				$form->populate($formData);
			}
		}
	}

	public function deleteAction()
	{
		$this->view->title = "Delete show";
		$this->view->headTitle($this->view->title, 'PREPEND');
		// shouldn't do an irreversible action using GET but should use POST
		if ($this->getRequest()->isPost()) {
			$del = $this->getRequest()->getPost('del');
			if ($del == 'Yes') {
				$id = $this->getRequest()->getPost('showid');
				$teams = new Model_DbTable_Shows();
				$teams->deleteShow($id);
			}
			$this->_redirect('/home/shows/');
		} else {
			$id = $this->_getParam('show', 0);

			$rows = new Model_DbTable_Shows();
			$this->view->show = $rows->getShow($id);
		}
	}

	public function selectAction()
	{
		$showid = $this->_getParam("show",0);

		$m = new Model_DbTable_Shows();
		$sel = $m->selectShow($showid);

		$this->_redirect('/home/showdetails/');
		// no need for a view to display
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

	}

	public function inviteAction() {
		$zones = Model_DbTable_Zones::getInstance()->fetchZones();
		$this->view->zones = $zones;
		//$this->_helper->layout->disableLayout();
	}

	//	private function logit($xtra) {
	//		// YYYYmmdd-hhii
	//		$tstamp = date('Ymd-hi' , time());
	//		$fname = "c:/aaa/mylog.log" ;
	//		//mkdir($fname);
	//		$logme = 'xtra=' . $xtra . ' ,nnnnn ' . ";\n";
	//		$fh = fopen($fname,'a');
	//
	//		if (flock($fh, LOCK_EX))
	//		{
	//			fwrite($fh, $logme) ;
	//			flock($fh, LOCK_UN);
	//		}
	//
	//		fclose($fh);
	//
	//	}
	public function entryAction() {
		//  we arecomming from selecting a show row ..
		// make this show current
		$showid = $this->_getParam('show',0);
			
		$shows = new Model_DbTable_Shows();
		$selshow = $shows->selectShow($showid);

		$e = new Model_DbTable_Entries();

		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			//$this->logit(json_encode($formData));

			$e->updateEntriesAll($showid, $formData);
			$this->_redirect('/home/showdetails/');
		} else {

			// load classes and students and pass to the view
			$c = new Model_DbTable_Classes();
			$clazzes = $c->fetchClasses($selshow->typeid);

			$teamid=$this->_getParam('teamid', 0);
			$teamname = $this->_getparam('teamName','myteam');

			$s = new Model_DbTable_Students();
			$students = $s->fetchStudents($teamid);


			$entries = $e->fetchTeamEntries($showid, $students, $clazzes);

			$this->view->teamname = $teamname;
			$this->view->clazzes = $clazzes;
			$this->view->students = $students;
			$this->view->entries = $entries;
		}
	}

	public function copyAction() {
		$showid = $this->_getParam("show",0);
		$m = new Model_DbTable_Shows();
		$newshowid = $m->copyShow($showid);
		$this->_redirect('/show/edit/show/' . $newshowid);
	}



	public function emailAction() {
		$showid = $this->_getParam("show",0);
		$this->view->title='Invited teams :';
		$coaches = Model_DbTable_ShowTeams::getInstance()->fetchInvitedTeamsAll($showid);
		$this->view->coaches = $coaches;
		$this->view->showid = $showid;
		$showlink = APPLICATION_DOMAIN . '/home/showdetails/show/' . $showid;
		//			$this->view->showlink = $showlink;

		if ($this->getRequest()->isPost()) {
			$showid = $this->_getParam('show','777');

			$m = new Model_DbTable_Users();
			$myid = Zend_Auth::getInstance()->getIdentity()->USER_ID;
			//			var_dump($coaches);
			$m->emailShowLink($myid, $showlink, $coaches);
			$goto = '/home/showdetails/show/' . $showid;
			$this->_redirect($goto);
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
		if ($this->getRequest()->isPost()) {
			$showid = $this->_getParam('show','777');

			$myid = Zend_Auth::getInstance()->getIdentity()->USER_ID;

			$m = new Model_DbTable_Users();
			//			var_dump($teams);
			$m->emailConfirmPage($myid,$teams);

			$goto = '/home/showdetails/show/' . $showid;
			$this->_redirect($goto);

		}
	}
	public function uploadAction()
	{
		$this->view->headTitle('Use Custom Logo (Optional)');
		$this->view->title = 'Use Custom Logo (Optional)';
		$this->view->bodycopy = 'Please select a .png or a .jpg file';

		$userid = $this->_request->getParam('logo',0);
		$showid = $this->_request->getParam('show',0);
		$eventtypeid = $this->_request->getParam('eventtype',0);
		
			$eve = Model_DbTable_EventTypes::getInstance()->fetchEventType($eventtypeid);
			$this->view->defaultlogo = $eve['LOGO_NAME'];
		
		$this->view->showid = $showid;
		$this->view->userid = $userid;
		$this->view->eventtypeid = $eventtypeid;

		$logopath = Model_DbTable_Users::makeLogoPath($userid);
		if (file_exists($logopath)) {
			$this->view->logopath = $logopath;
		} else {
			$this->view->nologo = $logopath;
		}

		$form = new Form_Upload();
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {

				// success - do something with the uploaded file
				$uploadedData = $form->getValues();

				$fullFilePath = $form->customlogo->getFileName();
				$path_info = pathinfo($fullFilePath);
				$ext ='.' . $path_info['extension'];

				$newFilePath = dirname($fullFilePath) . '/logo' . $userid . $ext;
				// Rename uploaded file using Zend Framework
				$filterFileRename = new Zend_Filter_File_Rename(array('target' => strtolower($newFilePath), 'overwrite' => true));

				$filterFileRename -> filter($fullFilePath);


				//				Zend_Debug::dump($uploadedData, '$uploadedData');
				//				Zend_Debug::dump($fullFilePath, '$fullFilePath');

				$goto = '/show/upload/logo/' . $userid . '/show/' . $showid . '/eventtype/' . $eventtypeid;
				$this->_redirect($goto);

			} else {
				$form->populate($formData);
			}
		} else { // not post

		}

		$this->view->form = $form;

	}

	public function restorelogoAction() {
		//		$this->_helper->viewRenderer->setNoRender();
		//		$this->_helper->layout->disableLayout();
		$userid = $this->_request->getParam('logo',0);
		$showid = $this->_request->getParam('show',0);
		$eventtypeid = $this->_request->getParam('eventtype',0);

		Model_DbTable_Users::deleteUserLogo($userid);

		$goto = '/show/upload/logo/' . $userid . '/show/' . $showid . '/eventtype/' . $eventtypeid;
		$this->_redirect($goto);
	}


}



