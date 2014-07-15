<?php

require_once 'Zend/Date.php';

class Model_Report_Judgescardpage
{
	protected $_page;
	protected $_pageWidth;
	protected $_pageHeight;
	protected $_yPosition;
	protected $_leftMargin;
	protected $_normalFont;
	protected $_boldFont;


	// show name, and date
	public $_showinfo;
	public $_section;
	public $_numbersrow;

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
		$style->setLineWidth(1);
		$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
		$this->_page->setStyle($style);
	}


	private function _drawoneline($nw, $yoffset ) {
		$arr = array_fill(0, $nw, '_');
		$txt = implode('',$arr);
		$this->_page->drawText($txt, $this->_leftMargin, $this->_pageHeight - $yoffset );
	}

	private function _drawheader() {
		$this->_page->saveGS();
		$this->_page->setFont($this->_boldFont, 14);
		// type of classes (fences/flat), centered
		$yoffset = 60;
		$txt = strtoupper( $this->_section->classtype );
		$ww = Model_Report_Coverpage::_widthForStringUsingFontSize($txt,$this->_page->getFont(), $this->_page->getFontSize());
		$lm = 0.5 * ($this->_pageWidth - $ww);
		$this->_page->drawText($txt, $lm, $this->_pageHeight - $yoffset );


		// section name and class name
		$yoffset += 50;
		$txt = 'CLASS NO.   ' . $this->_section->sectname . '   ' . $this->_section->classname;
		$this->_page->drawText($txt, $this->_leftMargin, $this->_pageHeight - $yoffset );

		// single table row with numbers 1 through 10 with 2 double lines as boundary
		$nw14 = 67; // how many underscores if font size=14 bold
		$nw22 = 43; // how many underscores if font size is 22 normal
		$nw12 = 80;
		$nw = $nw12-4;
		$this->_page->setFont($this->_normalFont, 12);
		$yoffset += 20;
		$this->_drawoneline($nw, $yoffset);
		$this->_drawoneline($nw, $yoffset+3);
		$yoffset += 25;
		$yh = $yoffset; // for verticals
		$this->_drawoneline($nw, $yoffset);
		$this->_drawoneline($nw, $yoffset+3);

		$this->_writesmallnumbers(11, $yoffset-12);

		$this->_page->setFont($this->_normalFont, 12);

		// first table row with 1 2 3 4 5 6 7 8 9 10 11 12 . . . TOTAL
		$yoffset += 50;
		$this->_drawoneline($nw12, $yoffset);
		$this->_drawoneline($nw12, $yoffset+3);

		$yoffset +=25;
		$y0 = $yoffset;
		$nrows = 20;
		if ($this->_section->classtypeid % 2 ==0) {// even = fences
			$smallspacing = '13-25';
			$bigspacing = '3-40';
			
		} else {
			$smallspacing = '7-60';
			$bigspacing = '0-40';
			
		}
		$this->_writeNverticalLines($smallspacing,$bigspacing,$yoffset, $this->_numbersrow);
		for ($jj=0;$jj<$nrows; $jj++) {
			$this->_drawoneline($nw12, $yoffset);
			$yoffset +=23;
		}

		// top row vertical lines
		$this->_page->setFont($this->_normalFont, 22);
		$this->_writeNverticalLines('11-50','0-0', $yh-3, null);
		$this->_page->drawText('|', $this->_leftMargin , $this->_pageHeight - $yh+3 );

		// the rest of the table cells - vertical lines are too short with small font
		// so we draw all vertical lines in a separate loop with font size 22

		$yoffset = $y0;
		for ($jj=0;$jj<$nrows; $jj++) {
			$xoffset = $this->_writeNverticalLines($smallspacing,$bigspacing, $yoffset-3, null);
			$yoffset +=23;
		}

		// judges name
		$this->_page->setFont($this->_normalFont, 14);
		$yoffset +=20;
		$this->_page->drawText("Judge's Signature:  ", $this->_leftMargin, $this->_pageHeight - $yoffset );
		$this->_page->drawText("______________________________________",
		$this->_leftMargin+120, $this->_pageHeight - $yoffset );

		// judge's signature
		$yoffset +=25;

		$this->_page->drawText("Judge: ", $this->_leftMargin, $this->_pageHeight - $yoffset );
		$this->_page->drawText($this->_showinfo['JUDGE'], $this->_leftMargin+110, $this->_pageHeight - $yoffset );


		$this->_page->restoreGS();


	}



	private function _writesmallnumbers($nn, $yoffset) {
		$this->_page->setFont($this->_page->getFont(), 8);
		$xoffset = 10;
		for ($jj=1; $jj<$nn; $jj++) {
			$txt = '' . $jj;
			$this->_page->drawText($txt, $this->_leftMargin + $xoffset, $this->_pageHeight - $yoffset );
			$xoffset += 50;
		}

	}

	private function _writeNverticalLines ($smallspacing, $bigspacing, $yoffset, $row) {
		$tmp = explode('-', $smallspacing);
		$nsmall = $tmp[0];
		$smallxoffset = $tmp[1];

		$tmp = explode('-', $bigspacing);
		$nbig = $tmp[0];
		$bigxoffset = $tmp[1];

		$nn = $nsmall + $nbig;
		//$txt = '|';
		$xoffset = is_array($row) ? -20 : 0;
		for ($jj=0; $jj<$nn; $jj++) {
			$inc = $jj < $nsmall ? $smallxoffset : $bigxoffset;
			$txt = is_array($row) ? $row[$jj] : '|';
			$xoffset += $inc;
			$this->_page->drawText($txt, $this->_leftMargin + $xoffset, $this->_pageHeight - $yoffset );
		}
		if (is_array ($row)) {
			$this->_page->drawText('TOTAL', $this->_leftMargin + $xoffset+50, $this->_pageHeight - $yoffset );
		}
		return $xoffset;
	}

	//	private function _writevertical($nn,$initxoffset, $inc, $yoffset, $restorefont) {
	//		$this->_page->setFont($this->_page->getFont(), 24);
	//		$xoffset = $initxoffset;
	//		for ($jj=0; $jj<$nn+1; $jj++) {
	//			$txt = '|';
	//			$this->_page->drawText($txt, $this->_leftMargin + $xoffset, $this->_pageHeight - $yoffset );
	//			$xoffset += $inc;
	//		}
	//		$this->_page->setFont($this->_page->getFont(), $restorefont);
	//	}
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

		;
		$txt ='© '
		. APP_NAME;
		$ww = Model_Report_Coverpage::_widthForStringUsingFontSize($txt,$this->_normalFont, 12);
		$lm = 0.5 * ($this->_pageWidth - $ww);
		$this->_page->drawText($txt, $lm, 20);

		$showdate = new Zend_Date($this->_showinfo['SHOW_DATE']);
		$txt = $this->_showinfo['SHOW_NAME'] . ' - ' . $showdate->get(Zend_Date::DATE_FULL);
		$ww = Model_Report_Coverpage::_widthForStringUsingFontSize($txt,$this->_normalFont, 12);
		$lm = 0.5 * ($this->_pageWidth - $ww);
		$this->_page->drawText( $txt, $lm, 40);

		$this->_page->restoreGS();
	}


	public function render()
	{
		$this->setStyle();

		$yoffset = $this->_drawheader();

		$this->drawPageFooter();

		//$this->drawReportFooter($offset+40);
		return $this->_page;
	}




}