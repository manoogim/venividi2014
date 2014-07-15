<?php
class Form_Login extends Zend_Form{
	
public function __construct($options = null)
	{
		parent::__construct($options);
		
		$username = $this->createElement('text','email');
		$username->setLabel('Email:')
		->setRequired(true)
//		->setAttrib('size','27')
		->setAttrib('maxlength','35')
		->setAttrib('style','width:250px;');
		;
		 
		$password = $this->createElement('password','password');
		$password->setLabel('Password:')
		->setRequired(true)
//		->setAttrib('size','27')
		->setAttrib('maxlength','15')
		->setAttrib('style','width:250px;');
		;
		;
		 // style="width:150px;" 
		$signin = $this->createElement('submit','signin');
		$signin->setLabel('Sign in')
		->setIgnore(true);
		
		
		$this->setName('login');

		$this->addElements(array($username, $password, $signin));
		
		// render the form into a table: 
		$formDeco = array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'table')),
			'Form' );
		$this->setDecorators($formDeco);
        
        // need to ensure the elements and labels are in table data and table rows
        $elemDeco = array(
	        'ViewHelper', 
	        'Errors', 
	        array('decorator' => array('td' => 'HtmlTag'), 'options' => array('tag' => 'td')),
	        array('Label', array('tag' => 'td')),
	        array('decorator' => array('tr' => 'HtmlTag'), 'options' => array('tag' => 'tr')),
	        );
        $this->setElementDecorators($elemDeco);
      $signin->removeDecorator('Label');
	}

}