<?php

class StudentController extends Zend_Controller_Action
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
		$this->view->title = "All Students";
		$this->view->headTitle('Add,edit,delete student rider', 'PREPEND');

		$students = new Model_DbTable_Students();
		$teamId = $this->_request->getParam('team',0);
		$this->view->teamId = $teamId;
		$this->view->owner = $this->_request->getParam('owner',0);
		$teamName = $this->_request->getParam('teamName','');

		if ($teamName != '') {
			$this->view->title = 'Team: ' . $teamName;
		}
		$this->view->students = $students->fetchStudents($teamId);


			
	}

	private function _targeturl($userid, $teamId, $typeid, $teamname) {
		$ret = '/owner/' . $userid . '/team/'. $teamId . '/type/' . $typeid . '/teamName/' . $teamname;
		return $ret;
	}
	public function editAction()
	{
		$this->view->title = "Edit rider";
		$this->view->headTitle($this->view->title, 'PREPEND');

		// We instantiate our Form_Student
		// set the label for the submit button to “Add”
		// and then assign to the view for rendering.
		$where = $this->_getParam('where','teamsski');
		$typeid = $this->_getParam('type','77');
		$teamname = preg_replace("/ /","+", $this->_getParam('teamName','77'));
		$form = new Form_Student(array('typeid'=>$typeid));
		$form->submit->setLabel('Save');
		$this->view->form = $form;


		// If the request object's isPost() method is true,
		// then the form has been submitted
		// and so we retrieve the form data from the request
		// and check to see if it is valid using the isValid()
		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				$studentId = $form->getValue('STUDENT_ID');
				$teamId = $form->getValue('TEAM_ID');
				$userid = $form->getValue('owner');
				$firstName = $form->getValue('FIRST_NAME');
				$lastName = $form->getValue('LAST_NAME');
				$backNumber = $form->getValue('BACK_NUMBER');
				$class1 = $form->getValue('CLASS1_ID');
				$class2 = $form->getValue('CLASS2_ID');
				$data = array(

				'first_name' => $firstName,
				'last_name' => $lastName,
				'team_id' => $teamId,
				'back_number' => $backNumber,
				'class1_id' => $class1,
				'class2_id' => $class2,
				);
				$students = new Model_DbTable_Students();
				$students->updateStudent($studentId, $data);
				//$students->updateStudent($studentId,$firstName,$lastName,$teamId, $backNumber, $class1, $class2);
				$this->getRequest()->setParam('teamid', $teamId);
				
				// after record was edited, go back to where we came from
				$ns = new Zend_Session_Namespace('myshows');
				$show = $ns->showid;
				$gdeposleroot = $where == 'entry' ? '/entry/entry/show/' . $show : '/student/index';
				//$gdeposle =  $gdeposleroot . '/owner/' . $userid . '/team/'.$teamId . '/type/' . $typeid . '/teamName/' . $teamname;
				$gdeposle = $gdeposleroot . $this->_targeturl ($userid, $teamId, $typeid, $teamname);
				$this->_redirect($gdeposle);

				// If not valid, populate with user entered in and redisplay.
			} else {
				$form->populate($formData);
			}
		}  // when displaying the form, retrieve the records from db database and populate the form.
		else {
			$id = $this->_request->getParam('student',0);

			if ($id > 0) {
				$students = new Model_DbTable_Students();
				$form->populate($students->getStudent($id));
				$hidden = $form->getElement('owner');
				$hidden->setValue($this->_getParam('owner','77'));
			}
		}
	}

	public function addAction()
	{
		$this->view->title = "Add rider";
		$this->view->headTitle($this->view->title, 'PREPEND');

		// We instantiate our Form_Student
		// set the label for the submit button to “Add”
		// and then assign to the view for rendering.
		$typeid = $this->_getParam('type','77');
		$form = new Form_Student(array('typeid'=>$typeid));
		$form->submit->setLabel('Add');
		$this->view->form = $form;


		// If the request object's isPost() method is true,
		// then the form has been submitted
		// and so we retrieve the form data from the request
		// and check to see if it is valid using the isValid()
		$fc = Zend_Controller_Front::getInstance();
		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				$firstName = $form->getValue('FIRST_NAME');
				$lastName = $form->getValue('LAST_NAME');
				$teamId = $this->_getParam('team',0);
				$userid = $form->getValue('owner');
				$class1 = $form->getValue('CLASS1_ID');
				$class2 = $form->getValue('CLASS2_ID');
				$backNumber = $form->getValue('BACK_NUMBER');
				$students = new Model_DbTable_Students();
				$data = array(
					'first_name' => $firstName,
					'last_name' => $lastName,
					'team_id' => $teamId,
					'back_number' => $backNumber,
					'class1_id' => $class1,
					'class2_id' => $class2,
				);
				$students->addStudent($data);

				// after record was added, go back to the home page
				$this->_redirect('/student/index/owner/' . $userid . '/team/'.$teamId . '/type/' . $typeid);
				$this->_redirect($url);

				// If not valid, populate with user entered in and redisplay.
			} else {
				$form->populate($formData);
				$teamId = $this->_getParam('team',0);
					
			}
		} else {
			$hidden = $form->getElement('owner');
			$hidden->setValue($this->_getParam('owner','77'));

			//    		$typeid = $form->getElement('typeid');
			//    		$typeid->setValue($this->_getParam('type','77'));
		}
	}

	public function deleteAction()
	{
		$this->view->title = "Delete student";
		$this->view->headTitle($this->view->title, 'PREPEND');
		$typeid = $this->_getParam('type','77');
		$teamname = preg_replace("/ /","+", $this->_getParam('teamName','77'));
		$teamId = $this->_getParam('team','77');
		$userid = $this->_getParam('owner','77');
		// shouldn't do an irreversible action using GET but should use POST
		if ($this->getRequest()->isPost()) {
			$del = $this->getRequest()->getPost('del');
			
			if ($del == 'Yes') {
				$id = $this->getRequest()->getPost('studentid');
				echo 'post' . $id;
				$albums = new Model_DbTable_Students();
				$albums->deleteStudent($id);
			}
			$gdeposle = '/student/index' . $this->_targeturl ($userid, $teamId, $typeid, $teamname);
			$this->_redirect($gdeposle);
		} else {
			$id = $this->_getParam('student', 0);

			$students = new Model_DbTable_Students();
			$this->view->student = $students->getStudent($id);
			//            $hidden = $form->getElement('owner');
			//    		$hidden->setValue($this->_getParam('owner','77'));
		}
	}

	public function hatetypingAction() {
		$this->view->title = "Enter or paste roster";
		$this->view->headTitle($this->view->title, 'PREPEND');
		$form = new Form_Hatetyping();
		$this->view->form = $form;
		$typeid = $this->_getParam('type','77');

		// If the request object's isPost() method is true,
		// then the form has been submitted
		// and so we retrieve the form data from the request
		// and check to see if it is valid using the isValid()
		$fc = Zend_Controller_Front::getInstance();
		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {

				$hugetext = $form->getValue('HUGETEXT');
				$teamId = $this->_getParam('team',0);
				$userid = $form->getValue('owner');
				$students = new Model_DbTable_Students();
				$students->hateTyping($typeid, $teamId, $hugetext);

				// after record was added, go back to the home page
				$this->_redirect('/student/index/owner/' . $userid . '/team/'.$teamId . '/type/' . $typeid);
				$this->_redirect($url);

				// If not valid, populate with user entered in and redisplay.
			} else {
				$form->populate($formData);
				$teamId = $this->_getParam('team',0);
					
			}
		} else {
			$hidden = $form->getElement('owner');
			$hidden->setValue($this->_getParam('owner','77'));

		}
			
	}

}



