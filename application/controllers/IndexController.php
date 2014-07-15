<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    public function preDispatch() {
//    	if (APPLICATION_DOWN) {
//    		$url =  '/home/down/' ;
//    	} else 
    	if (!Zend_Auth::getInstance()->hasIdentity()) {
    		$url =  '/auth/login/' ;
    		$this->_redirect($url);
    	};
    	
    	
    }


    public function indexAction()
    {
        $this->view->title = "Welcome to Veni, Vidi, Vici!";
        $headtitle = "Setup show, Manage teams, students, entries";
        $this->view->headTitle($headtitle, 'PREPEND');
        
    }


    

    
}



