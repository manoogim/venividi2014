<?php
class Form_Registration extends Zend_Form{
	public function __construct($options = null)
	{
		parent::__construct($options);
		
		$this->setName('signup');

		$ipaddr = new Zend_Form_Element_Hidden('IP_ADDRESS');

		$firstname = $this->createElement('text','FIRST_NAME');
		$firstname->setLabel('* First Name:')
		->setAttrib("maxlength","15")
		->setAttrib("size","25")
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);

		$lastname = $this->createElement('text','LAST_NAME');
		$lastname->setLabel('* Last Name:')
		->setAttrib("maxlength","15")
		->setAttrib("size","25")
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);

		$email = $this->createElement('text','EMAIL');
		$email->setLabel('* Email:')
		->setRequired(true)
		->setAttrib("maxlength","35")
		->setAttrib("size","25")
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->addValidator('EmailAddress');

		$password = $this->createElement('password','PASSWORD');
		$password->setLabel('* Password:')
				->setAttrib("maxlength","15")
		->setAttrib("size","25")
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);

		$confirmPassword = $this->createElement('password','confirmPassword');
		$confirmPassword->setLabel('* Confirm Password:')
				->setAttrib("maxlength","15")
		->setAttrib("size","25")
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);

		$phone = $this->createElement('text','PHONE');
		$phone->setLabel('Phone:')
		->setRequired(true)
		->setAttrib("maxlength","35")
		->setAttrib("size","25")
		->addFilter('StripTags')
		->addFilter('StringTrim')
		;
		
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

		$terms = $this->createElement('checkbox', 'serviceterms');
		//$terms->setDescription('<a href="' . $options['termsurl']. '">I agree with the terms of service</a>')  ;
		$terms->setLabel("* I have read and I agree with the service terms:");
		$register = $this->createElement('submit','register');
		$register->setLabel('Register')
		->setIgnore(true);
		
	
		$this->addElements(array(
		$ipaddr,
		$firstname,
		$lastname,
		$email,
		$password,
		$confirmPassword,
		$phone,
		$terms,
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
            array('Description',array('escape'=>false,'tag'=>'span')), //escape false because I want html output 
	        array('decorator' => array('td' => 'HtmlTag'), 'options' => array('tag' => 'td')),
	        array('Label', array('tag' => 'td')),
	        array('decorator' => array('tr' => 'HtmlTag'), 'options' => array('tag' => 'tr')),
	        );
        $this->setElementDecorators($elemDeco);

        $register->removeDecorator('Label');
	}

}