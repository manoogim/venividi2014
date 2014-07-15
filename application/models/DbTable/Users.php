<?php

class Model_DbTable_Users extends Zend_Db_Table
{
	protected $_name="user";

	function checkUnique($email)
	{
		$select = $this->_db->select()
		->from($this->_name,array('email'))
		->where('email=?',$email);

		$result = $this->getAdapter()->fetchOne($select);
		if($result){
			return true;
		}
		return false;
	}

	function fetchUser($id) {
		$id = (int)$id;
		$row = $this->fetchRow('user_id = ' . $id);
		if (!$row) {
			throw new Exception("Count not find row $id");
		}
		return $row->toArray();
	}

	function updateUser($firstName,$lastName,$password,$userid,$phone) {
		$data = array(
				'first_name' => $firstName,
				'last_name' => $lastName,
				'password' => $password,
				'phone' => $phone,
		);

		$this->update($data, 'user_id=' . (int) $userid);
	}

	private function composeMail() {
		// send e-mail to ...
		$to=$email_to;

		// Your subject
		$subject="Your password here";

		// From
		$header="from: your name <your email>";

		// Your message
		$messages= "Your password for login to our website \r\n";
		$messages.="Your password is $your_password \r\n";
		$messages.="more message... \r\n";

		// send email
		$sentmail = mail($to,$subject,$messages,$header);

	}





	function sendPassword($email) {
		$where = 'EMAIL="' . $email . '"';
		$row = $this->fetchAll($where);

		if (count($row) > 0) {

			$smtpServer = 'localhost';
			$username = 'venividi.x10hosting.com';
			$password = 'welcome';


			$config = array('ssl' => 'tls',
			                'auth' => 'login',
			                'username' => $username,
			                'password' => $password);

			$transport = new Zend_Mail_Transport_Smtp($smtpServer, $config);


			$mail = new Zend_Mail();

			$mail->setFrom('+venividivici.x10hosting.com', 'passwordrecovery');
			$mail->addTo('manoogim@yahoo.com', 'Some Recipient');
			$mail->setSubject('Your password for venividivici');
			$mail->setBodyText('This is the text of the mail.');

			//$status = $mail->send($transport);
			$status=false;
			$code="foundit";
			if ($status) {
				$res = 'Your password has been emailed to: ' . $email;
			} else {
				$code="error";
				$res = 'There was a problem with the mail service, please try again later.';
			}

		} else {
			$code="warning";
			$res = 'We do not have any registered user with such email: ' . $email;
		}
		return array($code, $res);
	}

	public function emailShowLink($myuserid, $showlink, $coaches) {
		$usr = $this->fetchUser($myuserid);
		$headers = $this->_makeHeaders($usr['EMAIL']);

		$subject = $usr['FIRST_NAME'] . ' ' . $usr['LAST_NAME'] . ' has invited you to a show';

		//		$message = 'The link below will bring you to a login page.' . "\r\n" .
		//		'Your login id is the same as your email.' . "\r\n" .
		//		'Type your login id and your password.' .
		//		'Then you can view the show details and enter your team riders online' . "\r\n" .
		$message = 'Click the link below to view the show details (login required)' . "\r\n" .
		$showlink;

		$to = '';
		foreach ($coaches as $c) {
			$to = $to . $c['EMAIL'] . ', ';
		}
		$to = $to . $usr['EMAIL'];

		//		$this->logit('$to=' . $to);
		//		$this->logit('$$subject=' . $subject);
		//		$this->logit('$$$message=' . $message);
		//		$this->logit('$$$headers=' . $headers);
		mail($to, $subject, $message, $headers);
	}

	static function _makeHeaders($sender) {
		$headers = 'From: '. $sender . "\r\n" . 'Reply-To: ' . $sender . "\r\n" . 'X-Mailer: PHP/' . phpversion();
		return $headers;
	}


	public function emailConfirmPage($myuserid, $entries) {
		//		$mm = new Model_DbTable_Entries();
		//		$entries = $mm->fetchStudentEntries($showid);

		$usr = $this->fetchUser($myuserid);
		$headers = $this->_makeHeaders($usr['EMAIL']);
		//$headers = $this->buildHeaders();
		$subject = $usr['FIRST_NAME'] . ' ' . $usr['LAST_NAME'] . ' confirms show entries';
		$content = 'The following entries have been confirmed for the show:' . "\r\n";
		foreach ($entries as $team) {
			if (count($team->riders) > 0 ) {
				$content = $content . strtoupper($team->teamname) . "\r\n";
				foreach ($team->riders as $rider) {
					$content = $content . '' . $rider->ridername . ': ' . $rider->classes . "\r\n";
				}
				$content = $content . "\r\n";
			}
		}

		$message = $content;

		$to = '';
		foreach ($entries as $c) {
			$to = $to . $c->coachemail . ', ';
		}
		$to = $to . $usr['EMAIL'];

		//		$this->logit('$entries='. json_encode($entries));
		//		$this->logit('$to=' . $to);
		//		$this->logit('$$subject=' . $subject);
		//		$this->logit('$$$message=' . $message);
		//		$this->logit('$$$headers=' . $headers);
		mail($to, $subject, $message, $headers);
	}

	static public function makeLogoPath($userid) {
		// did user upload a logo ?
		$logofile = 'logo'. $userid .'.png';
		$logopath = realpath(INDEX_PATH) . '/data/' . $logofile;
		if (!file_exists($logopath)) {
			$logofile = 'logo'. $userid .'.jpg';
			$logopath = realpath(INDEX_PATH) . '/data/' . $logofile;
		}
		
		return $logopath;
	}

	static public function getUserLogo($userid, $eventtypeid) {
		// did user upload a logo ?
		$logopath = Model_DbTable_Users::makeLogoPath($userid);
		try {
			$ans = Zend_Pdf_Image::imageWithPath($logopath);
			//Model_DbTable_Users::logit('imagewithpath=' . $logopath);
		} catch (Exception $e) {
			//Model_DbTable_Users::logit('error imagewithpath=' . $e);
			$eve = Model_DbTable_EventTypes::getInstance()->fetchEventType($eventtypeid);
			$logopath =  realpath(INDEX_PATH) . '/data/' . $eve['LOGO_NAME'] ;
			//Model_DbTable_Users::logit('imagewithpath=' . $logopath);
			$ans = Zend_Pdf_Image::imageWithPath($logopath);
		}

		return $ans;
	}
	static public function deleteUserLogo($userid) {
		$logopath = Model_DbTable_Users::makeLogoPath($userid);
		if (file_exists($logopath)) {
			unlink($logopath);
		}
		// twice in case there is jpg and png
		$logopath = Model_DbTable_Users::makeLogoPath($userid);
		if (file_exists($logopath)) {
			unlink($logopath);
		}
	}
	static private function logit($xtra) {
		// YYYYmmdd-hhii
		$tstamp = date('Ymd-hi' , time());
		$fname = "c:/aaa/mylog.log" ;
		//mkdir($fname);
		$logme = $xtra ."\n";
		$fh = fopen($fname,'a');

		if (flock($fh, LOCK_EX))
		{
			fwrite($fh, $logme) ;
			flock($fh, LOCK_UN);
		}

		fclose($fh);

	}
}
?>