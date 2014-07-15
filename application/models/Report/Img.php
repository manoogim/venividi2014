<?php
class Model_Report_Img  {

	
	public function createImage() {
		$page = $this->createImagePage('images/front_cover.png');
		
		$report = new Model_Report_Imgdoc();

		$report->addPage($page);
		return $report;
	}
	public function createImagePage($imgpath) {
		$page = new Model_Report_Imgpage();
		
		$page->_image = Zend_Pdf_Image::imageWithPath($imgpath);
		

		return $page;
	}


}