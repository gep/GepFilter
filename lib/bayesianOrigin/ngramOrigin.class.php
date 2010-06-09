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

class ngramOrigin {

    protected $text;
    protected $length;
    protected $ngrams;
    public function __construct($letter=1) {
        $this->setLength($letter);
    }
    
    public function setLength($length=1){
        $this->length=$length;
    }
    
    public function setText($text) {
        $this->text =" ".$text."  ";
    }
    
    public function setInitialNgram($arg) {
        $this->ngrams = $arg;
    }
    
    public function getnGrams() {
        return $this->ngrams;
    }
    
    public function extract() {
        $txt = & $this->text;
        $len = strlen($txt);
        $length = & $this->length;
        $ngrams = & $this->ngrams;
        $buf='';
        $ultimo='';
        for($i=0; $i < $len; $i++) {
            if ( strlen($buf) < $length) {
                if ( !$this->useful($txt[$i]) ) 
                    continue;
                    
                if ($this->is_space($txt[$i]) && $this->is_space($ultimo))
                     continue;
                    
                $buf .= $this->is_space($txt[$i]) ? '_' : $txt[$i];
                $ultimo = $txt[$i];
            } else {
                $buf = strtolower($buf);
                $buf = str_replace(" ","_",$buf);
                $ngrams[$buf] = isset($ngrams[$buf]) ? $ngrams[$buf] + 1 : 1;
                $ultimo='';
                $buf = '';
                /** 
                 *    The last caracter weren't accumulated, so decrement
                 *    the counter and in the next itineration it will be 
                 *    hanled.
                 */
                $i--;
            }
        }
    }
    
	public function is_space($f) {
	    return $f==' ' || $f=="\n" || $f=="\r" || $f=="\t";
	}
	public function useful($f) {
	    $f = strtolower($f);
	    return ($f >= 'a' && $f <= 'z') || $this->is_space($f);
}
}




?>