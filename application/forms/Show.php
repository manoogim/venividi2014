<?php
class Form_Show extends Zend_Form
{
	// within c-tor we create form elements  and submit
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setName('show');
		$ww = 'width: 455px';
		$showid = new Zend_Form_Element_Hidden('SHOW_ID');
		$userid = new Zend_Form_Element_Hidden('OWNER_ID');

		$showname = new Zend_Form_Element_Text('SHOW_NAME');
		$showname->setLabel('Show Name')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setAttrib('maxlength','255')
		->setAttrib('style',$ww)
//		->setAttrib('size','70')
		;
			
		$eventtype = new Zend_Form_Element_Select('EVENT_TYPE_ID');
		$eventtype->addMultiOption('','--Select One--');
		$model = new Model_DbTable_EventTypes();
		foreach ($model->fetchEventTypes() as $row) {
			$eventtype->addMultiOption($row['EVENT_TYPE_ID'], $row['EVENT_TYPE_NAME']);
		}
		$eventtype->setLabel('Show Type')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->setAttrib('style',$ww)
		;
			
		$btnhtml = '<button name="triggerButton" id="triggerButton" type="button">...</button>';
		// You can skip the reformatting if you use the according sql functions
		// like DATE_FORMAT() and STR_TO_DATE() in MySql.
			
		$showdate = new Zend_Form_Element_Text('SHOW_DATE');
		$showdate->setLabel('Show Date')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->addValidator('Date')
		->setAttrib('readonly','true')
		->setAttrib('style',$ww)
		->setDescription($btnhtml);

		$btnhtml2 = '<button name="triggerButton2" id="triggerButton2" type="button">...</button>';
		// You can skip the reformatting if you use the according sql functions
		// like DATE_FORMAT() and STR_TO_DATE() in MySql.
			
		$closedate = new Zend_Form_Element_Text('CLOSE_DATE');
		$closedate->setLabel('Close Date')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->addValidator('Date')
		->setAttrib('readonly','true')
		->setAttrib('style',$ww)
		->setDescription($btnhtml2);
		
		$showtime = new Zend_Form_Element_Text('SHOW_TIME');
		$showtime->setLabel('Start Time')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('style',$ww)
		
;
		$regtime = new Zend_Form_Element_Text('REGISTRATION_TIME');
		$regtime->setLabel('Registration Time')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('style',$ww)
		
;
		$entryfee = new Zend_Form_Element_Text('ENTRY_FEE');
		$entryfee->setLabel('Entry Fee ($)')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('Float')
		->setAttrib('style',$ww)		
;
		$checkpayable = new Zend_Form_Element_Text('CHECK_PAYABLE');
		$checkpayable->setLabel('Check Payable')
		->setRequired(false)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('style',$ww)
		;
		
		$zones = $this->createElement('select', 'ZONE_ID');
		$zones->setLabel('Invite Zone')
		->setRequired(true)
		->addMultiOption('','-- Select One --')
//		->addMultiOption('-1','All Zones')
		->addMultiOptions(Model_DbTable_Zones::getInstance()->fetchZones())
		->setAttrib('style',$ww)
		;

		$rgns = $this->createElement('select', 'RGN_ID');
		$rgns->setLabel('Invite Region')
		->setRequired(true)
		->addMultiOption('','--Select One--')
		->addMultiOption('-1','All Regions')
		->addMultiOptions(Model_DbTable_Regions::getInstance()->fetchRegions())
		->setAttrib('style',$ww)
		;

				
		$address = new Zend_Form_Element_Text('ADDRESS');
		$address->setLabel('Show Address')
		->setRequired(false)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('maxlength','255')
		->setAttrib('style',$ww)
		;
		$mc = new Model_DbTable_Classes();
		$availclasses = $mc->fetchClasses(1);
		$desc = 'Ex: ';
		foreach ($availclasses as $c) {
			$desc = $desc . $c->level . ',';			
		}
		$classorder = new Zend_Form_Element_Text('CLASS_ORDER');
		$classorder->setLabel('Run Class Order')
		->setRequired(false)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('maxlength','255')
		->setAttrib('style',$ww)
		->setDescription($desc)
		;
		
