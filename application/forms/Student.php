<?php
class Form_Student extends Zend_Form
{
	// within c-tor we create four form elements for the id, artist, title, and submit
	public function __construct($options = null)
	{
		parent::__construct($options);
		$ww = 'width: 300px';
		$this->setName('student');



		$id = new Zend_Form_Element_Hidden('STUDENT_ID');
		$teamId = new Zend_Form_Element_Hidden('TEAM_ID');
		$ownerId = new Zend_Form_Element_Hidden('owner');
//		$eventtypeid = new Zend_Form_Element_Hidden('typeid');

		$firstName = new Zend_Form_Element_Text('$FIRST_NAME');
		$tag = $firstName->getDecorator('HtmlTag');
		$tag->setOption('tag','td');
		$lbl = $firstName->getDecorator('Label');
		$lbl->setOption('tag','th');

		$decoArr = array(
				    'ViewHelper',
				    'Description',
				    'Errors',
		$tag,
		$lbl,
		);
		$firstName->setDecorators($decoArr);
		$firstName->setLabel('First Name')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setAttrib('maxlength','15')
		->setAttrib('style',$ww);

		$lastName = new Zend_Form_Element_Text('LAST_NAME');
		$lastName->setDecorators($decoArr);
		$lastName->setLabel('Last Name')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setAttrib('maxlength','15')
		->setAttrib('style',$ww);

		$backNumber = new Zend_Form_Element_Text('BACK_NUMBER');
		$backNumber->setDecorators($decoArr);
		$backNumber->setLabel('Back Number')
		->setRequired(false)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('Int')
		->setAttrib('maxlength','15')
		->setAttrib('style',$ww);

		$class1 = new Zend_Form_Element_Select('CLASS1_ID');
		$class1->addMultiOption('','--Select One--');
		$model = new Model_DbTable_Classes();
		$typeid = $options['typeid'];
		
		$e = new Model_DbTable_EventTypes();
		$ee = $e->fetchEventType($typeid);
		$tarr = explode(',',$ee['CLASS_TYPES']);

		foreach ($model->fetchAll('event_type_id=' . $typeid . ' AND class_type_id='. $tarr[0],'LEVEL') as $row) {
//		foreach ($model->fetchClasses($typeid) as $row) {
			$class1->addMultiOption($row['CLASS_ID'], $row['LEVEL'] . ' - ' . $row['CLASS_NAME']);
		}
		$class1->setLabel('Class 1')
		->setRequired(false)
		->setAttrib('style',$ww)
		;

		$class2 = new Zend_Form_Element_Select('CLASS2_ID');
		$class2->addMultiOption('','--Select One--');
		$model = new Model_DbTable_Classes();
		foreach ($model->fetchAll('event_type_id=' . $typeid . ' AND class_type_id=' . $tarr[1],'LEVEL') as $row) {
			$class2->addMultiOption($row['CLASS_ID'], $row['LEVEL'] . ' - ' . $row['CLASS_NAME']);
		}
		$class2->setLabel('Class 2')
		->setRequired(false)
		->setAttrib('style',$ww)
		;

		$btnDecoArr = array(
				    'ViewHelper',
				    'Description',
				    'Errors',
		$tag,
			
		);
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setDecorators($btnDecoArr);
		$submit->setAttrib('id', 'submitbutton');

		$this->addElements(array($id, $teamId, $ownerId, $firstName, $lastName, $backNumber, $class1, $class2, $submit));

		// render the form into a table:
		$formDeco = array('FormElements', array('HtmlTag', array('tag' => 'table')),'Form' );
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