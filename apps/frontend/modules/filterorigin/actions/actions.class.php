<?php

/**
 * filterorigin actions.
 *
 * @package    gepfilter
 * @subpackage filterorigin
 * @author     Andrew Semikov
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class filteroriginActions extends sfActions
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
  
  
  public function executeInitTrain(sfWebRequest $request){
  	$trainer = new trainerOrigin();
	
	
	/* loading previus learn */
	echo "<h1>Loading previous learn</h1>";flush();

	$previouslearn = array();
	
	foreach (Doctrine::getTable('KnowledgeBase')->createQuery('kb')
												->fetchArray()
												 as $item){
	    $previouslearn[$item['belongs']][$item['ngram']] = $item['repite'];
	}
	$trainer->setPreviousLearn($previouslearn);
	
	
	/* traine */
	echo "<h1>Training</h1>";flush();
  	foreach (Doctrine::getTable('Example')->createQuery('e')
//										  ->where('e.created_at < ?', '2010-06-07 01:00:00')
										  ->where('e.id <= ?', 656)
										  ->fetchArray() as $item){
	    $text = $item['content'];
	    $text = strip_tags($text);
	    $trainer->add_example($text,$item['state']);
	}
	echo "<h2>Loading examples</h2>";flush();
	
	/* learn */
	echo "<h2>Learning</h2>";flush();
	$trainer->extractPatterns();
	
	/* save what is learned */
	echo "<h1>Saving learning</h1>";flush();
	
	$conn = Doctrine_manager::getInstance()->getCurrentConnection();
    $handle = $conn->getDBh();
	
	
	foreach ($trainer->knowledge as $tipo => $v) {
		$date = date('Y-m-d H:i:s');
	    foreach($v as $k => $y) {
	        $stmt2 = $handle->prepare("DELETE FROM knowledge_base WHERE ngram = :ngrm AND belongs = :bel");
	    	$stmt2->bindParam(':ngrm', $k, PDO::PARAM_STR, 12);
	    	$stmt2->bindParam(':bel', $tipo, PDO::PARAM_STR, 12);
	    	$stmt2->execute();
	    	

	    	
	    	
			$stmt = $handle->prepare("INSERT INTO knowledge_base(ngram, belongs, repite, percent, created_at) VALUES (:ngrm, :bel, :rep, :perc, :created)");
			$stmt->bindParam(':ngrm', $k, PDO::PARAM_STR, 12);
			$stmt->bindParam(':bel', $tipo, PDO::PARAM_STR, 12);
			$stmt->bindParam(':rep', $y['cant'], PDO::PARAM_INT, 11);
			$stmt->bindParam(':perc', $y['bayesian'], PDO::PARAM_STR, 40);
			$stmt->bindParam(':created', $date, PDO::PARAM_STR, 30);
			$stmt->execute();
	        
	        
//	        $sql = "replace knowledge_base values('$k','$tipo','".$y['cant']."','".$y['bayesian']."')";
//	        mysql_query($sql,$db) or die(mysql_error($db).":".$sql);
	    }
	}
	echo "<h1>Optimizing database</h1>";flush();
	
	exit;
  }
  
  
  
  
  public function executeCheckIsSpam(sfWebRequest $request){
  	
  	$spam = new spamOrigin();
	/**/
	$texts = array("Phentermine", 
				   "Buy cheap xxx",
				   "Really nice post",
				   "Viagra", 
				   "cialis", 
				   "Paris Hilton", 
				   "Diploma");
	echo "<h1>Spam test</h1>";
	foreach ($texts as $text)
	    echo "<em><strong>$text</strong></em> has an accuraccy of <b>". $spam->isItSpam_v2($text,'spam')."%</b> spam<hr>";
	echo "<h1>Not Spam test</h1>";
	foreach ($texts as $text)
	    echo "<em><strong>$text</strong></em> has an accuraccy of <b>". $spam->isItSpam_v2($text,'1')."%</b> not spam<hr>";;
	    
	exit;
  }
  
}
