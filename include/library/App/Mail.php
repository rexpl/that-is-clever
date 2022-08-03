<?php 

namespace Clever\Library\App;

use Clever\Library\App\Config;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
	private $mail;

	public $subject;

	public $body;

	public function __construct(Config $config) {

		$this->mail = new PHPMailer();

		$this->mail->isSMTP();
		$this->mail->SMTPDebug = SMTP::DEBUG_OFF;

		$this->mail->Host = $config->get('mail_host');
		$this->mail->Port = $config->get('mail_port');

		$this->mail->SMTPAuth = true;

		$this->mail->Username = $config->get('mail_username');
		$this->mail->Password = $config->get('mail_password');

		if ($config->get('mail_port') == 587) {

			$this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		}
		else {

			$this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		}

		$this->mail->setFrom($config->get('mail_from_mail'), $config->get('mail_from_name'));
		$this->mail->isHTML(true); 

		
	}

	public function addRecipient($email) {

		$this->mail->addAddress($email);
	}

	public function send() {

		$this->mail->Subject = $this->subject;
		$this->mail->Body = $this->body;

		return $this->mail->send();
	}

}