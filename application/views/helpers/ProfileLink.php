<?php
/**
 * ProfileLink helper
 *
 * Call as $this->profileLink() in your layout script
 */
class My_View_Helper_ProfileLink
{
	public $view;

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}


	private function isAuthenticated() {
		$auth = Zend_Auth::getInstance();
		$ans = $auth->hasIdentity();
		return $ans;
	}
	public function profileLink($FLAG)
	{
		$ret = '';
		$auth = Zend_Auth::getInstance();
		$urlshows =  $this->view->baseUrl() . '/home/shows/';
		
		if ($auth->hasIdentity()) {
			$username = $auth->getIdentity()->FIRST_NAME;
			$profileurl = $this->view->baseUrl() . '/auth/profile/';
			$logouturl = $this->view->baseUrl() . '/auth/logout/';
			$ret= $ret . ' Welcome, ' . $username .
			'  |  <a href="' . $urlshows . '"> Home </a>' .
            '  |  <a href="' . $profileurl . '"> Profile </a> ' .
            '  |  <a href="' . $logouturl . '" > Logout </a>  '            
            ;
			//return $ret;
		} else {
				

			$urlhome = $this->view->url(array('controller'=>'auth', 'action'=>'login'));
			$ret = $ret . '<a href="' . $urlhome . '">Login </a> '
			. '  |  <a href="' . $urlshows . '"> Home </a>' ;
			;
		}
		$contacturl = $this->view->baseUrl() . '/home/contact/';
		$ret = $ret . ' | <a href="' . $contacturl . '"> Contact  </a> ';
		 
		$helpurl = $this->view->baseUrl() . '/home/help/';
		$ret = $ret . ' | <a href="' . $helpurl . '"> Help |</a> ';
		return $ret;

	}


}
