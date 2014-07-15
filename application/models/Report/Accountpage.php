<?php

require_once 'Zend/Date.php';

class Model_Report_Accountpage
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
	public $_teaminfo;
	public $_coachinfo;
	public $_cnts;
	public $_amts;
	public $_clazzes;
	public $_riders;
	public $_totalcnt;
	public $_totalamt;

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
//		$style->setLineColor(new Zend_Pdf_Color_Html('#990033'));
		$style->setLineWidth(1);
		$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
		$this->_page->setStyle($style);
	}


	private function _drawheader() {
		$this->_page->saveGS();
		$this->_page->setFont($this->_boldFont, 16);
		// type of show
		$yoffset = 60;
		$txt = 'Team Accounting Report';
		$this->_page->drawText($txt, $this->_leftMargin, $this->_pageHeight - $yoffset );

		$this->_page->setFont($this->_normalFont, 12);

		// show name
		$yoffset += 30;
		$show = $this->_showinfo->name ;
		$this->_page->drawText("SHOW:", $this->_leftMargin, $this->_pageHeight - $yoffset );
		$this->_page->drawText($show, $this->_leftMargin+120, $this->_pageHeight - $yoffset );

		// team name
		$show = $this->_showinfo->name ;
		$yoffset += 20;
		$showdate = $this->_showinfo->date;

		$this->_page->drawText("SHOW DATE:", $this->_leftMargin, $this->_pageHeight - $yoffset );
		$this->_page->drawText($showdate, $this->_leftMargin+120, $this->_pageHeight - $yoffset );
		// show date
		$yoffset += 20;

		$this->_page->drawText("SCHOOL:", $this->_leftMargin, $this->_pageHeight - $yoffset );
		$this->_page->drawText($this->_teaminfo, $this->_leftMargin+120, $this->_pageHeight - $yoffset );

		// coach name
		$yoffset +=20;
		$this->_page->drawText("COACH/CAPTAIN:  ", $this->_leftMargin, $this->_pageHeight - $yoffset );
		$this->_page->drawText($this->_coachinfo, $this->_leftMargin+120, $this->_pageHeight - $yoffset );

		$yoffset += 20;
		$this->_page->restoreGS();

		return $yoffset;
	}

	private function _drawbody ($yoffset, $riders, $classes, $cnts, $amts) {
		$this->_page->saveGS();
		$this->_page->setFont($this->_boldFont, 12);
		
		$xoffsets = array(0,20,200,440,470);
		$this->_drawoneline(array('','Rider Name','Classes','Cnt','Amt'), $xoffsets, $yoffset);
		$cnt = 0;
		$yoffset +=20;
		$this->_page->setFont($this->_normalFont, 12);
		foreach ($riders as $rider) {
			$cnt++;
			$line = array($cnt . '. ', $rider, trim(current($classes),', '), '  ' . current($cnts), ' $' . current($amts));
			$this->_drawoneline($line, $xoffsets, $yoffset);
			next($classes);
			next($cnts);
			next($amts);
			$yoffset +=15;
		}
//		$yoffset +=5;
		$this->_page->drawLine($this->_leftMargin, $this->_pageHeight - $yoffset, $this->_pageWidth - $this->_leftMargin, $this->_pageHeight - $yoffset);
		$yoffset +=12;
		$this->_page->setFont($this->_boldFont, 12);
		$line = array('Total:',' ' . $this->_totalcnt,'$' . $this->_totalamt);
		$xoffsets = array(400,440,470);
		$this->_drawoneline($line, $xoffsets, $yoffset);
		$this->_page->restoreGS();
	}
	private function _drawoneline($texts, $xoffsets, $yoffset) {
		$nn = count($texts);
		for ($jj=0; $jj<$nn; $jj++) {
			$this->_page->drawText($texts[$jj], $this->_leftMargin+$xoffsets[$jj], $this->_pageHeight - $yoffset );
		}
		
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
		$this->_page->drawLine($this->_leftMargin, 60, $this->_pageWidth - $this->_leftMargin, 60);

		$pg = 'Page -' . $this->_pagenum . '-';
		$txt =$pg
		. '                                                                                                          '
		. '                           © '
		. APP_NAME;
		$this->_page->drawText($txt, $this->_leftMargin, 20);
		$showdate = $this->_showinfo->date;
		$ww = Model_Report_Coverpage::_widthForStringUsingFontSize($showdate,$this->_normalFont, 12);
		$lm = 0.5 * ($this->_pageWidth - $ww);
		$this->_page->drawText( $showdate, $lm, 40);

		$this->_page->restoreGS();
	}


	public function render()
	{
		$this->setStyle();

		$yoffset = $this->_drawheader();
		$this->_drawbody($yoffset,$this->_riders, $this->_clazzes, $this->_cnts, $this->_amts);

		$this->drawPageFooter();

		return $this->_page;
	}


	function calcLeftMargin ($text) {
		$font = $this->_page->getFont();
		$fontsize = $this->_page->getFontSize();
		$w  = Model_Report_Coverpage::_widthForStringUsingFontSize($text, $font, $fontsize);
		$ans = ($this->_pageWidth - $w) / 2;
		return $ans;
	}


}