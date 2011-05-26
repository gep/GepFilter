<?php

/**
 * BaseConcatViewTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property text $word1
 * @property integer $ndoc1
 * @property integer $nentry1
 * @property integer $is_spam
 * 
 * @method text            getWord1()   Returns the current record's "word1" value
 * @method integer         getNdoc1()   Returns the current record's "ndoc1" value
 * @method integer         getNentry1() Returns the current record's "nentry1" value
 * @method integer         getIsSpam()  Returns the current record's "is_spam" value
 * @method ConcatViewTable setWord1()   Sets the current record's "word1" value
 * @method ConcatViewTable setNdoc1()   Sets the current record's "ndoc1" value
 * @method ConcatViewTable setNentry1() Sets the current record's "nentry1" value
 * @method ConcatViewTable setIsSpam()  Sets the current record's "is_spam" value
 * 
 * @package    gepfilter
 * @subpackage model
 * @author     Andrew Semikov
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseConcatViewTable extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('concat_view_table');
        $this->hasColumn('word1', 'text', null, array(
             'type' => 'text',
             'primary' => true,
             ));
        $this->hasColumn('ndoc1', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('nentry1', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('is_spam', 'integer', 1, array(
             'type' => 'integer',
             'primary' => true,
             'length' => 1,
             ));


        $this->index('word1_index', array(
             'fields' => 
             array(
              0 => 'word1',
             ),
             ));
        $this->index('is_spam_index', array(
             'fields' => 
             array(
              0 => 'is_spam',
             ),
             ));
        $this->option('collation', 'utf8');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}