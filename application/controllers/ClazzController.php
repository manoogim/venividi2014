<?php

class ClazzController extends Zend_Controller_Action
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
    	$ns = new Zend_Session_Namespace('myshows');
        $this->view->title = "Manage show entries";
        $this->view->headTitle("Class entries", 'PREPEND');
        $model = new Model_DbTable_Classes();
        $rowidx = $this->getRequest()->getUserParam('permute',7777);
        // $this->getRequest()->
        if ($rowidx ==7777) {
        	$this->view->classes = $model->fetchClassesAndCounts($ns->showid);
        } else {
        	$this->view->classes = $model->permuteClasses($ns->showid,$rowidx);
        }
      
//     $this->_invokeArgs=null;
//        $this->_frontController->resetInstance();
//$this->getRequest()->setParam('permute',7777);
$this->getFrontController()->getRouter()->clearParams('permute');
    }



}



