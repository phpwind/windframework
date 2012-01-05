<?php
Wind::import('WIND:mail.exception.WindMailException');
/**
 * 邮件发送类
 *
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package mail
 */
class WindMail {
	/**
	 * @var array 邮件头
	 */
	private $mailHeader = array();
	/**
	 * @var array 邮件附件
	 */
	private $attachment = array();
	/**
	 * @var string 邮件字符集
	 */
	private $charset = 'utf-8';
	/**
	 * @var string 是否是内嵌资源
	 */
	private $embed = false;
	/**
	 * @var array 邮件收件人
	 */
	private $recipients = null;
	/**
	 * @var string 邮件消息体html展现方式
	 */
	private $bodyHtml = '';
	/**
	 * @var string 邮件消息体文本展现方式
	 */
	private $bodyText = '';
	/**
	 * @var array 邮件边界线
	 */
	private $boundary;
	/**
	 * @var string 邮件编码方式
	 */
	private $encode = self::ENCODE_BASE64;
	
	//常用邮件MIME
	const CRLF = "\n";
	const TO = 'To';
	const CC = 'Cc';
	const BCC = 'Bcc';
	const FROM = 'From';
	const SUBJECT = 'Subject';
	const MESSAGEID = 'Message-Id';
	const CONTENTTYPE = 'Content-Type';
	const CONTENTENCODE = 'Content-Transfer-Encoding';
	const CONTENTID = 'Content-ID';
	const CONTENTPOSITION = 'Content-Disposition';
	const CONTENTDESCRIPT = 'Content-Description';
	const CONTENTLOCATION = 'Content-Location';
	const CONTENTLANGUAGE = 'Content-Language';
	const DATE = 'Date';
	
	//邮件MIME类型
	const MIME_OCTETSTREAM = 'application/octet-stream';
	const MIME_TEXT = 'text/plain';
	const MIME_HTML = 'text/html';
	const MIME_ALTERNATIVE = 'multipart/alternative';
	const MIME_MIXED = 'multipart/mixed';
	const MIME_RELATED = 'multipart/related';
	
	//邮件编码
	const ENCODE_7BIT = '7bit';
	const ENCODE_8BIT = '8bit';
	const ENCODE_QP = 'quoted-printable';
	const ENCODE_BASE64 = 'base64';
	const ENCODE_BINARY = 'binary';
	
	//邮件编码内容
	const DIS_ATTACHMENT = 'attachment';
	const DIS_INLINE = 'inline';
	const LINELENGTH = 72;
	
	//邮件发送方式
	const SEND_SMTP = 'smtp';
	const SEND_PHP = 'php';
	const SEND_SEND = 'send';

	/**
	 * 发送邮件
	 *
	 * @param string $type 发送类型
	 * @param array $config 邮件发送器需要的配置数据
	 * @return boolean
	 */
	public function send($type = self::SEND_SMTP, $config = array()) {
		try {
			$class = Wind::import('Wind:mail.sender.Wind' . ucfirst($type) . 'Mail');
			if (!class_exists($class)) throw new WindMailException(
				'[mail.WindMail.send] There is no way that you want to send e-mail \'' . $type . '\'');
			/* @var $sender IWindSendMail */
			$sender = new $class();
			$sender->send($this, $config);
			return true;
		} catch (Exception $e) {
			if (WIND_DEBUG & 2) Wind::getApp()->getComponent('windLogger')->info(
				'[mail.WindMail.send] send mail fail. ' . $e->getMessage(), 'windmail');
			if (WIND_DEBUG & 1) throw new WindMailException('[mail.WindMail.send] send mail fail.' . $e->getMessage());
			return false;
		}
	}

