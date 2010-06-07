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
  	set_time_limit(0);
	ini_set('memory_limit','64M');
  	
  	$trainer = new trainer();

	
	/* loading previus learn */
	echo "<h1>Loading previous learn</h1>";flush();
	
//	$query = mysql_query("select belongs,ngram,repite from knowledge_base",$db);
	
	$previouslearn = array();
	
	foreach (Doctrine::getTable('KnowledgeBase')->createQuery('kb')->fetchArray() as $item){
	    $previouslearn[$item['belongs']][$item['ngram']] = $item['repite'];
	}
	$trainer->setPreviousLearn($previouslearn);
	
	/* traine */
	echo "<h1>Training</h1>";flush();
//	$query = mysql_query("select * from examples",$db);
//	$sql=mysql_query("select comment_content as text,comment_approved as state from wp_comments",$db);
	echo "<h2>Loading examples</h2>";flush();
	foreach (Doctrine::getTable('Example')->createQuery('e')->fetchArray() as $item){
	    $text = $item['content'];
	    $text = strip_tags($text);
	    $trainer->add_example($text,$item['state']);
	}

	
	/* learn */
	echo "<h2>Learning</h2>";flush();
	$trainer->extractPatterns();
	
	/* save what is learned */
	echo "<h1>Saving learning</h1>";flush();	
//	sfConfig::set('sf_debug', false);

    $conn = Doctrine_manager::getInstance()->getCurrentConnection();
    $handle = $conn->getDBh();
    
	foreach ($trainer->getKnowledge() as $tipo => $v) {
	    foreach($v as $k => $y) {
//	        $q = Doctrine::getTable('KnowledgeBase')->createQuery('kb')
//	        								   ->delete()
//	        								   ->where('kb.ngram = ? AND kb.belongs = ?', array($k, $tipo));
//	        $q->execute();
//	        $q->free();
			
	        $handle->exec("INSERT INTO knowledge_base(ngram, belongs, repite, percent, created_at) VALUES ('".$k."', '".$tipo."', ".$y['cant'].", ".$y['bayesian'].", '".date('Y-m-d H:i:s')."')");
	        
//	        $knowledgeBase = (!isset($knowledgeBase) ? new KnowledgeBase() : $knowledgeBase);
//	        $knowledgeBase = new KnowledgeBase();

//	        $knowledgeBaseForm = new KnowledgeBaseForm($knowledgeBase, null, false);
//	        var_dump($k, $tipo, $y); exit;
//			$knowledgeBase->fromArray(array('ngram' => $k,
//	        							   'belongs' => $tipo, 
//	        							   'repite' => $y['cant'],
//	        							   'percent' => $y['bayesian'],
//	        							   'created_at' => date('Y-m-d H:i:s')));
//			$knowledgeBase->save();

//	        $knowledgeBaseForm->bind(array('ngram' => $k,
//	        							   'belongs' => $tipo, 
//	        							   'repite' => $y['cant'],
//	        							   'percent' => $y['bayesian'],
//	        							   'created_at' => date('Y-m-d H:i:s')));
//	        $knowledgeBaseForm->save();
//	        $knowledgeBase->free();
//	        
//	        unset($q);

	        
	        
	        
//	        $sql = "replace knowledge_base values('$k','$tipo','".$y['cant']."','".$y['bayesian']."')";
//	        mysql_query($sql,$db) or die(mysql_error($db).":".$sql);
	    }
	}
	echo "<h1>Optimizing database</h1>";flush();
	
//	mysql_query("create temporary table opttable as 
//	select ngram, count(*) total, min(percent) as nmin, max(percent) as nmax
//	from knowledge_base group by ngram having count(ngram) > 1",$db);
//	
//	mysql_query("delete from knowledge_base where ngram in (select ngram from opttable where (nmax-nmin) < 0.30)",$db);
	
	return sfView::NONE;
  }
  
  
  
  public function executeCheckMessage(sfWebRequest $request){
	$spam = new spam();
	/**/
	$texts = array("Phentermine", "Buy cheap xxx","Really nice post","Viagra","This a large text, it is not spam, but because the training set are small sentenses, it may be marked as spam. You can solve this problem with a largest sentences on the training set.");
	echo "<h1>Spam test</h1>";
	foreach ($texts as $text)
	    echo "<em><strong>$text</strong></em> has an accuraccy of <b>". $spam->isItSpam_v2($text,'spam')."%</b> spam<hr>";
	echo "<h1>Ham test</h1>";
	foreach ($texts as $text)
	    echo "<em><strong>$text</strong></em> has an accuraccy of <b>". $spam->isItSpam_v2($text,'1')."%</b> ham<hr>";;
	
	return sfView::NONE;
  }
  
  
  
  public function executeGetGMailEmail(sfWebRequest $request){
  	$gmailReader = new GmailReader(sfConfig::get('app_gmail_user_name'), sfConfig::get('app_gmail_user_pass'));
  }

}
