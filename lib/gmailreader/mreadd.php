<pre><?


// ��� ����������� � ������� $host="{imap.yandex.ru:143/imap/notls}"; $login="yandexuser"; $password="somepwd";
// ��� � gmail � ��� ����� � googleapps $host="{imap.gmail.com:993/imap/ssl}"; $login="test@test.ru"; $password="somepwd";
/* ������������� �� �������. $msg->mail ������������ ������  ����� ��������
  array(8) {
    ["from"]
    ["to"]
    ["name"]
    ["subject"] 
	// ��� ��������� ���� ��� ��������� � utf-8
    ["charset"] - � ���� ��������� ��������� 2 ��������� ����
    ["plain"]  - ��������� � ������� plaintext
    ["html"] - - ��������� � ������� HTML ,���� �� ��� ����� ���� [��� ��� ������� � ����] ����� ������ ������.
    ["attach"] - ������ � ����������� ���� '��������'=>���������� �����
    array(1) { 
		["someattachname.txt"]=> '�����-�� ��� ����������';
    }
  }
������ � IMAP: ��������� ������������� ����� � ����������� � ����������� ���������� � UTF-8 
��� �������: �������� ��������� � UTF8, � ['plain'] � ['html'] ���������� �������� �� ["charset"] � ����-���� �����. ����: $decoded=mb_convert_encoding($letter['html'],'UTF-8',$letter['charset'])
*/
//$host="{imap.gmail.com:993/imap/ssl}"; $login="test@test.ru"; $password="somepwd";
//$msg=new mread($host, $login, $password);
//var_dump($msg->mail);


class mread {
	private $mbox='';
	private $htmlmsg = '';
	private $plainmsg = '';
	private $charset = '';
	private $attachments = array();
	private $unread; 
	public function __get($name){
		if ($name=='mail') return $this->unread;
		else return null;
	}
	public function getmail(){ return $this->unread;} /* backwards compatibility for php4*/
	public function __construct($host, $login, $pwd) { /* backwards compatibility for php 4, __constructor*/
		$messages=array();
		$folder="[Gmail]/Sent Mail";
		//$folder="[Gmail]/&BCEEPwQwBDw-"; // ���� ��� ��������� �������� ���� �� ���������.
		$this->mbox = @imap_open ("{$host}{$folder}", $login,$pwd) or die(imap_last_error());
		$arr=imap_search  ($this->mbox, 'SINCE "03 Jun 2010 9:00:00"'); 
		if ($arr !== false) {
			foreach ($arr as $i){
				$headerArr = imap_headerinfo ( $this->mbox, $i);
				$mailArr =
				 array(
				  'sender' => $headerArr->sender[0]->mailbox . "@" . $headerArr->sender[0]->host,
				  'to' => $headerArr->to[0]->mailbox . "@" . $headerArr->to[0]->host,
				  'date' => $headerArr->date,
				  'size' => $headerArr->Size,
				  'subject' => $headerArr->subject,
				 );
				$this->getmsg($i);
				imap_setflag_full($this->mbox, $i, "\\Seen");
				//var_dump($this->htmlmsg,$this->plainmsg,$this->charset,$this->attachments);
				$messages[]=array('from'=> $mailArr['sender'],'to'=> $mailArr['to'],'name'=> $this->decode($headerArr->sender[0]->personal) ,'subject'=>$this->decode($mailArr->subject), 
									'charset'=>$this->charset,'plain'=>$this->plainmsg,'html'=>$this->htmlmsg,'attach'=>$this->attachments);
		}
			$this->unread=$messages;
			unset($messages);
		}
		else {$this->unread=false;}
		imap_close($this->mbox);
	}

	private function decode($enc){
		$parts = imap_mime_header_decode($enc);
		$str='';
		for ($p=0; $p<count($parts); $p++) {
			$ch=$parts[$p]->charset;
			$part=$parts[$p]->text;
			if ($ch!=='default') $str.=mb_convert_encoding($part,'UTF-8',$ch);
							else $str.=$part;
		}
		return $str;
	}

	private function getmsg($mid) {
		$this->htmlmsg = $this->plainmsg = $this->charset = '';
		$this->attachments = array();

		$s = imap_fetchstructure($this->mbox,$mid);
		if (!$s->parts) $this->getpart($mid,$s,0); 
		else {
			foreach ($s->parts as $partno0=>$p)
				$this->getpart($mid,$p,$partno0+1);
		}
	}

	private function getpart($mid,$p,$partno) {
		$data = ($partno)? imap_fetchbody($this->mbox,$mid,$partno): imap_body($this->mbox,$mid); 
		if ($p->encoding==4)
			$data = quoted_printable_decode($data);
		elseif ($p->encoding==3)
			$data = base64_decode($data);

		$params = array();
		if ($p->parameters)
			foreach ($p->parameters as $x)
				$params[ strtolower( $x->attribute ) ] = $x->value;
		if ($p->dparameters)
			foreach ($p->dparameters as $x)
				$params[ strtolower( $x->attribute ) ] = $x->value;

		if ($params['filename'] || $params['name']) {
			$filename = ($params['filename'])? $params['filename'] : $params['name'];
			$this->attachments[$filename] = $data;  // ���� 2 ����� � ����� ������ - ��� ���. TODO
		}
		elseif ($p->type==0 && $data) {
			if (strtolower($p->subtype)=='plain')
				$this->plainmsg .= trim($data) ."\n\n";
			else
				$this->htmlmsg .= $data ."<br><br>";
			$this->charset = $params['charset']; 
		}
		elseif ($p->type==2 && $data) {
			$this->plainmsg .= trim($data) ."\n\n";
		}

		if ($p->parts) {
			foreach ($p->parts as $partno0=>$p2)
				$this->getpart($mid,$p2,$partno.'.'.($partno0+1)); 
		}
	}
}
?></pre>
