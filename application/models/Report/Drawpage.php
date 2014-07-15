<?php

require_once 'Zend/Date.php';

class Model_Report_Drawpage
{
	public $_pagenum;
	protected $_page;
	protected $_pageWidth;
	protected $_pageHeight;
	protected $_yPosition;
	protected $_leftMargin;
	protected $_normalFont;
	protected $_boldFont;

	// show name, and date
	public $_showinfo;
	public $_classname;
	public $_sectname;
	public $_horsenames;
	public $_alternates;
	public $_startnum;

	public function __construct()
	{
		$this->_page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
		$this->_pageWidth = $this->_page->getWidth(); // 595
		$this->_pageHeight = $this->_page->getHeight(); // 842
		$this->_normalFont = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
		$this->_boldFont = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
		$this->_yPosition = 60;
		$this->_leftMargin = 50;
	}

	private function setStyle()
	{
		$style = new Zend_Pdf_Style();
		$style->setFillColor(new Zend_Pdf_Color_Html('#333333'));
		$style->setLineColor(new Zend_Pdf_Color_Html('#990033'));
		$style->setLineWidth(1);
		$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
		$this->_page->setStyle($style);
	}


	function logit($xtra) {
		// YYYYmmdd-hhii
		$tstamp = date('Ymd-hi' , time());
		$fname = "c:/aaa/mylog.log" ;
		//mkdir($fname);
		$logme = 'xtra=' . $xtra . ' ,nnnnn ' . ";\n";
		$fh = fopen($fname,'a');

		if (flock($fh, LOCK_EX))
		{
			fwrite($fh, $logme) ;
			flock($fh, LOCK_UN);
		}

		fclose($fh);

	}


	private function drawPageHeader() {
		$this->_page->saveGS();
		$this->_page->setFont($this->_boldFont, 12);
		$show = $this->_showinfo->name . '   ' . $this->_showinfo->date;
		$this->_page->drawText($show, $this->_leftMargin, $this->_pageHeight - 20 );
		$this->_page->restoreGS();
	}
	
	private function drawPageFooter()
	{
		$this->_page->saveGS();
		$this->_page->drawLine($this->_leftMargin, 60, $this->_pageWidth - $this->_leftMargin, 60);

		$pg = '        ' ;
		$txt =$pg  
		. '                                                                                                          '
		. '                           © ' 
		. APP_NAME;
		$this->_page->drawText($txt, $this->_leftMargin, 20);
		// $this->_page->drawText( APP_NAME, $this->_rightMargin, 40);
		$this->_page->restoreGS();
	}

	private function drawDrawsheet($sectname, $horsenames, $alternates, $classname) {
		//$this->logit('$sectname=' . $sectname . ' $horsenames= ' . count($horsenames));
		$this->_page->saveGS();
		$smallfont = 18;
		$bigfont = 32;
		
		// sect name 14-A across top
		$this->_page->setFont($this->_boldFont, $smallfont);
		$title = '';
		for ($jj=0; $jj<8; $jj++) {
			$title = $title  . $sectname . '   ' ;;
		}		
		$this->_page->drawText( $title, $this->_leftMargin, $this->_pageHeight - 60 );
		$this->_page->drawLine($this->_leftMargin, $this->_pageHeight - 65, $this->_pageWidth -$this->_leftMargin, $this->_pageHeight - 65 );
		
		// Horse Rider # title
		$this->_page->setFont($this->_boldFont, $bigfont);
		$this->_page->drawText( 'Horse', $this->_leftMargin, $this->_pageHeight - 100 );
		$this->_page->drawText( 'Rider #', $this->_leftMargin+250, $this->_pageHeight - 100 );
		$this->_page->drawLine($this->_leftMargin, $this->_pageHeight - 105, $this->_pageWidth -$this->_leftMargin, $this->_pageHeight - 105 );
		
		// list of horse namesand line for rider #
		$offsetinc = 50;
		$offset = 0;
		$cnt = $this->_startnum;
		$this->_page->setFont($this->_boldFont, $bigfont);
		foreach ($horsenames as $horse) {
			$cnt++;
			$txt = $cnt . '. ' . $horse;
			$this->_page->drawText($txt, $this->_leftMargin, $this->_pageHeight - 140 - $offset);
			$txt = '_______';
			$this->_page->drawText($txt, $this->_leftMargin+250, $this->_pageHeight - 140 - $offset);
			$offset += $offsetinc;
		}

		// alternates subtitle
		if (count ($alternates) >0) {
			$this->_page->setFont($this->_boldFont, $smallfont);
			$this->_page->drawText('Alternates:', $this->_leftMargin, $this->_pageHeight - 140 - $offset);
			$this->_page->setFont($this->_boldFont, $bigfont);
			foreach ($alternates as $alt) {
				$offset += $offsetinc;
				$this->_page->drawText($alt, $this->_leftMargin, $this->_pageHeight - 140 - $offset);
					
			}
		}
		
		// write class name at bottom
		$this->_page->setFont($this->_boldFont, $smallfont);
		$txt = $sectname . ' ' . $classname;
		$this->_page->drawText( $txt, $this->_leftMargin,  40 );
		//$this->logit('$offset at end=' . $offset);
		$this->_page->restoreGS();
	}
	
	
	public function render()
	{
		$this->setStyle();
		$this->drawPageHeader();
		$this->drawDrawsheet($this->_sectname, $this->_horsenames, $this->_alternates, $this->_classname);
		$this->drawPageFooter();
		//$this->drawReportFooter($offset+40);
		return $this->_page;
	}


}