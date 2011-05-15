<?php
/*
***************************************************************************
*   Copyright (C) 2007 by Cesar D. Rodas                                  *
*   cesar@sixdegrees.com.br                                               *
*                                                                         *
*   Permission is hereby granted, free of charge, to any person obtaining *
*   a copy of this software and associated documentation files (the       *
*   "Software"), to deal in the Software without restriction, including   *
*   without limitation the rights to use, copy, modify, merge, publish,   *
*   distribute, sublicense, and/or sell copies of the Software, and to    *
*   permit persons to whom the Software is furnished to do so, subject to *
*   the following conditions:                                             *
*                                                                         *
*   The above copyright notice and this permission notice shall be        *
*   included in all copies or substantial portions of the Software.       *
*                                                                         *
*   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,       *
*   EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF    *
*   MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.*
*   IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR     *
*   OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, *
*   ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR *
*   OTHER DEALINGS IN THE SOFTWARE.                                       *
***************************************************************************
*/ 
//if (defined("NGRAM_CLASS") ) return true;
//
//define("NGRAM_CLASS",true);

class ngram {

    protected $text;
    protected $length;
    protected $ngrams;
    public function __construct($letter=1) {
        $this->setLength($letter);
    }
    
    /**
     * set ngram length
     * @param $length
     */
    public function setLength($length=1){
        $this->length=$length;
    }
    
    
    /**
     * set text
     * @param string $text
     */
    public function setText($text) {
        $this->text =" ".$text."  ";
    }
    
    
    /**
     * set initial ngram
     * @param $arg
     */
    public function setInitialNgram($arg) {
        $this->ngrams = $arg;
    }
    
    public function getnGrams() {
        return $this->ngrams;
    }
    
    public function extract() {
        $len = strlen($this->text);
        $buf='';
        $ultimo='';
        
        for($i=0; $i < $len; $i++) {
            if ( strlen($buf) < $this->length) {
                if ( !$this->useful($this->text[$i]) ) 
                    continue;
                    
                if ($this->is_space($this->text[$i]) && $this->is_space($ultimo))
                     continue;
                    
                $buf .= $this->is_space($this->text[$i]) ? '_' : $this->text[$i];
                
                $ultimo = $this->text[$i];
            } else {
            	
                $buf = strtolower($buf);
                $buf = str_replace(" ","_",$buf);
                if (($index = $this->checkIfValueExists($buf)) !== false){ 
                	$this->ngrams[$index]['weight'] += 1;
                }else{
                	$this->ngrams[] = array('weight' => 1, 'ngram' => $buf);
                }
                $ultimo = '';
                $buf = '';

                $i--;
                
            }
            
        }
    }
    
    protected function is_space($f) {
    	return $f==' ' || $f=="\n" || $f=="\r" || $f=="\t";
	}
	
	
	protected function useful($f) {
	    $f = strtolower($f);
	    return ($f >= 'a' && $f <= 'z') || ($f >= 'а' && $f <= 'я') || $this->is_space($f);
	}
	
	
	protected function checkIfValueExists($buff){
		if (empty($this->ngrams)) return false;
		foreach ($this->ngrams as $k => $val){
			if ($val['ngram'] == $buff){
				return $k;
			}
		}
		return false;
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