<?php

require_once 'Zend/Date.php';

class Model_Report_Coverpage extends Model_Report_Prizepage
{
	public $_pagenum;
	protected $_page;
	protected $_pageWidth;
	protected $_pageHeight;
	protected $_yPosition;
	protected $_leftMargin;
	protected $_normalFont;
	protected $_boldFont;

	public $_image;
	public $_image2;
	// show name, and date
	public $_showinfo;
	// participating teams names
	public $_teams;
	
	public $_stewards;
	
	public $_horseholders;
	
	public $_announcer;
	
	public $_padockmaster;
	
	public $_jumpingmaster;
	
	public $_medical;

	public $_hostedby;
	
	private $_flag;
	
	public function __construct($flag)
	{
		$this->_flag = $flag;
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


	private function logit($xtra) {
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
	
	private function drawShowHeaderBig() {
		$this->_page->saveGS();
		if (isset ($this->_image)) {
			$ww = min ($this->_image->getPixelWidth(), $this->_pageWidth );
			$hh = min ($this->_image->getPixelHeight(), 200);

			$x1 = ($this->_pageWidth - $ww) / 2;
			$y1 = 700;
			$x2 = $x1 + $ww;
			$y2 = $y1 + $hh;
			$this->_page->drawImage($this->_image, $x1, $y1, $x2, $y2);
		}
		$this->_page->setFont($this->_boldFont, 20);
		
		$yoffset = 300;
		$txt = 'INTERCOLLEGIATE HORSE SHOW';
		$lm = $this->calcLeftMargin($txt);
		$this->_page->drawText($txt, $lm, $this->_pageHeight - $yoffset );
		
		$yoffset += 40;
		$txt = 'HOSTED BY';
		$lm = $this->calcLeftMargin($txt);
		$this->_page->drawText($txt, $lm, $this->_pageHeight - $yoffset );
		
		$show = $this->_showinfo ;
		$yoffset +=60;
		$txt = strtoupper( $this->_hostedby );
		$lm = $this->calcLeftMargin($txt);
		$this->_page->drawText($txt, $lm, $this->_pageHeight - $yoffset );
		
		$this->_page->setFont($this->_boldFont, 15);
		$yoffset += 40;
		$lm = $this->calcLeftMargin($show['SHOW_DATE']);
		$this->_page->drawText($show['SHOW_DATE'], $lm, $this->_pageHeight - $yoffset );
		
		$yoffset += 50;
		$lm = $this->calcLeftMargin($show['ADDRESS']);
		$this->_page->drawText($show['ADDRESS'], $lm, $this->_pageHeight - $yoffset );
		
		$this->_page->restoreGS();
	}
	

	private function drawShowHeader() {
		$this->_page->saveGS();
		$this->_page->setFont($this->_boldFont, 12);

		$show = $this->_showinfo ;
		
		$txt = strtoupper( $show['SHOW_NAME'] );
		$lm = $this->calcLeftMargin($txt);
		$this->_page->drawText($txt, $lm, $this->_pageHeight - 40 );
		
//		$zd = new Zend_Date($show['SHOW_DATE'],'yyyy-MM-dd');
//		$showdate = $zd->get(Zend_Date::DATE_FULL);
		$lm = $this->calcLeftMargin($show['SHOW_DATE']);
		$this->_page->drawText($show['SHOW_DATE'], $lm, $this->_pageHeight - 70 );
		
		$lm = $this->calcLeftMargin($show['ADDRESS']);
		$this->_page->drawText($show['ADDRESS'], $lm, $this->_pageHeight - 100 );
		
		$this->_page->restoreGS();
	}

	private function drawParticipatingTeams() {
		$this->_page->saveGS();
		$this->_page->setFont($this->_normalFont, 12);
		$txt = 'PARTICIPATING COLLEGES AND UNIVERSITIES';
		$lm = $this->calcLeftMargin($txt);
		$this->_page->drawText($txt, $lm, $this->_pageHeight - 140 );
		
		$yoffset = 160;
		//$this->logit('count($this->_teams)=' . count($this->_teams));
		foreach ($this->_teams as $t) {
			$lm = $this->calcLeftMargin($t);
			$this->_page->drawText($t, $lm, $this->_pageHeight - $yoffset );
			$yoffset += 20;
		}
		$this->_page->restoreGS();
	}
	
	private function drawOfficials() {
		$this->_page->saveGS();
		$this->_page->setFont($this->_boldFont, 14);
		
		$txt = 'OFFICIALS';
		$lm = $this->calcLeftMargin($txt);
		$yoffset = 180 + 20 * count($this->_teams);
		$this->_page->drawText($txt, $lm, $this->_pageHeight - $yoffset );
		
		$lm = $this->_leftMargin + 100;
		
		$txt = 'JUDGE';		
		$this->_page->setFont($this->_boldFont, 12);
		$this->_page->drawText($txt, $lm, $this->_pageHeight - $yoffset - 20 );
		//$this->_page->setFont($this->_normalFont, 12);
		$this->_page->drawText(strtoupper($this->_judge), $lm + 200, $this->_pageHeight - $yoffset - 20 );

		$yoffset = $this->_drawOfficialsGroup('STEWARDS', $this->_stewards, $lm, $yoffset + 40);
		
		$yoffset = $this->_drawOfficialsGroup('HORSE HOLDERS', $this->_horseholders, $lm, $yoffset +10);
		
		$yoffset = $this->_drawOfficialsGroup('ANNOUNCER', array($this->_announcer), $lm, $yoffset +10);
		$yoffset = $this->_drawOfficialsGroup('PADDOCK MASTER', array($this->_padockmaster), $lm, $yoffset +10);
		$yoffset = $this->_drawOfficialsGroup('JUMPING MASTER', array($this->_jumpingmaster), $lm, $yoffset +10);
		$yoffset = $this->_drawOfficialsGroup('MEDICAL', array($this->_medical), $lm, $yoffset +10);
		
		$this->_page->restoreGS();
	}
	
	private function _drawOfficialsGroup($title, $list, $lm, $yoffset) {
		//$this->_page->setFont($this->_boldFont, 12);
		$this->_page->drawText($title, $lm, $this->_pageHeight - $yoffset );
		foreach ($list as $h) {
			$this->_page->drawText($h, $lm + 200, $this->_pageHeight - $yoffset);
			$yoffset += 15;
		}
		return $yoffset;
	}
	
	private function drawPageFooter()
	{
		$this->_page->saveGS();
		$this->_page->drawLine($this->_leftMargin, 60, $this->_pageWidth - $this->_leftMargin, 60);

		$txt = '© '. APP_NAME;
		$lm = $this->calcLeftMargin($txt);
		$this->_page->drawText($txt, $lm, 20);
		
		$this->_page->restoreGS();
	}


	private function drawZoneRgnBig($yoffset) {
		$this->_page->saveGS();
		$this->_page->setFont($this->_normalFont, 20);
		
		$zone = $this->_showinfo['INVITED_ZONE']==-1 ? 'ALL' : $this->_showinfo['INVITED_ZONE'];
		$rgn = $this->_showinfo['INVITED_RGN'] ==-1 ? 'ALL' : $this->_showinfo['INVITED_RGN'];
		$txt = 'Zone ' . $zone . ', Region ' . $rgn;
		$lm = $this->calcLeftMargin($txt);
		$this->_page->drawText($txt, $lm, $yoffset);
		
		$txt = 'www.IHSAINC.com';
		$lm = $this->calcLeftMargin($txt);
		$this->_page->drawText($txt, $lm, $yoffset - 30 );
		
		/* draw partnership logo  */
			$ww = $this->_pageWidth ;
			$hh = 200;

			$x1 = 100;
			$y1 = $yoffset - 220;
			$x2 = $x1 + $ww - 200;
			$y2 = $y1 + 160;
		$this->_page->drawImage($this->_image2, $x1, $y1, $x2, $y2);
		
		$this->_page->restoreGS();
	}

	private function render2 () {
		$this->setStyle();
		$this->drawShowHeaderBig();
		$yoffset = $this->_pageHeight - 540;
		$this->drawZoneRgnBig($yoffset);
		$this->drawPageFooter();
		//$this->drawReportFooter($offset+40);
		return $this->_page;		
	}
	
	private function render1 () {
		$this->setStyle();
		$this->drawShowHeader();
		$this->drawParticipatingTeams();		
		$this->drawOfficials();
		$this->drawPageFooter();
		//$this->drawReportFooter($offset+40);
		return $this->_page;		
	}
	public function render()
	{
		if ($this->_flag == 1) {
			return $this->render1();
		} else {
			return $this->render2();
		}
	}


	function calcLeftMargin ($text) {
		$font = $this->_page->getFont();
		$fontsize = $this->_page->getFontSize();
		$w  = $this->_widthForStringUsingFontSize($text, $font, $fontsize);
		$ans = ($this->_pageWidth - $w) / 2;
		return $ans;
	}
	
	static function _widthForStringUsingFontSize($string, $font, $fontSize){     
		$drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);     
		$characters = array();     
		for ($i = 0; $i < strlen($drawingString); $i++) {         
			$characters[] = (ord($drawingString[$i++]) << 8 ) | ord($drawingString[$i]);     }     
			$glyphs = $font->glyphNumbersForCharacters($characters);     
			$widths = $font->widthsForGlyphs($glyphs);     
			$stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;     
			return $stringWidth; 
	}
}