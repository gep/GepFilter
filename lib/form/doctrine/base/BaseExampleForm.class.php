<?php

/**
 * Example form base class.
 *
 * @method Example getObject() Returns the current form's model object
 *
 * @package    gepfilter
 * @subpackage form
 * @author     Andrew Semikov
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseExampleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'content'    => new sfWidgetFormTextarea(),
      'state'      => new sfWidgetFormChoice(array('choices' => array(0 => 0, 1 => 1, 'spam' => 'spam'))),
      'created_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'content'    => new sfValidatorString(array('required' => false)),
      'state'      => new sfValidatorChoice(array('choices' => array(0 => 0, 1 => 1, 2 => 'spam'), 'required' => false)),
      'created_at' => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('example[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Example';
  }

}
