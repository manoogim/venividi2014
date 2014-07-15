<?php

require_once 'Zend/Date.php';

class Model_Report_Page
{
	public $_pagenum;
	protected $_page;
	protected $_pageWidth;
	protected $_pageHeight;
	protected $_yPosition;
	protected $_leftMargin;
	protected $_normalFont;
	protected $_boldFont;

	// array of single lines of text
	protected $_titles;

	// each section has 4 text-tokens for subtitle
	protected $_subTitle;

	// array of 'lines', each line is array of 4 text-tokens
	protected $_lines;

	// show name, and date
	public $_showinfo;

	public $numdash;
	
	public $total;
	
	public $sectiontotals;

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


	public function setTitles($titles)
	{
		$this->_titles = $titles;
	}

	public function setSubTitle($subTitle) {
		$this->_subTitle = $subTitle;
	}

	public function setLines($lines) {
		$this->_lines = $lines;
	}

	// returns size of page space taken
	private function drawSection($idx, $offset)
	{
		$this->_page->saveGS();
		//$y = $idx * 200;
		// title
		$this->_page->setFont($this->_boldFont, 20);
		$this->_page->drawText($this->_titles[$idx], $this->_leftMargin, $this->_pageHeight - 50 - $offset);
		$this->_page->drawLine($this->_leftMargin, $this->_pageHeight - 60 - $offset, $this->_pageWidth -$this->_leftMargin, $this->_pageHeight - 60 - $offset);

		// subtitle
		$this->_page->setFont($this->_boldFont, 10);
		$subtitles = array_values($this->_subTitle);
		array_unshift($subtitles,'  ');
		$xoffsets = array(0, 20, 80, 200, 320);
		$this->_writeOneLine(80+$offset,$subtitles,$xoffsets);

		// section content
		$this->_page->setFont($this->_normalFont, 10);
		$nn = count($this->_lines[$idx]);
		for ($jj=0; $jj<$nn; $jj++) {
			$this->writeOneLine($offset, $idx, $jj);
		}

		// section footer
		$y = 120 + $nn * 12;
		$this->drawSectionFooter($idx, $y + $offset);
		
		// dashes  (controled by numdash >0)
		$dashes = array();
		for ($kk=0; $kk<$this->numdash; $kk++)  {
			$d = (1+$kk) . '. ';
			$dashes[] = $d . '____ ';
		}
		$xoffsets =array (20, 70, 120, 170, 220, 270, 320);		
		$this->_writeOneLine($y  + $offset, $dashes, $xoffsets);
		 
		// done with section
		$this->_page->restoreGS();

		return   $y;
	}

	private function drawSectionFooter($idx, $yoffset) {
		if (isset($this->sectiontotals)) {
			$this->_page->saveGS();
			$this->_page->setFont($this->_boldFont, 10);
			$xoffsets = array(0);
			$text = array( $this->sectiontotals[$idx]);
			$this->_writeOneLine($yoffset, $text, $xoffsets);
			$this->_page->restoreGS();
		}
	}

	/** most generic func to output n fixed length texts
	 * $yoffset = constant y offset from the bottom
	 * $line = array of texts
	 * $xoffset_arr = offset from left margin for each word
	 * */
	private function _writeOneLine ($yoffset, $line, $xoffset_arr) {
		$nn = count($line);
		for ($jj=0; $jj<$nn; $jj++) {
			$txt = $line[$jj];
			$xoffset = $xoffset_arr[$jj];

			$this->_page->drawText(
			$txt, $this->_leftMargin+$xoffset, $this->_pageHeight - $yoffset);
		}

	}
	/** output one section entry from $this->lines
	 * $idx = section number
	 * $jj = line number within section */
	private function writeOneLine($prevoffset, $idx, $jj) {
		//    	$msg =  'prevoffset=' . $prevoffset . 'idx=' . $idx . 'jj=' . $jj;
		//    	$this->logit($msg);
		$sect = $this->_lines[$idx];
		$line = $sect[$jj];
		$cnt = (1+$jj) . '.';
		array_unshift($line,  $cnt);

		$xoffsets = array(0, 20, 80, 200, 320);
		$localoffset = 100 + 12 * $jj;
		$offset = $prevoffset + $localoffset;
		$this->_writeOneLine($offset, $line, $xoffsets);
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

		$pg = 'Page -' . $this->_pagenum . '-';
		$txt =$pg  
		. '                                                                                                          '
		. '                           © ' 
		. APP_NAME;
		$this->_page->drawText($txt, $this->_leftMargin, 20);
		// $this->_page->drawText( APP_NAME, $this->_rightMargin, 40);
		$this->_page->restoreGS();
	}
	private function drawReportFooter($offset)
	{
		if (isset($this->total)) {
			$this->_page->saveGS();
			$this->_page->setFont($this->_boldFont, 12);
			// if total is present :
			$this->_writeOneLine($offset, array($this->total), array(0));

			$this->_page->restoreGS();
		}
	}
	public function render()
	{
		$this->setStyle();
		$this->drawPageHeader();
		$nn = count($this->_titles);
		$offset = 0;
		for ($idx=0; $idx<$nn; $idx++) {
			$size = $this->drawSection($idx, $offset);
			$offset += $size;
		}

		$this->drawPageFooter();
		$this->drawReportFooter($offset+40);
		return $this->_page;
	}


}