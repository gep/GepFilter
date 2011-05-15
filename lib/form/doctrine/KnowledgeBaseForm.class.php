<?php

/**
 * KnowledgeBase form.
 *
 * @package    gepfilter
 * @subpackage form
 * @author     Andrew Semikov
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class KnowledgeBaseForm extends BaseKnowledgeBaseForm
{
  public function configure()
  {
  	$this->disableCSRFProtection();
  }
  
  
  public function setup(){
  	parent::setup();
  	
  	$this->setValidator('ngram', new sfValidatorString(array('required' => true, 'max_length' => 10), array('required' => 'ngram is required', 'invalid' => 'ngram is invalid', 'max_length' => 'ngram is too big')));
  	$this->setValidator('belongs', new sfValidatorString(array('required' => true, 'max_length' => 10), array('required' => 'belongs is required', 'invalid' => 'belongs is invalid', 'max_length' => 'ngram is too big')));
  	
  	$this->disableCSRFProtection();
  }
}
