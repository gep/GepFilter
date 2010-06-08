<?php



class GmailReaderDB extends GmailReader{
	
   /**
    * add message to DB
    * @param string $date
    * @param string $state
    */
   public function getEmailSinceAndAddToDB($date, $state){
      $uids = $this->getMessageIdsSinceDate($date);  
      $messages = array();
      $succ = 0;
      $errored = 0;  
      foreach( $uids as $k=>$uid )  
      {  
      	$message = $this->retrieve_message($uid);
      	$messages[] = $message;
      	try{
      		
	      	$example = new Example();
			$example->fromArray(array('content' => $message['body'],
										  'state' => $state));
			$example->save();
			$example->free();
//			echo $message['body']. '<br /><br /><br /><br /><br />'; flush();
	      	$succ++;
	      	echo 'Proceeded email: '.$k. "<br /> \n"; flush();
      	}catch (Exception $e){
      		$errored++;
      		echo 'Error on '.$k.' message: '.$e->getMessage(). "<br /> \n"; flush();
      	}
      	
      }
      
      echo '<br /><br /><br /><br /><br />Succeeded: '.$succ; flush();
      echo '<br /><br /><br /><br /><br />Errored: '.$errored; flush();  
    }
}