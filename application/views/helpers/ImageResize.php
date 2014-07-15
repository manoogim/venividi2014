<?php
class My_View_Helper_ImageResize
{
	public $view;

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
	// <img height= src="<?php echo $this->baseUrl() . '/data/uploads/logo13.png';
	//using a standard html image tag, where you would have the
	//width and height, insert your new imageResize() function with
	//the correct attributes -->
	//
	//<img src="images/sock001.jpg" <?php imageResize($mysock[0],  $mysock[1], 150);
	function imageResize() {
		$defaultans='';
		$ns = new Zend_Session_Namespace('myshows');
		$userid = isset($ns->showid) ? $ns->show->owner : 0;
		$logofile = '/data/logo'. $userid .'.png';
		$logopath = realpath(APPLICATION_PATH) . '/../public/' . $logofile;
//		$defaultans = '$logofile=' . $logofile;
//		echo '$defaultans=' . $defaultans;
		if (!isset($userid) ) {
			$ans = $defaultans;
		} else if (!file_exists($logopath) || false) {
			$ans = $defaultans;
//			echo '$logofile' . $logofile;
		} else {
			//get the image size of the picture and load it into an array
			$mysock = getimagesize($logopath);
			$sizepart = $this->_imageResize($mysock[0], $mysock[1], 55);
			$ans = '<img src="'. $this->view->baseUrl() . $logofile .'" ' .  $sizepart . ' />';
			
			//$ans = '<img valign="middle" src="/venividivici/public/images/arrow_down.gif"  border="0" width="150" height="112" >'; //'<img src="/venividivici/public/data/logo13.png"  border="0" />';
		}

		return $ans;
	}

	private function _imageResize($width, $height, $target) {

		//takes the larger size of the width and height and applies the
		//formula accordingly...this is so this script will work
		//dynamically with any size image

		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}

		//gets the new value and applies the percentage, then rounds the value
		$width = round($width * $percentage);
		$height = round($height * $percentage);

		//returns the new sizes in html image tag format...this is so you
		//can plug this function inside an image tag and just get the

		return "width=\"$width\" height=\"$height\"";
	}
}