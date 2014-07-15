<?php
class Form_Hatetyping extends Zend_Form{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$example='2,700014,100012,"Halle","Andrews",,12,,,
3,700015,100012,"Leah","Davis",,12,9,3,8/19/2008 0:00:00
4,700016,200009,"Emily","Jadwin",,119,12,5,8/4/2008 0:00:00
5,700017,200009,"Rachel","McConnell",,119,12,5,8/4/2008 0:00:00
6,700028,100016,"Rachel","Fox",,16,9,3,9/8/2008 0:00:00
7,700029,100016,"Madeline","Motta",,16,10,4,8/20/2008 0:00:00
8,700030,100016,"Marissa","Murray",,16,9,3,8/20/2008 0:00:00
9,700031,100016,"Chanelle","Osko",,16,10,4,8/20/2008 0:00:00
10,700032,200011,"Bellamie","Russo",,121,13,6,8/20/2008 0:00:00
11,700042,200007,"Ian","Gilchrist",,117,13,6,8/11/2008 0:00:00
12,700055,100019,"Ansley","Carl",,19,8,2,8/28/2008 0:00:00
13,700056,200090,"Ava","Duncan",,123,12,5,8/28/2008 0:00:00
14,700057,100019,"Alexandra","Spafford",,19,11,,9/9/2008 0:00:00
15,700058,100019,"Samantha","Zabroske",,19,,,
16,700059,200023,"Carly","Tovell",,133,12,5,8/19/2008 0:00:00
17,700069,100021,"Taylor","Barnett",,21,11,,8/6/2008 0:00:00
18,700070,100021,"Jordan","Bressler",,21,11,,8/6/2008 0:00:00
19,700071,200081,"Katelyn","Smith",,193,12,5,8/11/2008 0:00:00
20,700167,100030,"Alex","Becker",,30,10,4,8/28/2008 0:00:00
21,700168,200020,"Emily","Gill",,130,12,5,8/28/2008 0:00:00';
		$this->setName('hatetyping');
		$id = new Zend_Form_Element_Hidden('TEAM_ID');		
		$ownerId = new Zend_Form_Element_Hidden('owner');
		
		$comment = $this->createElement('textarea', 'HUGETEXT');
		
		$comment
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->setAttrib('rows',25)
		->setAttrib('cols',40)
		
;
		$btn = $this->createElement('submit','submit');
		$btn->setLabel('Save Rider Names')
		->setIgnore(true);
		
	
		$this->addElements(array(
		$comment,
		$id,
		$ownerId,		
		$btn        ));

		
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
//	        array('Label', array('tag' => 'td')),
	        array('decorator' => array('tr' => 'HtmlTag'), 'options' => array('tag' => 'tr')),
	        );
        $this->setElementDecorators($elemDeco);
        $btn->removeDecorator('Label');

	}

}