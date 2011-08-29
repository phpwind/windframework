<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * 邮件发送类
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMail {
	
    /**
     * @var string  邮件内容仅文本
     */
    const ONLYTEXT = 1;
    /**
     * @var string  邮件内容仅HTML
     */
    const ONLYHTML = 2;
    /**
     * @var string  邮件内容是文本和HTML
     */
    const TEXTHTML = 3;
    /**
     * @var string  邮件内容有附件
     */
    const ATTACH = 4;
    /**
     * @var string  无邮件内容
     */
    const NONE = 5;
    
    /**
	 * @var array 邮件收件人
	 */
	private $recipients = array();
	/**
	 * @var array 邮件头
	 */
	private $mailHeader = array();
	/**
	 * @var array 邮件边界线
	 */
	private $boundary = '';
	/**
	 * @var array 邮件附件
	 */
	private $attachment = array();
	/**
	 * @var string 邮件字符集
	 */
	private $charSet = 'gbk';
	
	/**
	 * @var string 邮件mime类型
	 */
	private $contentType = self::MIME_TEXT;
	/**
	 * @var string 邮件消息体html展现方式
	 */
	private $bodyHtml = '';
	/**
	 * @var string 邮件消息体文本展现方式
	 */
	private $bodyText = '';
	/**
	 * @var string 是否是内嵌资源
	 */
	private $embed = false;
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
	const CONTENTTYPE='Content-Type';
	const CONTENTENCODE = 'Content-Transfer-Encoding';
	const CONTENTID = 'Content-ID';
	const CONTENTPOSITION = 'Content-Disposition';
	const CONTENTDESCRIPT = 'Content-Description';
	const CONTENTLOCATION = 'Content-Location';
	const CONTENTLANGUAGE='Content-Language';
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
    
    const DIS_ATTACHMENT = 'attachment';
    const DIS_INLINE = 'inline';
    const LINELENGTH = 72;
    
    const SEND_SMTP = 'smtp';
    const SEND_PHP = 'php';
    const SEND_SEND = 'send';
    
    public function send($type = self::SEND_SMTP,$config = array()){
    	if(!in_array($type,array(self::SEND_SMTP,self::SEND_PHP,self::SEND_SEND))){
    		throw new WindException('There is no way that you want to send e-mail');
    	}
    	$class = Wind::import('Wind:mail.sender.Wind'.ucfirst($type).'Mail');
    	/* @var $sender IWindSendMail */
    	$sender = new $class($config);
    	$sender->send($this);
    	return true;
    }
	/**
	 * 创建邮件头
	 * @return string
	 */
	public function createHeader(){
		$header = '';
		if(!isset($this->mailHeader[self::CONTENTTYPE])){
			$this->setContentType(null);
		}
		foreach($this->mailHeader as $key=>$value){
			$header .= $this->headerLine($key,$value);
		}
		return $header.self::CRLF;
	}
	
	/**
	 * 创建邮件消息体
	 * @return string
	 */
	public function createBody(){
		$mime = $this->getMimeType();
		if(self::ONLYTEXT === $mime){
			return self::encode($this->getBodyText(),$this->encode);
		}elseif(self::ONLYHTML === $mime){
			return self::encode($this->getBodyHtml(),$this->encode).self::CRLF;
		}elseif(self::TEXTHTML === $mime){
			$boundary = $this->boundaryLine();
			$body  = $boundary.$this->getTextHeader().self::encode($this->getBodyText(),$this->encode).self::CRLF;
			$body .= $boundary.$this->getHtmlHeader().self::encode($this->getBodyHtml(),$this->encode).self::CRLF;
			return $body .= $this->boundaryEndLine();
		}elseif(self::ATTACH === $mime){
			$boundary = $this->boundaryLine();
			$body = '';
			if('' != ($text = $this->getBodyText())){
				$body  .= $boundary.$this->getTextHeader().self::encode($text,$this->encode).self::CRLF;
			}
			if('' != ($html = $this->getBodyHtml())){
				$body .= $boundary.$this->getHtmlHeader().self::encode($html,$this->encode).self::CRLF;
			}
			$body .= $this->attach().self::CRLF;
			return $body .= $this->boundaryEndLine();
		}
		return '';
	}
	
	
	
	/**
	 * 设置邮件头
	 * @param string $name 邮件头名称
	 * @param string $value 邮件头对应的值
	 * @param boolean $append 是否是追加
	 * @return boolean
	 */
	public function setMailHeader($name, $value,$append = true) {
		$value = is_array($value) ? $value : array($value);
		if(false === $append){
			$this->mailHeader[$name] = $value;
			return true;
		}
		if (!isset($this->mailHeader[$name])) {
			$this->mailHeader[$name] = $value;
		}else{ 
			foreach($value as $key=>$value){
				if(is_string($key)){
					$this->mailHeader[$name][$key]=$value;
				}else{
					$this->mailHeader[$name][]=$value;
				}
			}
		}
		return true;
	}
	
	/**
	 * 设置发件人
	 * @param string $email 发件人邮箱
	 * @param string $name  发件人姓名
	 */
	public function setFrom($email, $name = null) {
		$value = $name ? array($name => $email) : array($email);
		$this->setMailHeader(self::FROM, $value,false);
	}
	
	/**
	 * 设置收件人
	 * @param string $email 收件人邮箱
	 * @param string $name  收件人姓名
	 */
	public function setTo($email, $name = null) {
		$value = $name ? array($name => $email) : array($email);
		$this->setMailHeader(self::TO, $value);
	}
	/**
	 * 设置抄送人
	 * @param string $email 抄送人邮箱
	 * @param string $name  抄送人姓名
	 */
	public function setCc($email, $name = null) {
		$value = $name ? array($name => $email) : array($email);
		$this->setMailHeader(self::CC, $value);
	}
	/**
	 * 设置暗送人
	 * @param string $email 暗送人邮箱
	 * @param string $name  暗送人姓名
	 */
	public function setBcc($email, $name = null) {
		$value = $name ? array($name => $email) : array($email);
		$this->setMailHeader(self::BCC, $value);
	}
	
	/**
	 * 设置邮件主题
	 * @param string $subject 主题
	 */
	public function setSubject($subject) {
		$this->setMailHeader(self::SUBJECT, $subject,false);
	}
	
	/**
	 * 设置邮件日期
	 * @param boolean $ifchinese 是否是中国日期
	 */
	public function setDate($date = null,$ifchinese = true){
		if(!$date){
			Wind::import ( 'WIND:utility.date.WindDate' );
			$date = $ifchinese ? WindDate::getChinaDate() : WindDate::getRFCDate();
		}
		$this->setMailHeader(self::DATE,$date );
	}
	
	/**
	 *设置邮件消息ID 
	 */
	public function setMessageId() {
		$this->setMailHeader(self::MESSAGEID, '<' . $this->createMessageId() . '>');
	}
	
	/**
	 * 设置邮件html展示内容
	 * @param string $bodyHtml
	 */
	public function setBodyHtml($bodyHtml){
		$this->bodyHtml = $bodyHtml;
	}
	/**
	 * 设置邮件文本展示内容
	 * @param string $bodyText
	 */
	public function setBodyText($bodyText){
		$this->bodyText = $bodyText;
	}
	
	/**
	 * 设置邮件类型
	 * @param string $type
	 */
	public function setContentType($type = null){
		if(!$type){
			$mime = $this->getMimeType();
			if(self::ONLYTEXT === $mime){
				$type = self::MIME_TEXT;
			}elseif(self::ONLYHTML === $mime){
				$type = self::MIME_HTML;
			}elseif(self::TEXTHTML === $mime){
				$type = self::MIME_ALTERNATIVE;
			}elseif(self::ATTACH === $mime && $this->embed){
				$type = self::MIME_RELATED; 
			}elseif(self::ATTACH === $mime && !$this->embed){
				$type = self::MIME_MIXED;
			}else{
				$type = self::MIME_TEXT;
			}
		}
		if(self::MIME_TEXT == $type || self::MIME_HTML == $type){
			$contentType = sprintf("%s; charset=\"%s\"", $type, $this->charSet);
		}elseif(self::MIME_RELATED == $type){
			$this->setBoundary();
			$contentType = sprintf("%s;%s type=\"text/html\";%s boundary=\"%s\"", self::MIME_RELATED, self::CRLF, self::CRLF, $this->getBoundary());
		}else{
			$this->setBoundary();
			$contentType = sprintf("%s;%s boundary=\"%s\"",$type,self::CRLF,$this->getBoundary());
		}
		$this->contentType = $type;
		$this->setMailHeader(self::CONTENTTYPE,$contentType,false);
	}
	
	/**
	 * 设置是否是内嵌资源
	 * @param boolean $embed
	 */
	public function setEmbed($embed = false){
		$this->embed = $embed;
	}
	
	/**
	 * 设置邮件编码
	 * @param string $encode
	 */
	public function setContentEncode($encode = self::ENCODE_BASE64){
		$this->encode = $encode;
		$this->setMailHeader(self::CONTENTENCODE,$encode);
	}
	
	/**
	 * 设置邮件字符
	 * @param string $charset
	 */
	public function setCharset($charset){
		$this->charSet = $charset;
	}
	
 	/**
 	 * 设置附件
 	 * @param string $stream 附件名或者附件内容
 	 * @param string $mime   附件类型
 	 * @param string $disposition 附件展现方式
 	 * @param string $encode 附件编码
 	 * @param string $filename 文件名
 	 * @param string $cid 内容ID
 	 */
 	public function setAttachment($stream, $mime = self::MIME_OCTETSTREAM, $disposition = self::DIS_ATTACHMENT, $encode    = self::ENCODE_BASE64,$filename    = null,$cid = 0){
	
    	$this->attachment[] = array(
    		 $stream,
    		 $mime,
    		 $disposition,
    		 $encode,
    		 $filename,
    		 $cid
    	);
    }
    
	/**
	 * 设置边界线
	 */
	public function setBoundary() {
		$this->boundary = '==_' . md5(microtime(true) . uniqid());
	}
	
	public function getMailHeader($name = null,$subname = null){
		if(!$name){
			return $this->mailHeader;
		}
		if(!isset($this->mailHeader[$name])){
			return '';
		}
		$header = $this->mailHeader[$name];
		if(!$subname || !isset($header[$subname])){
			return $header;
		}
		return $header[$subname];
	}
	
	/**
	 * 取得发件人
	 * @return array
	 */
	public function getFrom() {
		if(isset($this->mailHeader[self::FROM])){
			$from = $this->mailHeader[self::FROM];
			return is_array($from) ? array_shift($from) : $from;
		}
		return '';
	}
	/**
	 * 取得收件人
	 * @return array
	 */
	public function getTo() {
		return isset($this->mailHeader[self::TO]) ? $this->mailHeader[self::TO] : array();
	}
	
	/**
	 * 取得抄送的对象
	 * @return array
	 */
	public function getCc() {
		return isset($this->mailHeader[self::CC]) ? $this->mailHeader[self::CC] : array();
	}
	
	/**
	 * 取得暗送对象
	 * @return array
	 */
	public function getBcc() {
		return isset($this->mailHeader[self::BCC]) ? $this->mailHeader[self::BCC] : array();
	}
	
	/**
	 * 取得邮件主题
	 * @return array
	 */
	public function getSubject() {
		if(isset($this->mailHeader[self::SUBJECT])){
			$subject = $this->mailHeader[self::SUBJECT];
			return is_array($subject) ? array_shift($subject) : $subject;
		}
		return '';
	}
	
	/**
	 * 取得边界符
	 * @return array
	 */
	public function getBoundary() {
		return $this->boundary;
	}
	
	/**
	 * 取得mime类型
	 * @return string
	 */
	public function getMimeType(){
		if('' != $this->getBodyText() &&  '' == $this->getBodyHtml() && false == $this->hasAttachment()){
			return self::ONLYTEXT;
		}elseif('' == $this->getBodyText() &&  '' != $this->getBodyHtml() && false == $this->hasAttachment()){
			return self::ONLYHTML;
		}elseif('' != $this->getBodyText() &&  '' != $this->getBodyHtml() && false == $this->hasAttachment()){
			return self::TEXTHTML;
		}elseif($this->hasAttachment()){
			return self::ATTACH;
		}else{
			return self::NONE;
		}
		return 0;
	}
	
	/**
	 * 取得真实的收件人
	 * @return array
	 */
	public function getRecipients() {
		$this->buildRecipients($this->getTo());
		$this->buildRecipients($this->getCc());
		$this->buildRecipients($this->getBcc());
		return $this->recipients;
	}
	
	/**
	 * 取得消息中的html
	 * @return string
	 */
	public function getBodyHtml(){
		return trim($this->bodyHtml);
	}
	
	/**
	 * 取得消息中的文本
	 * @return string
	 */
	public function getBodyText(){
		return trim($this->bodyText);
	}
	
	/**
	 * 取得文本头
	 * @return string
	 */
	public function getTextHeader(){
		$textHeader = self::CONTENTTYPE.': text/plain; charset='.$this->charSet.self::CRLF;
		return $textHeader .= self::CONTENTENCODE.': '.$this->encode.self::CRLF;
	}
	
	/**
	 * 取得html头
	 * @return string
	 */
	public function getHtmlHeader(){
		$htmlHeader = self::CONTENTTYPE.': text/html; charset='.$this->charSet.self::CRLF;
		return $htmlHeader .= self::CONTENTENCODE.': '.$this->encode.self::CRLF.self::CRLF;
	}
	
	/**
	 * 取得附件的头
	 * @param string $mime   mime头类型
	 * @param string $name   文件名
	 * @param string $encode 编码
	 * @param string $disposition 附件展现方式
	 * @param string $cid 内容ID
	 * @return string
	 */
	public function getAttachHeader($mime,$name,$encode = self::ENCODE_BASE64,$disposition = 'attachment',$cid = 0){
		$attachHeader  = sprintf(self::CONTENTTYPE.": %s; name=\"%s\"%s", $mime, $name, self::CRLF);
        $attachHeader .= sprintf(self::CONTENTENCODE.": %s%s", $encode, self::CRLF);
		if($disposition == 'inline') {
       		 $attachHeader .= sprintf(self::CONTENTID.": <%s>%s", $cid, self::CRLF);
      	}
      	return $attachHeader .= sprintf(self::CONTENTPOSITION.": %s; filename=\"%s\"%s%s", $disposition, $name, self::CRLF,self::CRLF);
	}
	
	/**
	 * 获到文件中的内容
	 * @param string $file 文件名
	 * @param string $encode 编码方式
	 * @return string
	 */
	public function getStreamFromFile ($file, $encode = self::ENCODE_BASE64) {
		    $fp = fopen($file, 'rb');
		    $magic_quotes = get_magic_quotes_runtime();
		    set_magic_quotes_runtime(0);
		    $steam = fread($fp,filesize($file));
		    fclose($fp);
		    set_magic_quotes_runtime($magic_quotes);
		    return $steam;
	}
	
	/**
	 * 上传附件
	 * @return string
	 */
	public function attach() {
			$attach = '';
	    	foreach($this->attachment as $key => $value){
	    		list($stream,$mime,$disposition,$encode,$filename,$cid) = $value;
	    		$filename = $filename ? $filename : (is_file($stream) ? $stream : 'attachment_'.$key);
	    		$attachHeader = $this->getAttachHeader($mime,$filename,$encode,$disposition,$cid);
	    		$boundary = $this->boundaryLine();
	    		if(is_file($stream)){
	    			$stream = $this->getStreamFromFile($stream);
	    		}
	    		$attach .= $boundary.$attachHeader.$this->encode($stream,$encode).self::CRLF;
	    	}
	    	return $attach;
	}
	
	/**
	 * 过滤mime头
	 * @param string $header
	 * @return string
	 */
	public function filterHeader($header) {
		return strtr(trim($header),array("\r"=>'',"\n"=>'',"\r\n"=>''));
	}
	
	/**
	 * 获取边界线
	 * @return string
	 */
	public function boundaryLine() {
		return self::CRLF . '--' . $this->getBoundary() . self::CRLF;
	}
	
	/**
	 * 获取结束边界线
	 * @return string
	 */
	public function boundaryEndLine() {
		return self::CRLF . '--' . $this->getBoundary() . '--' . self::CRLF;
	}
	
	/**
     * 对字符进行编码
     * @param string $string 要编码的字符串
     * @param string $encode 编码的方式
     * @return string 
     */
	public function encode($string,$encode = self::ENCODE_BASE64){
		 switch($encode) {
		      case self::ENCODE_BASE64: return self::encodeBase64($string);
		      case self::ENCODE_7BIT:
		      case self::ENCODE_8BIT:
		      case self::ENCODE_BINARY:return $string;
		      case self::ENCODE_QP:return self::EncodeQP($string);
		      default:return $string;
	    }
	    return $string.self::CRLF;
	}
	
	 /**
     * 对mime头进行编码
     * @param string $string 要编码的字符串
     * @param string $encode 编码的方式
     * @return string 
     */
	public function encodeHeader($header,$encode=self::ENCODE_BASE64){
		switch($encode) {
		      case self::ENCODE_BASE64: return self::encodeBase64Header($this->filterHeader($header),$this->charSet);
		      case self::ENCODE_7BIT:
		      case self::ENCODE_8BIT:
		      case self::ENCODE_BINARY:return $header;
		      case self::ENCODE_QP:return self::encodeQpHeader($this->filterHeader($header),$this->charSet);
		      default:return $header;
	    }
	    return $header;
	}
	 /**
     * 对mime头进行quoted-printable编码
     * @param string $string 要编码的字符串
     * @param string $chunklen 分割字符串中每块的长度
     * @param string $end 每个字符块后面的填充符
     * @return string 
     */
    public static function encodeQpHeader($string, $charset,$chunkLen = self::LINELENGTH,$end = self::CRLF){
        $prefix = sprintf('=?%s?Q?', $charset);
        $chunkLen = $chunkLen-strlen($prefix)-3;
        $string = self::_encodeQp($string);
		$string = strtr($string,array('_'=>'5F',' '=>'=20','?'=>'=3F'));
        $lines[] = $tmp = '';
        while(strlen($string) > 0) {
            $culLen = max(count($lines)-1, 0);
            $token       = self::getNextQpToken($string);
            $string         = substr($string, strlen($token));
            $tmp .= $token;
            if('=20' == $token) {
                if(strlen($lines[$culLen].$tmp) > $chunkLen) {
                    $lines[$culLen+1] = $tmp;
                } else {
                    $lines[$culLen] .= $tmp;
                }
                $tmp = '';
            }
            if(0 == strlen($string)) {
                $lines[$culLen] .= $tmp;
            }
        }
        for($i = 0; $i < count($lines); $i++) {
            $lines[$i] = ' '.$prefix.$lines[$i].'?=';
        }
        return trim(implode($end, $lines));
    }
     /**
     * 对mime头进行base64编码
     * @param string $string 要编码的字符串
     * @param string $chunklen 分割字符串中每块的长度
     * @param string $end 每个字符块后面的填充符
     * @return string 
     */
	public static function encodeBase64Header($string,$charset, $chunkLen = self::LINELENGTH,$end = self::CRLF){
        $prefix = '=?' . $charset . '?B?';
        $suffix = '?=';
        $length = $chunkLen - strlen($prefix) - strlen($suffix);
        $encodedString = self::encodeBase64($string, $length, $end);
        $encodedString = $prefix . strtr($encodedString,array($end=>$suffix . $end . ' ' . $prefix)) . $suffix;
        return $encodedString;
    }
 	 /**
     * quoted-printable编码
     * @param string $string 要编码的字符串
     * @param string $chunklen 分割字符串中每块的长度
     * @param string $end 每个字符块后面的填充符
     * @return string 
     */
 	public static function encodeQp($string, $chunklen = self::LINELENGTH,$end = self::CRLF){
        $endodeString = '';
        $string = self::_encodeQp($string);
        while ($string) {
            $plen = $chunklen < ($plen = strlen($string)) ? $chunklen : $plen;
            if (false !== ($pos = strrpos(substr($string, 0, $plen), '='))  && $pos >= $plen - 2) {
                $plen = $pos;
            }
            0 < $plen && ' ' == $string[$plen - 1] &&  --$plen;
            $endodeString .= substr($string, 0, $plen) . '=' . $end;
            $string = substr($string, $plen);
        }
        $endodeString = rtrim($endodeString, $end);
        $endodeString = rtrim($endodeString, '=');
        return $endodeString;
    }
    /**
     * base64编码
     * @param string $string 要编码的字符串
     * @param string $chunklen 分割字符串中每块的长度
     * @param string $end 每个字符块后面的填充符
     * @return string quoted-printable
     */
    public static function encodeBase64($string,$chunklen = self::LINELENGTH,$end = self::CRLF){
		return chunk_split(base64_encode($string), $chunklen, $end);
	}
	/**
	 * quoted-printable 对照表
	 * @return string 
	 */
	public static function getQpTable(){
		return array( 
			"\x00"=>"=00","\x01"=>"=01","\x02"=>"=02","\x03"=>"=03",
			"\x04"=>"=04","\x05"=>"=05","\x06"=>"=06","\x07"=>"=07",
	        "\x08"=>"=08","\x09"=>"=09","\x0A"=>"=0A","\x0B"=>"=0B",
	        "\x0C"=>"=0C","\x0D"=>"=0D","\x0E"=>"=0E","\x0F"=>"=0F",
	        "\x10"=>"=10","\x11"=>"=11","\x12"=>"=12","\x13"=>"=13",
	        "\x14"=>"=14","\x15"=>"=15","\x16"=>"=16","\x17"=>"=17",
	        "\x18"=>"=18","\x19"=>"=19","\x1A"=>"=1A","\x1B"=>"=1B",
	        "\x1C"=>"=1C","\x1D"=>"=1D","\x1E"=>"=1E","\x1F"=>"=1F",
	        "\x7F"=>"=7F","\x80"=>"=80","\x81"=>"=81","\x82"=>"=82",
	        "\x83"=>"=83","\x84"=>"=84","\x85"=>"=85","\x86"=>"=86",
	        "\x87"=>"=87","\x88"=>"=88","\x89"=>"=89","\x8A"=>"=8A",
	        "\x8B"=>"=8B","\x8C"=>"=8C","\x8D"=>"=8D","\x8E"=>"=8E",
	        "\x8F"=>"=8F","\x90"=>"=90","\x91"=>"=91","\x92"=>"=92",
	        "\x93"=>"=93","\x94"=>"=94","\x95"=>"=95","\x96"=>"=96",
	        "\x97"=>"=97","\x98"=>"=98","\x99"=>"=99","\x9A"=>"=9A",
	        "\x9B"=>"=9B","\x9C"=>"=9C","\x9D"=>"=9D","\x9E"=>"=9E",
	        "\x9F"=>"=9F","\xA0"=>"=A0","\xA1"=>"=A1","\xA2"=>"=A2",
	        "\xA3"=>"=A3","\xA4"=>"=A4","\xA5"=>"=A5","\xA6"=>"=A6",
	        "\xA7"=>"=A7","\xA8"=>"=A8","\xA9"=>"=A9","\xAA"=>"=AA",
	        "\xAB"=>"=AB","\xAC"=>"=AC","\xAD"=>"=AD","\xAE"=>"=AE",
	        "\xAF"=>"=AF","\xB0"=>"=B0","\xB1"=>"=B1","\xB2"=>"=B2",
	        "\xB3"=>"=B3","\xB4"=>"=B4","\xB5"=>"=B5","\xB6"=>"=B6",
	        "\xB7"=>"=B7","\xB8"=>"=B8","\xB9"=>"=B9","\xBA"=>"=BA",
	        "\xBB"=>"=BB","\xBC"=>"=BC","\xBD"=>"=BD","\xBE"=>"=BE",
	        "\xBF"=>"=BF","\xC0"=>"=C0","\xC1"=>"=C1","\xC2"=>"=C2",
	        "\xC3"=>"=C3","\xC4"=>"=C4","\xC5"=>"=C5","\xC6"=>"=C6",
	        "\xC7"=>"=C7","\xC8"=>"=C8","\xC9"=>"=C9","\xCA"=>"=CA",
	        "\xCB"=>"=CB","\xCC"=>"=CC","\xCD"=>"=CD","\xCE"=>"=CE",
	        "\xCF"=>"=CF","\xD0"=>"=D0","\xD1"=>"=D1","\xD2"=>"=D2",
	        "\xD3"=>"=D3","\xD4"=>"=D4","\xD5"=>"=D5","\xD6"=>"=D6",
	        "\xD7"=>"=D7","\xD8"=>"=D8","\xD9"=>"=D9","\xDA"=>"=DA",
	        "\xDB"=>"=DB","\xDC"=>"=DC","\xDD"=>"=DD","\xDE"=>"=DE",
	        "\xDF"=>"=DF","\xE0"=>"=E0","\xE1"=>"=E1","\xE2"=>"=E2",
	        "\xE3"=>"=E3","\xE4"=>"=E4","\xE5"=>"=E5","\xE6"=>"=E6",
	        "\xE7"=>"=E7","\xE8"=>"=E8","\xE9"=>"=E9","\xEA"=>"=EA",
	        "\xEB"=>"=EB","\xEC"=>"=EC","\xED"=>"=ED","\xEE"=>"=EE",
	        "\xEF"=>"=EF","\xF0"=>"=F0","\xF1"=>"=F1","\xF2"=>"=F2",
	        "\xF3"=>"=F3","\xF4"=>"=F4","\xF5"=>"=F5","\xF6"=>"=F6",
	        "\xF7"=>"=F7","\xF8"=>"=F8","\xF9"=>"=F9","\xFA"=>"=FA",
	        "\xFB"=>"=FB","\xFC"=>"=FC","\xFD"=>"=FD","\xFE"=>"=FE",
	        "\xFF"=>"=FF"
		);
	}
  	/**
  	 * 在quoted-printable编码中过滤等号
  	 * @param string $string 取得编码的字符串
  	 * @return string
  	 */
  	public static function _encodeQp($string){
        $string = strtr($string,array('='=>'=3D'));
        return strtr($string,self::getQpTable());
    }
	/**
	 * 生成mime邮件头的消息ID
	 * @return string
	 */
	public function createMessageId() {
		if (array() != ($from = $this->getFrom())) {
			$user = is_array($from) ? $from[0] : $from;
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$user = $_SERVER['REMOTE_ADDR'];
		} else {
			$user = getmypid();
		}
		$rand = mt_rand();
		if ($this->recipients) {
			$recipient = array_rand($this->recipients);
		} else {
			$recipient = 'No recipient';
		}
		if (isset($_SERVER["SERVER_NAME"])) {
			$host = $_SERVER["SERVER_NAME"];
		} else {
			$host = php_uname('n');
		}
		return sha1(time() . $user . $rand . $recipient) . '@' . $host;
	}
	
	/**
	 * 清空邮件头
	 * @param string $header
	 */
	public function clearMailHeader($header = null){
		if($header){
			if(isset($this->mailHeader[$header])){
			 unset($this->mailHeader[$header]);
			}
		}else{
			$this->mailHeader = array();
		}
	}
	/**
	 * 清空附件
	 */
	public function clearAttachment(){
		$this->attachment = array();
	}
	
	/**
	 * 清空收件人
	 */
	public function clearRecipients(){
		$this->recipients = array();
	}
	
	/**
	 * 清空边界线
	 */
	public function clearBoundary(){
		$this->boundary = '';
	}
	
	/**
	 * 清空html格式的邮件正文
	 */
	public function clearBodyHtml(){
		$this->bodyHtml = '';
	}
	
	/**
	 *  清空text格式的邮件正文
	 */
	public function clearBodyText(){
		$this->bodyText = '';
	}
	
	/**
	 * 清空邮件头、附件、收件人、边界线、html和text格式的邮件正文;
	 */
	public function clearAll(){
		$this->clearMailHeader();
		$this->clearRecipients();
		$this->clearAttachment();
		$this->clearBoundary();
		$this->clearBodyHtml();
		$this->clearBodyText();
	}
	/**
	 * 构建收件人列表
	 * @param string $data
	 * @return array();
	 */
	private function buildRecipients($data = array()) {
		if(empty($data) || !is_array($data)){
			return array();
		}
		foreach ($data as $key => $value) {
			if (is_string($key)) {
				$this->recipients[] = $value.' '.$key;
			} else {
				$this->recipients[] = $value;
			}
		}
		return $this->recipients;
	}
	
	/**
	 * 生成mime邮件头
	 * @param string $name mime头
	 * @param string $value mime值
	 * @return string
	 */
	private function headerLine($name,$value){
		if(is_array($value)){
			$tmp = '';
			foreach($value as $key=>$_value){
				$_value = is_string($key) ? $key.' '.$_value : $_value;
				$tmp .= $tmp ? ','.$_value : $_value;
			}
			return $name.': '.$tmp.self::CRLF;
			
		}else{
			return $name .': '.$value.self::CRLF;
		}
		return '';
	}
	
	/**
	 * 判断邮件是否有附件
	 * @return boolean
	 */
	private function hasAttachment(){
    	return count($this->attachment) > 0;
    }
    
	/**
	 * 取得下一个quoted-printable
	 * @param string $string  
	 * @return string
	 */
	private static function getNextQpToken($string){
        return '=' == substr($string, 0, 1) ? substr($string, 0, 3) : substr($string, 0, 1);
    }
}