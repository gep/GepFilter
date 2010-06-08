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
//if (defined("TRAINER_CLASS") ) return true;
//define("TRAINER_CLASS",true);
//require(dirname(__FILE__)."/ngram.php");

/**
 *    This class "laern" about what is spam and what is not
 *
 *    
 */
class trainer {
    protected $examples;
    protected $knowledge;
    
    /**
     * 
     * @var ngram
     */
    protected $ngram;
    
    public function __construct() {
        $this->ngram = new ngram();
    }
    
    public function add_example($text, $clasification) {
        $this->examples[$clasification][] = $text;
    }
    
    public function setPreviousLearn($f) {
        $this->previous = $f;
    }
    
    public function extractPatterns() {
        
        foreach($this->examples as $tipo => $texts) {
            $params[$tipo] = 0;
            $this->ngram->setInitialNgram( isSet($this->previous[$tipo]) ? $this->previous[$tipo] : array() );
            foreach ($texts as $text) {
                $this->ngram->setText($text);
                for($i=3; $i <= 5;$i++) {
                    $this->ngram->setLength($i);
                    $this->ngram->extract();
                }
            }
 
            $this->knowledge[$tipo] = (isset($this->knowledge[$tipo]) ? $this->knowledge[$tipo] : array());
            foreach( $this->ngram->getnGrams() as $k => $v) {
                $this->knowledge[$tipo][$k] = array ('cant' => $v['weight'], 'ngram' => $v['ngram']);
                $params[$tipo] += $v['weight'];
            }
        }
        $this->computeBayesianFiltering($params);
    }
    
    
    public function getKnowledge(){
    	return $this->knowledge;
    }
    
    
    /**
     * compute bayesian probability
     * @param $param
     */
    public function computeBayesianFiltering($param) {
        //print_r($param);
        foreach($this->knowledge as $tipo => $caracterist) {
            foreach($caracterist as $k => $v) {
                 $t = ($v['cant']/$param[$tipo]);
                 $f = 0;
                 foreach($param as $k1 => $v1) 
                     if ( $k1 != $tipo) {
                        
                        $f += isset($this->knowledge[$k1][$k]['cant']) ? $this->knowledge[$k1][$k]['cant'] / $v1 : 0; 
                    }
                 $this->knowledge[$tipo][$k]['bayesian'] = $t / ($t + $f);
            }
        }
    }
    
    
    
}
?>