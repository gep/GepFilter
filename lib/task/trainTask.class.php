<?php

class trainTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'gepfilter';
    $this->name             = 'train';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [train|INFO] task does things.
Call it with:

  [php symfony train|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $conn = Doctrine_manager::getInstance()->getCurrentConnection();
    $handle = $conn->getDBh();
    
    $date = date('Y-m-d H:i:s');
    
    
    $stmt2 = $handle->prepare("CREATE OR REPLACE VIEW spam_stat_view AS 
	SELECT ts_stat.word, ts_stat.ndoc, ts_stat.nentry
    FROM ts_stat('SELECT to_tsvector(''english'', content) FROM example WHERE state = ''spam'''::text) ts_stat(word, ndoc, nentry)");
	    	$stmt2->execute();
	    	
	$stmt2 = $handle->prepare("CREATE OR REPLACE VIEW ham_stat_view AS 
	SELECT ts_stat.word, ts_stat.ndoc, ts_stat.nentry
    FROM ts_stat('SELECT to_tsvector(''english'', content) FROM example WHERE state = ''1'''::text) ts_stat(word, ndoc, nentry)");
	    	$stmt2->execute();
	    	
	    	
	    	
	$stmt2 = $handle->prepare("CREATE OR REPLACE VIEW concat_view AS 
         SELECT spam_stat_view.word AS word1, spam_stat_view.ndoc AS ndoc1, spam_stat_view.nentry AS nentry1, 1 AS is_spam
           FROM spam_stat_view
		UNION 
         SELECT ham_stat_view.word AS word1, ham_stat_view.ndoc AS ndoc1, ham_stat_view.nentry AS nentry1, 0 AS is_spam
           FROM ham_stat_view;");
	    	$stmt2->execute();
    
    $spam_entries = Doctrine_Core::getTable('SpamStatView')->createQuery('ssv')
    													   ->select('SUM(ssv.nentry) as entry_sum')
    													   ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
    $spam_entries = $spam_entries['entry_sum'];
    
    $ham_entries = Doctrine_Core::getTable('HamStatView')->createQuery('hsv')
    													 ->select('SUM(hsv.nentry) as entry_sum')
    													 ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
   	$ham_entries = $ham_entries['entry_sum'];
   	
   	$stmt2 = $handle->prepare("TRUNCATE TABLE table_concat_view");
   	$stmt2->execute();
   	
   	$stmt2 = $handle->prepare("INSERT INTO table_concat_view 
							   (
							      SELECT word1,
							             ndoc1,
							             nentry1,
							             is_spam
							        FROM concat_view
							   )");
   	$stmt2->execute();
   	
//    print_r($spam_entries); echo "\n";
//    print_r($ham_entries); exit;
    
//    $stmt2 = $handle->prepare("DELETE FROM lexeme WHERE lexeme_item IN (SELECT word1 FROM concat_view) AND belongs IN (SELECT IF is_spam = 1 THEN 'spam' ELSE '1' END IF; FROM concat_view)");
//    $stmt2->execute();
	
    
    foreach (Doctrine_Core::getTable('TableConcatView')->createQuery('tcv')->fetchArray() as $cv){
    	 	$stmt2 = $handle->prepare("DELETE FROM lexeme WHERE lexeme_item = :lx AND belongs = :bel");
	    	$stmt2->bindParam(':lx', $cv['word1'], PDO::PARAM_STR, 12);
	    	$stmt2->bindValue(':bel', (($cv['is_spam'] == 1)?'spam':'ham'), PDO::PARAM_STR);
	    	$stmt2->execute();
 			
	    	
    $stmt = $handle->prepare("INSERT INTO 
    							lexeme(lexeme_item, belongs, repite, percent, created_at, updated_at) 
    							VALUES (:lx, 
    									:bel, 
    									:rep, 
    									(:perc)/(coalesce((SELECT nentry1::real/:opposite_amount FROM table_concat_view WHERE is_spam = :spam AND word1 = :lx), 0) + :perc), 
    									:created, 
    									:updated)");
			$stmt->bindParam(':lx', $cv['word1'], PDO::PARAM_STR, 255);
			$stmt->bindValue(':bel', (($cv['is_spam'] == 1)?'spam':'ham'), PDO::PARAM_STR);
			$stmt->bindParam(':rep', $cv['nentry1'], PDO::PARAM_INT, 11);
			$stmt->bindValue(':perc', $cv['nentry1']/(($cv['is_spam'] == 1)? $spam_entries : $ham_entries), PDO::PARAM_STR);
//			$stmt->bindParam(':spam', $cv['is_spam'], PDO::PARAM_INT, 11);
			$stmt->bindValue(':spam', (($cv['is_spam'])?'0':'1'), PDO::PARAM_INT);
			$stmt->bindValue(':opposite_amount', (($cv['is_spam'] == 1)? $ham_entries : $spam_entries),  PDO::PARAM_STR);
			$stmt->bindParam(':created', $date, PDO::PARAM_STR, 30);
			$stmt->bindParam(':updated', $date, PDO::PARAM_STR, 30);
			$stmt->execute();
    	
    }
//    SELECT sum(nentry) as summa
// FROM spam_stats 
// GROUP BY word
// LIMIT 500;
    
    


	    $this->logSection('info', 'Finished');
    
  }
}
