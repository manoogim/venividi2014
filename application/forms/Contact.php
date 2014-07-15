<?php
class Form_Contact extends Zend_Form{
	public function __construct($options = null)
	{
		parent::__construct($options);
		
		$this->setName('contact');
		$id = new Zend_Form_Element_Hidden('USER_ID');		

		$firstname = $this->createElement('text','FIRST_NAME');
		$firstname->setLabel('* First Name:')
				->setAttrib("maxlength","15")
		->setAttrib("size","35")
		->setRequired(true);

		$lastname = $this->createElement('text','LAST_NAME');
		$lastname->setLabel('* Last Name:')
				->setAttrib("maxlength","15")
		->setAttrib("size","35")
		->setRequired(true);

		$email = $this->createElement('text','EMAIL');
		$email->setLabel('* Email:')
				->setAttrib("maxlength","35")
		->setAttrib("size","35")
		->setRequired(true)
		->addValidator('EmailAddress');

		$m = new Model_DbTable_Contacts();
		$options = $m->getOptions();
		$subject = $this->createElement('select', 'SUBJECT');
		$subject->setLabel('* Subject:')
		->setRequired(true)
		->addMultiOption('','--Select One--')
		->addMultiOptions($options);
		
		$comment = $this->createElement('textarea', 'COMMENT');
		$comment->setLabel('* Comment:')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('rows',5)
		->setAttrib('cols',50);

		
//		$capcha = $this->createElement('Captcha','captcha',array(
//			'label' => 'Please enter the 5 letters displayed below:',
//			'name' => 'captcha',
//			'required' => true,
//			'captcha' => array(
//			'captcha' => 'dumb',
//			'wordLen' => 4,
//			'timeOut' => 5
//		)
//		));

		$register = $this->createElement('submit','comment');
		$register->setLabel('Save')
		->setIgnore(true);
		
	
		$this->addElements(array(
		$id,
		//$ipaddr,		
		$firstname,
		$lastname,
		$email,
		$subject,
		$comment,
		$register        ));

		
		
		
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

	}

}