		$judge = new Zend_Form_Element_Text('JUDGE');
		$judge->setLabel('Judge')
		->setRequired(false)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('maxlength','255')
		->setAttrib('style',$ww)
		;

		$steward = new Zend_Form_Element_Text('STEWARD');
		$steward->setLabel('Steward(s)')
		->setRequired(false)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('maxlength','255')
		->setAttrib('style',$ww)
		->setDescription('Mult entries separate by ,')
		;
		$hh = new Zend_Form_Element_Text('HORSEHOLDER');
		$hh->setLabel('Horse Holder(s)')
		->setRequired(false)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('maxlength','255')
		->setAttrib('style',$ww)
		->setDescription('Mult entries separate by ,')
		;
		$anno = new Zend_Form_Element_Text('ANNOUNCER');
		$anno->setLabel('Announcer')
		->setRequired(false)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('maxlength','255')
		->setAttrib('style',$ww)
		;
		$padock = new Zend_Form_Element_Text('PADOCK_MASTER');
		$padock->setLabel('Paddock Master')
		->setRequired(false)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('maxlength','255')
		->setAttrib('style',$ww)
		;
		$jumping = new Zend_Form_Element_Text('JUMPING_MASTER');
		$jumping->setLabel('Jumping Master')
		->setRequired(false)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('maxlength','255')
		->setAttrib('style',$ww)
		;
		$medical = new Zend_Form_Element_Text('MEDICAL');
		$medical->setLabel('Medical')
		->setRequired(false)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('maxlength','255')
		->setAttrib('style',$ww)
		;
		$misc = new Zend_Form_Element_Text('MISC_OTHER');
		$misc->setLabel('Misc Notes')
		->setRequired(false)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('maxlength','255')
		->setAttrib('style',$ww)
		->setDescription('Ex: No dogs allowed,')
		;
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton')
	//	->setAttrib('style',$ww)
		;


		$this->addElements(array( $showname, $eventtype, $showdate, $closedate, 
			$showtime, $regtime, $entryfee, $checkpayable, $zones, $rgns,$classorder,
			$address, $judge, $steward, $hh,  $anno, $padock, $jumping, $medical, $misc, $showid, $userid, $submit));

		// render the form into a table:
		$formDeco = array('FormElements', array('HtmlTag', array('tag' => 'table')),'Form' );
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

		//  modify the decorators so the submit spans both columns:
		//        $this->submit->setDecorators(array(
		//        array(
		//            'decorator' => 'ViewHelper',
		//            'options' => array('helper' => 'formSubmit')),
		//        array(
		//            'decorator' => array('td' => 'HtmlTag'),
		//            'options' => array('tag' => 'td', 'colspan' => 2)),
		//        array(
		//            'decorator' => array('tr' => 'HtmlTag'),
		//            'options' => array('tag' => 'tr')),
		//        ));

		// from bob alans tutorial
		//                $this->clearDecorators();
		//        $this->addDecorator('FormElements')
		//         ->addDecorator('HtmlTag', array('tag' => '<ul>'))
		//         ->addDecorator('Form');
		//
		//        $this->setElementDecorators(array(
		//            array('ViewHelper'),
		//            array('Errors'),
		//            array('Description'),
		//            array('Label', array('separator'=>' ')),
		//            array('HtmlTag', array('tag' => 'li', 'class'=>'element-group')),
		//        ));
		//
		//        // buttons do not need labels
		//        $submit->setDecorators(array(
		//            array('ViewHelper'),
		//            array('Description'),
		//            array('HtmlTag', array('tag' => 'li', 'class'=>'submit-group')),
		//        ));

	}
}