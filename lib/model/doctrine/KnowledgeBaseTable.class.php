<?php


class KnowledgeBaseTable extends Doctrine_Table
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('KnowledgeBase');
    }
}