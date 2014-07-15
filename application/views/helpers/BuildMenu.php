<?php
class My_View_Helper_BuildMenu
{
	public $view;

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
	function buildMenu() {
			
		$auth = Zend_Auth::getInstance();
		$ns = new Zend_Session_Namespace('myshows');
		$showid = $ns->showid;
		$str = '';

		if ($auth->hasIdentity()) {
			$urlshow = $this->view->baseUrl() . '/home/shows/';
			$str=$str . '<a href="' . $urlshow . '"> Shows</a>';



			$urlteam = $this->view->baseUrl() . '/team/index/';
			$str=$str . ' | <a href="' . $urlteam . '">Riders</a>';

			$urlhorse = $this->view->baseUrl() . '/horse/index/';
			$str=$str . ' | <a href="' . $urlhorse . '">Horses</a>';

			$model = new Model_DbTable_Entries();
			$flg = isset($showid) && $model->hasEntries($showid) && $ns->show->owner==$auth->getIdentity()->USER_ID;
			if ($flg) {
				$urlclazz = $this->view->baseUrl() .'/clazz/index/';
				$str=$str . ' | <a href="' . $urlclazz . '">Program</a>';
					
				$urlprt = $this->view->baseUrl() .'/report/index/';
				$str=$str . ' | <a href="' . $urlprt . '">Print</a>';

			}

				
		}
		return $str;
	}
}