	/**
	 * 创建邮件头信息
	 * 
	 * @return string
	 */
	public function createHeader() {
		if (!isset($this->mailHeader[self::CONTENTTYPE])) {
			$type = self::MIME_TEXT;
			if ($this->attachment)
				$type = $this->embed ? self::MIME_RELATED : self::MIME_MIXED;
			elseif ($this->bodyHtml)
				$type = $this->bodyText ? self::MIME_ALTERNATIVE : self::MIME_HTML;
			$this->setContentType($type);
		}
		if (!isset($this->mailHeader[self::CONTENTENCODE])) $this->setContentEncode();
		$header = '';
		foreach ($this->mailHeader as $key => $value) {
			if (!$value) continue;
			$header .= $key . ': ';
			if (is_array($value)) {
				foreach ($value as $_key => $_value)
					$header .= (is_string($_key) ? $_key . ' ' . $_value : $_value) . ',';
				$header = trim($header, ',');
			} else
				$header .= $value;
			$header .= self::CRLF;
		}
		return $header . self::CRLF;
	}

	/**
	 * 创建邮件消息体
	 * 
	 * @return string
	 */
	public function createBody() {
		$body = $end = '';
		if ($this->bodyText) $body .= $this->_encode($this->bodyText);
		if ($this->bodyHtml) {
			if ($body !== '') {
				$textHeader = self::CONTENTTYPE . ': text/plain; charset=' . $this->charset . self::CRLF;
				$textHeader .= self::CONTENTENCODE . ': ' . $this->encode . self::CRLF;
				$body = $this->_boundaryStart() . $textHeader . $body . self::CRLF;
				$htmlHeader = self::CONTENTTYPE . ': text/html; charset=' . $this->charset . self::CRLF;
				$htmlHeader .= self::CONTENTENCODE . ': ' . $this->encode . self::CRLF . self::CRLF;
				$body .= $this->_boundaryStart() . $htmlHeader;
				$end = $this->_boundaryEnd();
			}
			$body .= $this->_encode($this->bodyHtml) . self::CRLF;
		}
		if ($this->attachment) {
			$body .= $this->_attach() . self::CRLF;
			$end = $this->_boundaryEnd();
		}
		return $body . $end;
	}

	/**
	 * 取得真实的收件人
	 * 
	 * @return array
	 */
	public function getRecipients() {
		if ($this->recipients === null) {
			$tmp = array_merge($this->getTo(), $this->getCc(), $this->getBcc());
			if ($tmp) {
				foreach ($tmp as $key => $value)
					$this->recipients[] = $value;
			}
		}
		return $this->recipients;
	}

	/**
	 * 设置发件人
	 * 
	 * @param string $email 发件人邮箱
	 * @param string $name  发件人姓名
	 * @return void
	 */
	public function setFrom($email, $name = null) {
		if (!$email || !is_string($email)) return;
		$email = $name ? array($name => $email) : array($email);
		$this->setMailHeader(self::FROM, $email, false);
	}

	/**
	 * 取得发件人
	 * 
	 * @return string
	 */
	public function getFrom() {
		$from = $this->getMailHeader(self::FROM);
		return is_array($from) ? array_pop($from) : $from;
	}

	/**
	 * 设置收件人
	 * 
	 * @param string|array $email 收件人邮箱
	 * @param string $name  收件人姓名
	 */
	public function setTo($email, $name = null) {
		if (!$email) return;
		if (!is_array($email)) $email = $name ? array($name => $email) : array($email);
		$this->setMailHeader(self::TO, $email);
	}

	/**
	 * 取得收件人
	 * 
	 * @return array
	 */
	public function getTo() {
		return $this->getMailHeader(self::TO);
	}

	/**
	 * 设置抄送人
	 * 
	 * @param string $email 抄送人邮箱
	 * @param string $name  抄送人姓名
	 */
	public function setCc($email, $name = null) {
		if (!$email) return;
		if (!is_array($email)) $email = $name ? array($name => $email) : array($email);
		$this->setMailHeader(self::CC, $email);
	}

	/**
	 * 取得抄送的对象
	 * 
	 * @return array
	 */
	public function getCc() {
		return $this->getMailHeader(self::CC);
	}

	/**
	 * 设置暗送人
	 * 
	 * @param string $email 暗送人邮箱
	 * @param string $name  暗送人姓名
	 */
	public function setBcc($email, $name = null) {
		if (!$email) return;
		if (!is_array($email)) $email = $name ? array($name => $email) : array($email);
		$this->setMailHeader(self::BCC, $email);
	}

