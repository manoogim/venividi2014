<?php
class Form_Profile extends Zend_Form{
	public function __construct($options = null)
	{
		parent::__construct($options);
		
		$this->setName('signup');
		$id = new Zend_Form_Element_Hidden('USER_ID');
		
		//		$email = new Zend_Form_Element_Hidden('EMAIL');
		$email = $this->createElement('text','EMAIL');		
		$email->setLabel('* Email:')
		->setAttrib('readonly', 'true')
		->setAttrib("size","35")
		->setAttrib("maxlength","35")
		->setRequired(true);

		$firstname = $this->createElement('text','FIRST_NAME');
		$firstname->setLabel('* First Name:')
		->setAttrib("maxlength","15")
		->setAttrib("size","35")
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);

		$lastname = $this->createElement('text','LAST_NAME');
		$lastname->setLabel('* Last Name:')
				->setAttrib("maxlength","15")
		->setAttrib("size","35")
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);

		$oldpassword = $this->createElement('password','OLD_PASSWORD');
		$oldpassword->setLabel('* Old Password:')
				->setAttrib("maxlength","15")
		->setAttrib("size","25")
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setRequired(true);

		$password = $this->createElement('password','password');
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
		
		$register = $this->createElement('submit','register');
		$register->setLabel('Update')
		->setIgnore(true);
		
	
		$this->addElements(array(
		$id,
		$email,
		
		$firstname,
		$lastname,
		$phone,
//		$username,
		$oldpassword,
		$password,
		$confirmPassword,
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
        $register->removeDecorator('Label');
	}

}