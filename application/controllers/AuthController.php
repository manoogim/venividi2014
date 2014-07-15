<?php
class AuthController extends Zend_Controller_Action
{


// public function indexAction()
//    {
//        $this->view->form = new Form_Login();
//    }
	public function loginAction()    {
		$users = new Model_DbTable_Users();
		$form = new Form_Login();
		$this->view->form = $form;
//		foreach ($this->_request->getParams() as $par) {
//			echo 'zz' .  $par . '=' . $this->_request->getParam($par);
//		}
		if($this->getRequest()->isPost()){
			if($form->isValid($_POST)){
				$data = $form->getValues();
				$auth = Zend_Auth::getInstance();

				$authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(),'user');
				$authAdapter->setIdentityColumn('email')
				->setCredentialColumn('password');

				$authAdapter->setIdentity($data['email'])
				->setCredential($data['password']);

				$result = $auth->authenticate($authAdapter);
				if($result->isValid()){
					$storage = new Zend_Auth_Storage_Session();
					$storage->write($authAdapter->getResultRowObject());
					$userid = $auth->getIdentity()->USER_ID;
					$model = new Model_DbTable_Shows();
					$model->selectMostRecentShow($userid);

					$url = '/home/shows/';
					$showid = $this->_request->getParam('show');
					
					if (isset ($showid)) $url = '/home/showdetails/show/' . $showid;
					$this->_redirect($url);
					
				} else {
					$this->view->errorMessage = "Invalid username or password. Please try again.";
				}
			} else {
				// not post
			}
		}
	}
	public function signupAction()    {
		$users = new Model_DbTable_Users();
		$form = new Form_Registration(array('termsurl'=> $this->view->baseUrl() . '/home/terms/'));
		$this->view->form=$form;
		$this->view->title = 'Register User';

		if($this->getRequest()->isPost()){
			if($form->isValid($_POST)){
				$data = $form->getValues();
				if($data['PASSWORD'] != $data['confirmPassword']){
					$this->view->errorMessage = "Password and confirm password don't match.";
					return;
				}
				if($users->checkUnique($data['EMAIL'])){
					$this->view->errorMessage = "Name already taken. Please choose another one.";
					return;
				}
				if ($data['serviceterms']!=1) {
					$this->view->errorMessage = "You must read and agree with the service terms.";
					return;
				}
				unset($data['serviceterms']);
				unset($data['confirmPassword']);
				unset($data['USER_ID']);
				$data['IP_ADDRESS'] = $_SERVER['REMOTE_ADDR'];
				$users->insert($data);
				$this->_redirect('auth/login');
			}
		}
	}
	
 public function profileAction()
    {
    	
     	$this->view->title = "Update Profile";
    	$this->view->headTitle($this->view->title, 'PREPEND');

    	// We instantiate our Form_Team,
    	// set the label for the submit button to “Add”
    	// and then assign to the view for rendering.
    	$form = new Form_Profile();   	
    	$this->view->form = $form;

    	$auth = Zend_Auth::getInstance();
    	$userid = $auth->getIdentity()->USER_ID;
    	
    	$model = new Model_DbTable_Users();
    	$dbuser = $model->fetchUser($userid);
    	// If the request object's isPost() method is true,
    	// then the form has been submitted
    	// and so we retrieve the form data from the request
    	// and check to see if it is valid using the isValid()
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->_request->getPost();
    	//	$this->logit('pwd=' . $formData['password']);
    	//	$this->logit('conf=' . $formData['confirmPassword']);
    		if ($form->isValid($formData)) {
    			
    			if ($dbuser['PASSWORD'] != $formData['OLD_PASSWORD']) {
    				$this->view->errorMessage = "Old password is incorrect.";
    				return;    				
    			}
    			if($formData['password'] != $formData['confirmPassword']){
    				$this->view->errorMessage = "Password and confirm password don't match.";
    				return;
    			}
    			$firstName = $formData['FIRST_NAME'];
    			$lastName = $formData['LAST_NAME'];
    			$password = $formData['password'];
    			$phone = $formData['PHONE'];
    			$model->updateUser($firstName,$lastName,$password,$userid, $phone);
				// if name canged, it is too visible to ignore
    			$auth->getIdentity()->FIRST_NAME = $firstName;
    			// after record was added, go back to the home page
    			$this->_redirect('/index/');

    			// If not valid, populate with user entered in and redisplay.
    		} else {
    			$form->populate($formData);
    		}
    	}  // when displaying the form, retrieve the records from db database and populate the form.           
         else {

               
                $form->populate($model->fetchUser($userid));
            
        } 
       
    }
	
	public function logoutAction()    {
		$storage = new Zend_Auth_Storage_Session();
		$storage->clear();
		
		$ns = new Zend_Session_Namespace('myshows');
		unset($ns->showid);
		unset($ns->show);
		$this->_redirect('auth/login');
	}

	



//function logit($xtra) {
//	// YYYYmmdd-hhii
//	$tstamp = date('Ymd-hi' , time());
//	$fname = "c:/aaa/mylog.log" ;
//	//mkdir($fname);
//	$logme = 'xtra=' . $xtra . ' ,nnnnn ' . ";\n";
//	$fh = fopen($fname,'a');
//
//	if (flock($fh, LOCK_EX))
//	{
//		fwrite($fh, $logme) ;
//		flock($fh, LOCK_UN);
//	}
//
//	fclose($fh);
//
//}
}