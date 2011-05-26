<?php


class spam2 {
    protected $_source;
    /**
     *  Constructor
     *
     *  Because the Spam Class do not know the source, you must
     *  define a function that return an array of token and values.
     *  
     *  @param string $Callback Function name
     */
    public function __construct($callback='') {

    }
    
    /**
     *    Returns the the posibility to text belogs to "spams" 
     *    
     *    @param $text Text to analize
     *    @access public 
     *    @return true
     */
    public function isItSpam($text,$type) {
        $ngram = new ngram;
        $ngram->setText($text);
        
        for($i=3; $i <= 5;$i++) {
            $ngram->setLength($i);
            $ngram->extract();
        }
        
        $fnc = $this->_source;
        $ngrams =  $ngram->getnGrams();
        $knowledge =  $fnc( $ngrams,$type );
        $total=0;
        $acc=0;
        foreach($ngrams as $k => $v) {
            if ( isset($knowledge[$k]) ) {
                $acc += $knowledge[$k] * $v;
                $total++;
            }
        }
        $percent = ($acc/$total);
        $percent = $percent > 1.0 ? 1.0 : $percent;
        return $percent * 100;
    }
    
   
    
    public function isItSpam_v2($text,$type) {
        
        $ngrams =  $this->getLexemes($text);

        $knowledge =  $this->getNgramsFromDB( $ngrams,$type );
        $total=0;
        $acc=0;
        
        /**
         *  N = total number of n-grams used.
         *  K = product of all n-grams (values are extracted from knowledge base)
         *  
         *  H = chi2Q( -2N K, 2N);
         *  S = chi2Q( -2N ( (1.0 - ngram(1)) ( 1.0 - ngram(2)) .. ( 1.0 - ngram(N)) ), 2N)
         *  I = ( 1 + H - S ) / 2
         *
         */
        $N = 0;
        $H = $S = 1;
        
//        var_dump($ngrams); exit;
        foreach($ngrams as $k => $v) {
        	if (($index = self::staticCheckIfValueExists($knowledge, $v['word'])) === false) continue;
//            if ( !isset($knowledge[$k]) ) continue;
            $N++;
            $value = $knowledge[$index]['percent'] * $v['nentry']; 
            $H *= $value;
            $S *= (float)( 1 - ( ($value>=1) ? 0.99 : $value) );
        }

        $H = $this->chi2Q( -2 * log( $N *  $H), 2 * $N);
        $S = (float)$this->chi2Q( -2 * log( $N *  $S), 2 * $N);
        $percent = (( 1 + $H - $S ) / 2) * 100;
        return is_finite($percent) ? $percent : 100;
    }
    
    
    public function getLexemes($text){
		$conn = Doctrine_manager::getInstance()->getCurrentConnection();
    	$handle = $conn->getDBh();
    	
    	$stmt2 = $handle->prepare("SELECT * FROM ts_stat('SELECT to_tsvector(''english'', ''".addslashes($text)."'')')");
//    	$stmt2->bindParam(':text_value', $text, PDO::PARAM_STR, 255);
    	$stmt2->execute();
//    	var_dump($stmt2->fetchAll(PDO::FETCH_ASSOC), addslashes($text)); exit;
    	return $stmt2->fetchAll(PDO::FETCH_ASSOC);
//    	var_dump($result); exit;
    	
    }
    
    /**
	 *  get ngrams
	 *
	 *  This is function is called by the classifier class, and it must 
	 *  return all the n-grams.
	 *  
	 *  @param Array $ngrams N-grams.
	 *  @param String $type Type of set to compare
	 */
    public function getNgramsFromDB($ngrams, $type){
	    
//    	var_dump($ngrams); exit;
    	
    	$info = array();
    	foreach ($ngrams as $ngram){
    		$info[] = $ngram['word'];
    	}
    	
	    
	    $q = Doctrine::getTable('Lexeme')->createQuery('tcv')->where('tcv.belongs = ?', $type)
	    													  ->andWhereIn('tcv.lexeme_item', $info);
	    $t = array();
	    foreach ($q->fetchArray() as $item){
	    	$t[]  = array('ngram' => $item['lexeme_item'], 'percent' => $item['percent']);
	    }
	    
	    $q->free();
	
	    return $t;
    }
    
    public function chi2Q( $x,  $v) {
        $m = (double)$x / 2.0;
        $s = exp(-$m);
        $t = $s;
        
        for($i=1; $i < ($v/2);$i++) {
            $t *= $m/$i;
            $s += $t;
        }
        return ( $s < 1.0) ? $s : 1.0;
    }
    
	public static function staticCheckIfValueExists($ar, $buff){
		foreach ($ar as $k => $val){
			if ($val['ngram'] == $buff){
				return $k;
			}
		}
		return false;
	}
}
?>