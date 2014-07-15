<?php

class Model_Report_Programdoc
{
    protected $_pdf;

    public function __construct()
    {
        $this->_pdf = new Zend_Pdf();
        $this->_pdf->properties['Title'] = 'IHSA Show Report';
        $this->_pdf->properties['Author'] = 'Veni, Vidi, Vici';
    }

    public function addPage(Model_Report_Programpage  $page)
    {        
        $this->_pdf->pages[] = $page->render();
    }

    public function getDocument()
    {
    	
        return $this->_pdf;
    }
}