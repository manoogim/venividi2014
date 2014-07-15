<?php
class Model_DbTable_Contacts extends Zend_Db_Table
{
	protected $_name="comments";

	public function addComment($data) {
		$this->insert($data);
		$this->emailme($data);
	}

	private function emailme($data) {
		$sender = $data['EMAIL'];
		$subjectid = $data['SUBJECT'];
		$options =  $this->getOptions();
		$subject ='venividivici comment=' . $options[$subjectid];
		$message= $sender . " sent this comment\n " . $data['COMMENT'];

		$headers = $this->buildHeaders();
		mail('manoogim@yahoo.com', $subject, $message, $headers);


	}
	
	function buildHeaders() {
		$headers = 'From: webmaster@venividivici.com' . "\r\n" .
	    'Reply-To: webmaster@venividivici.com' . "\r\n" .
    	'X-Mailer: PHP/' . phpversion();
		return $headers;
	}
	
	function getOptions() {
		/*		->addMultiOption('1','Love the program  :-) ' )
		->addMultiOption('2','Hate the program  :-( ' )
		->addMultiOption('3','Found a bug  !#$%^&*' )
		->addMultiOption('4', 'Other');*/
		$ret = array('1'=> 'Love the program  :-) ',
		'2'=>'Hate the program  :-( ',
		'3'=>'Found a bug  !#$%^&*',
		'4'=>'Other');
		return $ret;
	}
}
?>