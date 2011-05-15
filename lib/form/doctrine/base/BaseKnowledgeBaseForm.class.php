<?php

/**
 * KnowledgeBase form base class.
 *
 * @method KnowledgeBase getObject() Returns the current form's model object
 *
 * @package    gepfilter
 * @subpackage form
 * @author     Andrew Semikov
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseKnowledgeBaseForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'ngram'      => new sfWidgetFormInputHidden(),
      'belongs'    => new sfWidgetFormInputHidden(),
      'repite'     => new sfWidgetFormInputText(),
      'percent'    => new sfWidgetFormInputText(),
      'created_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'ngram'      => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'ngram', 'required' => false)),
      'belongs'    => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'belongs', 'required' => false)),
      'repite'     => new sfValidatorInteger(),
      'percent'    => new sfValidatorNumber(),
      'created_at' => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('knowledge_base[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'KnowledgeBase';
  }

}
