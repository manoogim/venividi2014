<?php
class Form_Horse extends Zend_Form
{
	// within c-tor we create four form elements for the id, artist, title, and submit
	public function __construct($options = null)
	{
		parent::__construct($options);
		
		$this->setName('team');
		$id = new Zend_Form_Element_Hidden('HORSE_ID');

		$horsename = new Zend_Form_Element_Text('HORSE_NAME');
		$horsename->setLabel('Horse Name')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty')
			->setAttrib('maxlength','15')
			->setAttrib('size','20')
			->setAttrib('TABINDEX',1)
			;
		$barn = new Zend_Form_Element_Text('BARN');
		$barn->setLabel('Home Barn')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty')
			->setAttrib('maxlength','50')
			->setAttrib('size','20')
			->setAttrib('TABINDEX',1)
			;
		$breed = new Zend_Form_Element_Text('BREED');
		$breed->setLabel('Breed')
			->setRequired(false)
			->addFilter('StripTags')
			->addFilter('StringTrim')			
			->setAttrib('maxlength','20')
			->setAttrib('size','20')
			->setAttrib('TABINDEX',1)
			;
			
		$ww = 'width: 175px';
		$gender = new Zend_Form_Element_Select('GENDER');
		$gender->addMultiOption('','--Select One--');
		
		$gender->addMultiOptions(Model_DbTable_Lookup::getInstance()->fetchLookups('GENDER'));
		$gender->setLabel('Gender')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setAttrib('style',$ww)
		;
		$color = new Zend_Form_Element_Text('COLOR');
		$color->setLabel('Color')
			->setRequired(false)
			->addFilter('StripTags')
			->addFilter('StringTrim')			
			->setAttrib('maxlength','20')
			->setAttrib('size','20')
			->setAttrib('TABINDEX',1)
			;	

		$visina = new Zend_Form_Element_Text('HEIGHT');
		$visina->setLabel('Horse Height')
		->setRequired(false)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setAttrib('style',$ww)
		;
					
			$wlimit = new Zend_Form_Element_Text('WEIGHT_LIMIT');
			$wlimit->setLabel('Weight Limit')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setAttrib('maxlength','15')
			->setAttrib('size','20')
			->setDescription('Ex: 110')
			;

			$hlimit = new Zend_Form_Element_Text('HEIGHT_LIMIT');
			$hlimit->setLabel('Height Limit')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setAttrib('maxlength','15')
			->setAttrib('size','20')
			->setDescription("Ex: 4'5''")
			;

		$crop = new Zend_Form_Element_Select('CROP');
		$crop->addMultiOption('','--Select One--');
		$crop->addMultiOptions(Model_DbTable_Lookup::getInstance()->fetchLookups('CROP'));
		$crop->setLabel('Crop Usage')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setAttrib('style',$ww)
		;

		$spurs = new Zend_Form_Element_Select('SPURS');
		$spurs->addMultiOption('','--Select One--');
		$spurs->addMultiOptions(Model_DbTable_Lookup::getInstance()->fetchLookups('SPURS'));
		$spurs->setLabel('Spurs Usage')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setAttrib('style',$ww)
		;
			$desc = $this->createElement('textarea', 'HORSE_DESC');
			$desc->setLabel('Comments:')
			->addFilter('StripTags')
			//->addFilter('StringTrim')
			->setAttrib('rows',2)
			->setAttrib('cols',50);
				
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');


		$this->addElements(array($id, $horsename, $barn, $breed, $gender, $color, 
		$visina, $wlimit, $hlimit, $crop, $spurs, $desc, $submit));
		
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
        $submit->removeDecorator('Label');
	}
	
}