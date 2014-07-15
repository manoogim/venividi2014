<?php
class My_View_Helper_BuildMenuItem
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
    
    private function buildItemLogin() {
    	$auth = Zend_Auth::getInstance();
    	if ($auth->hasIdentity()) {
    		$ret ='';
    	} else {
    		$urlhome = $this->view->url(array('controller'=>'auth', 'action'=>'login'));
    		$ret =  '<a href="' . $urlhome . '">Login</a>';
    	}
    	return $ret;
    }
    private function buildItemLogout() {
    	$auth = Zend_Auth::getInstance();
    	if (!$auth->hasIdentity()) {
    		$ret ='';
    	} else {
    		$urlhome = $this->view->url(array('controller'=>'auth', 'action'=>'logout'));
    		$ret =  '<a href="' . $urlhome . '">Logout</a>';
    	}
    	return $ret;
    }

    private function buildItemTeam() {
    	$auth = Zend_Auth::getInstance();
    	if (!$auth->hasIdentity()) {
    		$ret ='';
    	} else {
    		$urlhome = $this->view->url(array('controller'=>'team', 'action'=>'index'));
    		$ret =  '<a href="' . $urlhome . '">Team</a>';
    	}
    	return $ret;
    }
    private function buildItemStudent() {
    	$auth = Zend_Auth::getInstance();
    	if (!$auth->hasIdentity()) {
    		$ret ='';
    	} else {
    		$urlhome = $this->view->url(array('controller'=>'student', 'action'=>'index'));
    		$ret =  '<a href="' . $urlhome . '">Student</a>';
    	}
    	return $ret;
    }
    
    private function buildItemEntry() {   	
    	$ns = new Zend_Session_Namespace('myshows');
    	if (!Zend_Auth::getInstance()->hasIdentity()) {
    		$ret ='';
    	} else if (!isset($ns->showid)) {
    		$ret ='';
    	} else {
    		$urlhome = $this->view->url(array('controller'=>'student', 'action'=>'index'));
    		$ret =  '<a href="' . $urlhome . '">Student</a>';
    	}
    	return $ret;
    }
    function buildMenuItem($tag) {
    	
    }
}