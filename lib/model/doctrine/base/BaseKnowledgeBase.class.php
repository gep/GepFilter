<?php

/**
 * BaseKnowledgeBase
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $ngram
 * @property string $belongs
 * @property integer $repite
 * @property float $percent
 * 
 * @method string        getNgram()   Returns the current record's "ngram" value
 * @method string        getBelongs() Returns the current record's "belongs" value
 * @method integer       getRepite()  Returns the current record's "repite" value
 * @method float         getPercent() Returns the current record's "percent" value
 * @method KnowledgeBase setNgram()   Sets the current record's "ngram" value
 * @method KnowledgeBase setBelongs() Sets the current record's "belongs" value
 * @method KnowledgeBase setRepite()  Sets the current record's "repite" value
 * @method KnowledgeBase setPercent() Sets the current record's "percent" value
 * 
 * @package    gepfilter
 * @subpackage model
 * @author     Andrew Semikov
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseKnowledgeBase extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('knowledge_base');
        $this->hasColumn('ngram', 'string', 10, array(
             'type' => 'string',
             'primary' => true,
             'length' => 10,
             ));
        $this->hasColumn('belongs', 'string', 10, array(
             'type' => 'string',
             'primary' => true,
             'length' => 10,
             ));
        $this->hasColumn('repite', 'integer', null, array(
             'type' => 'integer',
             'notnull' => 'on',
             ));
        $this->hasColumn('percent', 'float', null, array(
             'type' => 'float',
             'notnull' => 'on',
             ));


        $this->index('viewer_viewed', array(
             'fields' => 
             array(
              0 => 'repite',
             ),
             ));
        $this->option('collation', 'utf8');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $timestampable0 = new Doctrine_Template_Timestampable(array(
             'created' => 
             array(
              'name' => 'created_at',
              'type' => 'timestamp',
             ),
             'updated' => 
             array(
              'disabled' => true,
             ),
             ));
        $this->actAs($timestampable0);
    }
}