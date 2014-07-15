<?php
class Form_Team extends Zend_Form
{
	// within c-tor we create four form elements for the id, artist, title, and submit
	public function __construct($options = null)
	{
		parent::__construct($options);
		$ww = 'width: 300px';
		$this->setName('team');
		$id = new Zend_Form_Element_Hidden('TEAM_ID');

		$teamName = new Zend_Form_Element_Text('TEAM_NAME');

		$teamName->setLabel('Team Name: ')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty')
			->setAttrib('maxlength','30')
			->setAttrib('style',$ww)
			;
		
			$eventtype = new Zend_Form_Element_Select('EVENT_TYPE_ID');
		$eventtype->addMultiOption('','--Select One--');
		$model = new Model_DbTable_EventTypes();
		foreach ($model->fetchEventTypes() as $row) {
			$eventtype->addMultiOption($row['EVENT_TYPE_ID'], $row['EVENT_TYPE_NAME']);
		}		
		$eventtype->setLabel('Competition Type: ')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty')
			->setAttrib('style',$ww)
			;		

		$zones = $this->createElement('select', 'ZONE_ID');
		$zones->setLabel('Zone:')
		->setRequired(true)
		->addMultiOption('','--Select One--')
		->addMultiOptions(Model_DbTable_Zones::getInstance()->fetchZones())
		->setAttrib('style',$ww)
		;

		
		$rgns = $this->createElement('select', 'RGN_ID');
		$rgns->setLabel('Region:')
		->setRequired(true)
		->addMultiOption('','--Select One--')
		->addMultiOptions(Model_DbTable_Regions::getInstance()->fetchRegions())
		->setAttrib('style',$ww)
		;
				
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');


		$this->addElements(array($id, $teamName, $eventtype, $zones, $rgns, $submit));
		
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
        $submit->removeDecorator('Label');
	}
	
}