<?php

/**
 * filterchain actions.
 *
 * @package    gepfilter
 * @subpackage filterchain
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class filterchainActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('default', 'module');
  }
  
  /**
   * init trainig
   * @param sfWebRequest $request
   */
  public function executeInitialTrain(sfWebRequest $request){
  	
  	$trainer = new trainer();

	
	/* loading previus learn */
	echo "<h1>Loading previous learn</h1>";flush();
	
	
	$previouslearn = array();
	
	foreach (Doctrine::getTable('KnowledgeBase')->createQuery('kb')
												->fetchArray()
												 as $item){
	    $previouslearn[$item['belongs']][] = array('ngram' => $item['ngram'], 'weight' => $item['repite']);
	}
	$trainer->setPreviousLearn($previouslearn);
	
	/* traine */
	echo "<h1>Training</h1>";flush();

	echo "<h2>Loading examples</h2>";flush();

	foreach (Doctrine::getTable('Example')->createQuery('e')
//										  ->where('e.created_at < ?', '2010-06-07 01:00:00')
										  ->where('e.id <= ?', 656)
										  ->fetchArray() as $item){
	    $text = $item['content'];
	    $text = strip_tags($text);
	    $trainer->add_example($text,$item['state']);
	}


	
	/* learn */
	echo "<h2>Learning</h2>";flush();
	$trainer->extractPatterns();
	
	/* save what is learned */
	echo "<h1>Saving learning</h1>";flush();	

    $conn = Doctrine_manager::getInstance()->getCurrentConnection();
    $handle = $conn->getDBh();
    

	foreach ($trainer->getKnowledge() as $tipo => $v) {
		$date = date('Y-m-d H:i:s');
	    foreach($v as $k => $y) {
	    	$stmt2 = $handle->prepare("DELETE FROM knowledge_base WHERE ngram = :ngrm AND belongs = :bel");
	    	$stmt2->bindParam(':ngrm', $y['ngram'], PDO::PARAM_STR, 12);
	    	$stmt2->bindParam(':bel', $tipo, PDO::PARAM_STR, 12);
	    	$stmt2->execute();
	    	

	    	
	    	
			$stmt = $handle->prepare("INSERT INTO knowledge_base(ngram, belongs, repite, percent, created_at) VALUES (:ngrm, :bel, :rep, :perc, :created)");
			$stmt->bindParam(':ngrm', $y['ngram'], PDO::PARAM_STR, 12);
			$stmt->bindParam(':bel', $tipo, PDO::PARAM_STR, 12);
			$stmt->bindParam(':rep', $y['cant'], PDO::PARAM_INT, 11);
			$stmt->bindParam(':perc', $y['bayesian'], PDO::PARAM_STR, 40);
			$stmt->bindParam(':created', $date, PDO::PARAM_STR, 30);
			$stmt->execute();
	    	
//	        $handle->exec("INSERT INTO knowledge_base(ngram, belongs, repite, percent, created_at) VALUES ('".$k."', '".$tipo."', ".$y['cant'].", ".$y['bayesian'].", '".date('Y-m-d H:i:s')."')");
       
	        
	    }
	}
	echo "<h1>Optimizing database</h1>";flush();
	

	return sfView::NONE;
  }
  
  
  
  public function executeCheckMessage(sfWebRequest $request){
	$spam = new spam();
	/**/
	$texts = array("Phentermine", "Buy cheap xxx","Really nice post","Viagra",);
	echo "<h1>Тест проверки на спам</h1>";
	foreach ($texts as $text)
	    echo "<em><strong>$text</strong></em> вероятность <b>". $spam->isItSpam_v2($text,'spam')."%</b> spam<hr>";
	echo "<h1>Тест проверки на не спам</h1>";
	foreach ($texts as $text)
	    echo "<em><strong>$text</strong></em> вероятность <b>". $spam->isItSpam_v2($text,'1')."%</b> not spam<hr>";;
	
	return sfView::NONE;
  }
  
  
  
  public function executeGetGMailEmail(sfWebRequest $request){
  	
  	$gmailReader = new GmailReaderDB(sfConfig::get('app_gmail_user_name'), sfConfig::get('app_gmail_user_pass'));
  	
  	$gmailReader->openInboxEmail();
//  	$gmailReader->openSpamEmail();
  	$gmailReader->getEmailSinceAndAddToDB('1 Sep 2009 9:00:00', '1');

	return sfView::NONE;
  }

}
