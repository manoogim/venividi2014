<?php

require_once 'Zend/Date.php';

class Model_Report_Imgpage extends Model_Report_Prizepage
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
	public $_image;

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

	
	private function drawPageFooter()
	{
		$this->_page->saveGS();
//		$this->_page->drawLine($this->_leftMargin, 60, $this->_pageWidth - $this->_leftMargin, 60);

		
		$txt = '© '. APP_NAME;
		$this->_page->drawText($txt, $this->_leftMargin+200, 20);
		// $this->_page->drawText( APP_NAME, $this->_rightMargin, 40);
		$this->_page->restoreGS();
	}


	private function _drawImage() {
		$this->_page->saveGS();
		$this->_page->drawImage($this->_image, 20, 20, 590, 840);
		$this->_page->restoreGS();
	}
	
	public function render()
	{
		$this->setStyle();
		$this->_drawImage();
		//$this->drawPageFooter();

		return $this->_page;
	}


//	function calcLeftMargin ($text) {
//		$font = $this->_page->getFont();
//		$fontsize = $this->_page->getFontSize();
//		$w  = $this->_widthForStringUsingFontSize($text, $font, $fontsize);
//		$ans = ($this->_pageWidth - $w) / 2;
//		return $ans;
//	}
//	
//	function _widthForStringUsingFontSize($string, $font, $fontSize){     
//		$drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);     
//		$characters = array();     
//		for ($i = 0; $i < strlen($drawingString); $i++) {         
//			$characters[] = (ord($drawingString[$i++]) << 8 ) | ord($drawingString[$i]);     }     
//			$glyphs = $font->glyphNumbersForCharacters($characters);     
//			$widths = $font->widthsForGlyphs($glyphs);     
//			$stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;     
//			return $stringWidth; 
//	}
}