	/**
	 * 取得暗送对象
	 * 
	 * @return array
	 */
	public function getBcc() {
		return $this->getMailHeader(self::BCC);
	}

	/**
	 * 设置邮件主题
	 * 
	 * @param string $subject 主题
	 */
	public function setSubject($subject) {
		$this->setMailHeader(self::SUBJECT, $this->_encodeHeader($subject), false);
	}

	/**
	 * 取得邮件主题
	 * 
	 * @return string
	 */
	public function getSubject() {
		$subject = $this->getMailHeader(self::SUBJECT);
		is_array($subject) && $subject = $subject[0];
		return str_replace(array("\r", "\n"), array('', ' '), $subject);
	}

	/**
	 * 设置邮件日期
	 * 
	 * @param string $data
	 */
	public function setDate($date) {
		$this->setMailHeader(self::DATE, $date);
	}

	/**
	 * 设置邮件头
	 * 
	 * @param string $name 邮件头名称
	 * @param string $value 邮件头对应的值
	 * @param boolean $append 是否是追加
	 * @return void
	 */
	public function setMailHeader($name, $value, $append = true) {
		is_array($value) || $value = array($value);
		if (false === $append || !isset($this->mailHeader[$name])) {
			$this->mailHeader[$name] = $value;
		} else {
			foreach ($value as $key => $_value) {
				if (is_string($key))
					$this->mailHeader[$name][$key] = $_value;
				else
					$this->mailHeader[$name][] = $_value;
			}
		}
	}

	/**
	 * 返回邮件头信息值
	 *
	 * @param string $name
	 */
	public function getMailHeader($name) {
		if (!$name) return $this->mailHeader;
		return isset($this->mailHeader[$name]) ? $this->mailHeader[$name] : array();
	}

	/**
	 * 设置邮件消息ID 
	 */
	public function setMessageId() {
		$user = array_pop($this->getFrom());
		$user || $user = getmypid();
		if ($recipient = $this->getRecipients()) {
			$recipient = array_rand($recipient);
		} else
			$recipient = 'No recipient';
		$host = isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : php_uname('n');
		$message = sha1(time() . $user . mt_rand() . $recipient) . '@' . $host;
		$this->setMailHeader(self::MESSAGEID, '<' . $message . '>');
	}

	/**
	 * 设置邮件编码
	 * 
	 * @param string $encode
	 */
	public function setContentEncode($encode = self::ENCODE_BASE64) {
		$this->encode = $encode;
		$this->setMailHeader(self::CONTENTENCODE, $encode);
	}

	/**
	 * 设置邮件类型
	 * 
	 * @param string $type
	 */
	public function setContentType($type = self::MIME_TEXT) {
		if (self::MIME_TEXT == $type || self::MIME_HTML == $type)
			$contentType = sprintf("%s; charset=\"%s\"", $type, $this->charset);
		elseif (self::MIME_RELATED == $type)
			$contentType = sprintf("%s;%s type=\"text/html\";%s boundary=\"%s\"", self::MIME_RELATED, self::CRLF, 
				self::CRLF, $this->_boundary());
		else
			$contentType = sprintf("%s;%s boundary=\"%s\"", $type, self::CRLF, $this->_boundary());
		$this->setMailHeader(self::CONTENTTYPE, $contentType, false);
	}

	/**
	 * 上传附件
	 * 
	 * @return string
	 */
	private function _attach() {
		$attach = '';
		foreach ($this->attachment as $key => $value) {
			list($stream, $mime, $disposition, $encode, $filename, $cid) = $value;
			$filename || $filename = 'attachment_' . $key;
			$attachHeader = sprintf(self::CONTENTTYPE . ": %s; name=\"%s\"%s", $mime, $filename, self::CRLF);
			$attachHeader .= sprintf(self::CONTENTENCODE . ": %s%s", $encode, self::CRLF);
			if ($disposition == 'inline') $attachHeader .= sprintf(self::CONTENTID . ": <%s>%s", $cid, self::CRLF);
			$attachHeader .= sprintf(self::CONTENTPOSITION . ": %s; filename=\"%s\"%s%s", $disposition, $filename, 
				self::CRLF, self::CRLF);
			$attach .= $this->_boundaryStart() . $attachHeader . $this->_encode($stream, $encode) . self::CRLF;
		}
		return $attach;
	}

