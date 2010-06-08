<?php


	/**
	 * class to communicate with google mail via IMAP
	 * @author gep
	 *
	 */
 class GmailReader  
    {  
    public $mbox;  
     
    public function __construct( $user, $pass )  
    {  
     $this->mbox = imap_open("{imap.gmail.com:993/imap/ssl}INBOX",$user,$pass)  
      or die("can't connect: " . imap_last_error());  
    }  
     
   public function openSentMail()  
   {  
   imap_reopen($this->mbox, "{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail" )  
     or die("Failed to open Sent Mail: " . imap_last_error());  
   }

   public function openSpamEmail(){
   	imap_reopen($this->mbox, "{imap.gmail.com:993/imap/ssl}[Gmail]/Spam" )  
     or die("Failed to open Spam Mail: " . imap_last_error()); 
   }
   
   
   public function openInboxEmail(){
   	imap_reopen($this->mbox, "{imap.gmail.com:993/imap/ssl}INBOX" )  
     or die("Failed to open Spam Mail: " . imap_last_error()); 
   }
     
   public function openMailBox($mailbox)  
   {  
    imap_reopen($this->mbox, "{imap.gmail.com:993/imap/ssl}$mailbox" )  
     or die("Failed to open $mailbox: " . imap_last_error());  
   }  
     
   public function getMailboxInfo()  
   {  
    $mc = imap_check($this->mbox);  
     return $mc;  
    }  
      
    /** 
     * $date should be a string 
     * Example Formats Include: 
     * Fri, 5 Sep 2008 9:00:00 
     * Fri, 5 Sep 2008 
     * 5 Sep 2008 
     * I am sure other's work, just test them out. 
     */  
    public function getHeadersSince($date)  
    {  
     $uids = $this->getMessageIdsSinceDate($date);  
     $messages = array();  
     foreach( $uids as $k=>$uid )  
     {  
      $messages[] = $this->retrieve_header($uid);  
     }  
     return $messages;  
    }  
      
    /** 
     * $date should be a string 
     * Example Formats Include: 
     * Fri, 5 Sep 2008 9:00:00 
     * Fri, 5 Sep 2008 
     * 5 Sep 2008 
     * I am sure other's work, just test them out. 
     */  
    public function getEmailSince($date)  
    {  
     $uids = $this->getMessageIdsSinceDate($date);  
     $messages = array();  
     foreach( $uids as $k=>$uid )  
     {  
      $messages[] = $this->retrieve_message($uid);
      echo 'Proceeded email: '.$k. "<br /> \n"; flush();  
     }  
     return $messages;  
    }

    
      
    public function getMessageIdsSinceDate($date)  
    {  
     return imap_search( $this->mbox, 'SINCE "'.$date.'"'); 
    } 
     
    public function retrieve_header($messageid) 
    { 
       $message = array(); 
     
       $header = imap_header($this->mbox, $messageid); 
       $structure = imap_fetchstructure($this->mbox, $messageid); 
     
       $message['subject'] = $header->subject; 
       $message['fromaddress'] =   $header->fromaddress; 
       $message['toaddress'] =   $header->toaddress;  
//       $message['ccaddress'] =   $header->ccaddress; 
       $message['date'] =   $header->date; 
     
       return $message; 
    } 
     
    public function retrieve_message($messageid) 
    { 
       $message = array(); 
     
       $header = imap_header($this->mbox, $messageid); 
       $structure = imap_fetchstructure($this->mbox, $messageid); 
       
//       var_dump($structure); 
       
     
       $message['subject'] = $header->subject;  
       $message['fromaddress'] =   $header->fromaddress; 
       $message['toaddress'] =   $header->toaddress;  
//       $message['ccaddress'] =   $header->ccaddress; 
       $message['date'] =   $header->date; 
    
     if ($this->check_type($structure)) 
     {
//		$text = str_replace('&', '+', imap_fetchbody($this->mbox,$messageid,"1"));
//		$text  = str_replace(',', '/', $text);
//      $text = imap_fetchbody($this->mbox,$messageid,"2");
//      $enc = mb_detect_encoding($text);
//      echo $enc.'<br />';
//      $message['body'] = iconv('KOI8-R', 'UTF-8', $text); ## GET THE BODY OF MULTI-PART MESSAGE
		if(is_array($structure->parts) && is_array($structure->parts[0]->parameters) && $structure->parts[0]->parameters[0]->attribute == 'CHARSET' && in_array(strtolower($structure->parts[0]->parameters[0]->value), array('utf-8', 'windows-1251'))){
			$message['body'] = base64_decode(imap_fetchbody($this->mbox,$messageid,"1"));
		}else{
			
			$message['body'] = imap_fetchbody($this->mbox,$messageid,"1");
		}
      if(!$message['body']) {$message['body'] = '';} 
     } 
     else 
     { 
//      $message['body'] = iconv('KOI8-R', 'UTF-8', imap_body($this->mbox, $messageid));
		$message['body'] = imap_body($this->mbox, $messageid);
      if(!$message['body']) {$message['body'] = '';}  
     }  
     
     return $message;  
   }  
     
   public function check_type($structure) ## CHECK THE TYPE  
   {  
     if($structure->type == 1)  
       {  
        return(true); ## YES THIS IS A MULTI-PART MESSAGE  
       }  
    else  
       {  
        return(false); ## NO THIS IS NOT A MULTI-PART MESSAGE  
       }  
   }  
     
   }  