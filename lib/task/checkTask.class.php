<?php

class checkTask extends sfBaseTask
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
    $this->name             = 'check';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [check|INFO] task does things.
Call it with:

  [php symfony check|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $this->spam = new spam2();
    
    $type = 'spam';
	
    echo $type."\n\n\n";
    
    $this->texts = array("Polar night Phentermine Buy cheap xxx Really nice post Viagra Via",
    					 "Polar night", 
						 "Phentermine", 
						 "Buy cheap xxx",
						 "Really nice post",
						 "Viagra", 
						 "Via",
    					);
	foreach ($this->texts as $text){
		echo $text .'    '.$this->spam->isItSpam_v2($text,$type)."% \n";
	}
  }
}
