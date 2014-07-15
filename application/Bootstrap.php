<?php


class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	// method is called automatically by Zend_Application as it starts with _init
	protected function _initAutoload()
	{
		$loader = Zend_Loader_Autoloader::getInstance();      
		
		$arr = array('namespace'=>'', 'basePath' => APPLICATION_PATH);
		$moduleLoader = new Zend_Application_Module_Autoloader($arr);
		return $moduleLoader;
	}
	
	// When dispatching, Zend_Layout will look for a layout view script 
	// called layout.phml in the application/layouts directory,
	protected function _initViewHelpers()
	{
		// use the bootstrap() member variable 
		// to ensure that the layout resource is initialised
		$this->bootstrap('layout');
		
		// retrieve the Zend_Layout object using getResource()
		$layout = $this->getResource('layout');

		// retrieve the view using the layout's getView() method
		$view = $layout->getView();
		
		// call various helper methods to set up ready for rendering later
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
		$view->headMeta()->appendName('google-site-verification','O5t0xR9evEaQMgYxiBhTVMH1HejpFwLb8vkwsmwpFis');
		$view->headMeta()->appendName('Description','IHSA style show software for Hunter Seat and Western riding, generates entries report and class splits.');
		$view->headMeta()->appendName('keywords','horse,show,riding,huntseat,western,ihsa,iea,interscholastic,hoofpicks,showpro,management,class,division,program,split,class split, back number, walk,trot,canter,equitation,over fences,software,open,varsity,novice,futures');
		$view->headTitle()->setSeparator(' - ');
	
		$view->headTitle(APP_NAME . ' IHSA show program');
		
		$view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'My_View_Helper');
	}

}



