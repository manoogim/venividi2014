<?php

require_once 'Zend/Date.php';

class Model_Report_Pointcardpage
{
	public $_pagenum;
	protected $_page;
	protected $_pageWidth;
	protected $_pageHeight;
	protected $_yPosition;
	protected $_leftMargin;
	protected $_normalFont;
	protected $_boldFont;

	public $_7302D;
	public $_7302B;
	public $_7302F;
	// show name, and date
	public $_showinfo;
	public $_teaminfo;
	public $_coachinfo;
	public $_clazzes;

	public function __construct()
	{
		$this->_page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
		$this->_pageWidth = $this->_page->getWidth(); // 595
		$this->_pageHeight = $this->_page->getHeight(); // 842
		$this->_normalFont = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
		$this->_boldFont = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
		$this->_yPosition = 60;
		$this->_leftMargin = 50;
		$this->_7302D ='Point riders must be registered with the score keeper or secretary prior to the first draw. ' .
			'Teams who fail to register point riders prior to the first draw shall have ' .
			' the first 8 eligible riders for Hunter Seat and the first 6 eligible riders Western Horsemanship listed ' .
			' in the program in the class list designated by the show as point riders';
		$this->_7302B =  'Champion and Reserve Hunter Seat teams shall be determined by totaling the scores of the top 7 of the 8' .
		' designated point riders from each team.  All 6 scores shall count for Western scores';
		$this->_7302F ='If an ineligible rider is used as a team point rider, the team will lose that score ' .
			'and one more score will be dropped, regardless of the size of the team. ' .
			'The score to be dropped will be the lowest score greater than zero.';
	}

	private function setStyle()
	{
		$style = new Zend_Pdf_Style();
		$style->setFillColor(new Zend_Pdf_Color_Html('#333333'));
		//$style->setLineColor(new Zend_Pdf_Color_Html('#990033'));
		$style->setLineWidth(1);
		$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
		$this->_page->setStyle($style);
	}


	private function _drawheader() {
		$this->_page->saveGS();
		$this->_page->setFont($this->_boldFont, 20);
		// type of show
		$yoffset = 60;
		$txt = $this->_showinfo->type . ' Point Card';
		$this->_page->drawText($txt, $this->_leftMargin, $this->_pageHeight - $yoffset );
		
		// ihsa rule 7302.D
		$yoffset +=30;
		$yoffset = $this->_drawPara('IHSA Rule 7302.D', $this->_7302D, $yoffset);
		
		$this->_page->setFont($this->_normalFont, 12);
		
		// show name
		$yoffset += 30;
		$show = $this->_showinfo->name ;
		$this->_page->drawText("SHOW:", $this->_leftMargin, $this->_pageHeight - $yoffset );
		$this->_page->drawText($show, $this->_leftMargin+100, $this->_pageHeight - $yoffset );

		// team name
		$show = $this->_showinfo->name ;
		$yoffset += 20;
		$this->_page->drawText("SCHOOL:", $this->_leftMargin, $this->_pageHeight - $yoffset );
		$this->_page->drawText($this->_teaminfo, $this->_leftMargin+100, $this->_pageHeight - $yoffset );

		// show date
		$yoffset += 20;
		$showdate = new Zend_Date($this->_showinfo->date);
		$txt = $showdate->get(Zend_Date::DATE_FULL);
		$this->_page->drawText("DATE:", $this->_leftMargin, $this->_pageHeight - $yoffset );
		$this->_page->drawText($txt, $this->_leftMargin+100, $this->_pageHeight - $yoffset );

		// table of classes
		//Division	Number	Rider	Section	Place	Points
		$yoffset += 40;
		$textarr = array('Division','Number','Rider','Sect','Place','Points');
		$xoffsets = array(0,150,200,380,420,460);
		$this->_page->setFont($this->_boldFont, 12);
		$yline = $this->_pageHeight - $yoffset-10;

		$this->_page->drawLine($this->_leftMargin, $yline, $this->_pageWidth - $this->_leftMargin+5, $yline);
		$yoffset += 25;
		$this->_writeOneLine($textarr, $xoffsets, $yoffset);

		// individual table rows
		$this->_page->setFont($this->_normalFont, 12);
		$yoffset +=20;
		foreach($this->_clazzes as $c) {
			$textarr = array($c);
			$this->_writeOneLine($textarr, $xoffsets, $yoffset);
			$yoffset += 20;
		}

		// total points
		$yoffset +=20;
		$this->_page->drawText("Total Points:", $this->_leftMargin+300, $this->_pageHeight - $yoffset );
		$this->_page->drawText("_________________", $this->_leftMargin+380, $this->_pageHeight - $yoffset );


		// coach name
		$yoffset +=40;
//		$this->_page->drawText("Coach:  ", $this->_leftMargin, $this->_pageHeight - $yoffset );
//		$this->_page->drawText($this->_coachinfo, $this->_leftMargin+70, $this->_pageHeight - $yoffset );

		// coach's signature
		$yoffset +=25;
		$this->_page->drawText("Coach Signature:  ", $this->_leftMargin, $this->_pageHeight - $yoffset );
		$this->_page->drawText("_________________________________________________________",
		$this->_leftMargin+120, $this->_pageHeight - $yoffset );

		//					wordwrap()
		
		
		$yoffset +=40;	
		$this->_page->setFont($this->_boldFont, 10);
		$this->_page->drawText('Note to Point Calculator: ', $this->_leftMargin, $this->_pageHeight - $yoffset );
		$yoffset +=5;	
		$this->_page->drawText('_______________________', $this->_leftMargin, $this->_pageHeight - $yoffset );
		$yoffset +=15;	
		$yoffset = $this->_drawPara('IHSA Rule 7302.B', $this->_7302B, $yoffset);
		
		$yoffset +=10;	
		$yoffset = $this->_drawPara('IHSA Rule 7302.F', $this->_7302F, $yoffset);
		$this->_page->restoreGS();


	}

