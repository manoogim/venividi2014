<?php

class Form_Upload extends Zend_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setName('upload');
        $this->setAttrib('enctype', 'multipart/form-data');


        $file = new Zend_Form_Element_File('customlogo');
        $file
            ->setDestination('data')
            ->setRequired(true)
            ->addValidator('Count', false, 1)
            ->addValidator('Size', false, 200000) // bytes
            ->addValidator('Extension', false, 'jpg,JPG,png,PNG')
            ->setAttrib('style','width: 600px')
            ->setAttrib('size',100)
            ;

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Use Custom Logo');

        $this->addElements(array( $file, $submit));

    }
}
