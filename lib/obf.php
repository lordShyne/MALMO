<?php 


class Obf{

// public $letters = "abcdefghijklmnopqrstuvzxyw1234567890";


    function isSpace($ltr){
        return preg_match('/\s+/', $ltr);
    }


    function isValidLetter($ltr){
        return preg_match('/^[\w\.]+$/', $ltr);
    }

    function getCrypt(){
        return '<span style="padding:0 !important; margin:0 !important; display:inline-block !important; width:0 !important; height:0 !important; font-size:0 !important;">'.substr(md5(uniqid()),0,1).'</span>';
    }



    
    function obf($str){
        $text = "";
        $str = str_replace("|", "", $str);
        $strarr = str_split($str);

        foreach($strarr as $letter){
            if($this->isSpace($letter)){
                $text .= " ";
            }

            if($this->isValidLetter($letter)){
                $text .= $this->getCrypt().$letter.$this->getCrypt();
            }else{
                $text .= $letter;
            }


        }

        echo $text;
        
    }




}
$obf = new Obf;

?>