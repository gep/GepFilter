<?php

/**
 * ftsfilter actions.
 *
 * @package    gepfilter
 * @subpackage ftsfilter
 * @author     Andrew Semikov
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ftsfilterActions extends sfActions
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
  
  
  public function executeCheckMessage(sfWebRequest $request){
	$this->spam = new spam();
	/**/
	$this->texts = array("Polar night", 
						 "Phentermine", 
						 "Buy cheap xxx",
						 "Really nice post",
						 "Viagra", 
						 "Via");
//	echo "<h1>Тест проверки на спам</h1>";
//	foreach ($texts as $text)
//	    echo "<em><strong>$text</strong></em> вероятность <b>". $spam->isItSpam_v2($text,'spam')."%</b> spam<hr>";
//	echo "<h1>Тест проверки на не спам</h1>";
//	foreach ($texts as $text)
//	    echo "<em><strong>$text</strong></em> вероятность <b>". $spam->isItSpam_v2($text,'1')."%</b> not spam<hr>";;
//	
//	return sfView::NONE;
  }
  
  
}