	private function _writeOneLine($textarr, $xoffsets, $yoffset) {
		foreach ($textarr as $txt) {
			$xoffset = current($xoffsets);
			$this->_page->drawText($txt, $this->_leftMargin + $xoffset, $this->_pageHeight - $yoffset );
			next($xoffsets);
		}

		$yline = $this->_pageHeight - $yoffset-5;
		$this->_page->drawLine($this->_leftMargin, $yline, $this->_pageWidth - $this->_leftMargin+5, $yline);

		$this->_page->setFont($this->_normalFont, 18);
		foreach($xoffsets as $x) {
			$this->_page->drawText('|', $this->_leftMargin + $x-5, $this->_pageHeight - $yoffset );
		}

		$this->_page->drawText('|', 550, $this->_pageHeight - $yoffset );


		$this->_page->setFont($this->_normalFont, 12);
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

		$showdate = new Zend_Date($this->_showinfo->date);
		//		$txt = $showdate->toString('F j, Y');
		$txt = $showdate->get(Zend_Date::DATE_FULL);
		$ww = Model_Report_Coverpage::_widthForStringUsingFontSize($txt,$this->_normalFont, 12);
		$lm = 0.5 * ($this->_pageWidth - $ww);
		$this->_page->drawText( $txt, $lm, 40);

		$this->_page->restoreGS();
	}


//	private function _drawrule7302($yoffset) {
//
//		$note = 'IHSA Rule 7302.B';
//
//
//		$text = $this->_7302B;
//		$this->_drawPara($note, $text, $yoffset);
//	}

	private function _drawPara($note, $text, $yoffset) {
		$this->_page->setFont($this->_boldFont, 10);
		$this->_page->drawText($note, $this->_leftMargin, $this->_pageHeight - $yoffset);
		$yoffset +=15;

		$this->_page->setFont($this->_normalFont, 10);
		$text = wordwrap($text, 100, "\n", false);
		$token = strtok($text, "\n");
		while ($token != false) {
			$this->_page->drawText($token, $this->_leftMargin, $this->_pageHeight - $yoffset);
			$yoffset+=15;			
			$token = strtok("\n");
		}
		
		return $yoffset;
	}
	
	public function render()
	{
		$this->setStyle();

		$yoffset = $this->_drawheader();

		$this->drawPageFooter();

		//$this->drawReportFooter($offset+40);
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