	/**
	 * 取得下一个quoted-printable
	 * @param string $string  
	 * @return string
	 */
	private static function getNextQpToken($string) {
		return '=' == substr($string, 0, 1) ? substr($string, 0, 3) : substr($string, 0, 1);
	}

	/**
	 * 获取边界线
	 * 
	 * @return string
	 */
	private function _boundaryStart() {
		return self::CRLF . '--' . $this->_boundary() . self::CRLF;
	}

	/**
	 * 获取结束边界线
	 * 
	 * @return string
	 */
	private function _boundaryEnd() {
		return self::CRLF . '--' . $this->_boundary() . '--' . self::CRLF;
	}

	/**
	 * 设置并返回边界线
	 * 
	 * @return string
	 */
	private function _boundary() {
		if (!$this->boundary) {
			$this->boundary = '==_' . md5(microtime(true) . uniqid());
		}
		return $this->boundary;
	}

	/**
	 * 编码邮件内容
	 * 
	 * @param string $message
	 * @param string $encode
	 * @return string
	 */
	private function _encode($message, $encode = '') {
		$encode || $encode = $this->encode;
		$mailEncoder = Wind::import("WIND:mail.encode.WindMail" . ucfirst($encode));
		if (!class_exists($mailEncoder)) throw new WindMailException(
			'[mail.WindMail._encode] encod class for ' . $encode . ' is not exist.');
		/* @var $mailEncoder IWindMailEncoder */
		$mailEncoder = new $mailEncoder();
		return $mailEncoder->encode(trim($message), self::LINELENGTH, self::CRLF);
	}

	/**
	 * 编码邮件头部
	 *
	 * @param string $message
	 * @param string $encode
	 * @return string
	 */
	private function _encodeHeader($message, $encode = '') {
		$encode || $encode = $this->encode;
		$mailEncoder = Wind::import("WIND:mail.encode.WindMail" . ucfirst($encode));
		if (!class_exists($mailEncoder)) throw new WindMailException(
			'[mail.WindMail._encode] encod class for ' . $encode . ' is not exist.');
		/* @var $mailEncoder IWindMailEncoder */
		$mailEncoder = new $mailEncoder();
		$message = strtr(trim($message), array("\r" => '', "\n" => '', "\r\n" => ''));
		return $mailEncoder->encodeHeader($message, $this->charset, self::LINELENGTH, self::CRLF);
	}

	/**
	 * 设置附件
	 * 
	 * @param string $stream 附件名或者附件内容
	 * @param string $mime   附件类型
	 * @param string $disposition 附件展现方式
	 * @param string $encode 附件编码
	 * @param string $filename 文件名
	 * @param string $cid 内容ID
	 */
	public function setAttachment($stream, $mime = self::MIME_OCTETSTREAM, $disposition = self::DIS_ATTACHMENT, $encode = self::ENCODE_BASE64, $filename = null, $cid = 0) {
		$this->attachment[] = array($stream, $mime, $disposition, $encode, $filename, $cid);
	}

	/**
	 * 设置邮件html展示内容
	 * @param string $bodyHtml
	 */
	public function setBodyHtml($bodyHtml) {
		$this->bodyHtml = $bodyHtml;
	}

	/**
	 * 设置邮件文本展示内容
	 * @param string $bodyText
	 */
	public function setBodyText($bodyText) {
		$this->bodyText = $bodyText;
	}

	/**
	 * 设置邮件字符
	 * @param string $charset
	 */
	public function setCharset($charset) {
		$this->charset = $charset;
	}

	/**
	 * 设置是否是内嵌资源
	 * @param boolean $embed
	 */
	public function setEmbed($embed = false) {
		$this->embed = $embed;
	}
}