<?php
if (!defined('ABSPATH')) exit;

class TCMP_Language {
    var $domain;
    var $bundle;

    function __construct() {
        $this->bundle=new TCMP_Properties();
    }
    function load($domain, $file) {
        $this->domain=$domain;
        $this->bundle->load($file);
    }
    //echo the $ec->Lang->L result
    function P($key, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL) {
        $what=$this->L($key, $v1, $v2, $v3, $v4, $v5);
        echo $what;
    }
    //verify if the key is defined or not
    function H($key) {
        if($this->bundle==NULL || !$this->bundle->hasKeys()) {
            return FALSE;
        }

        $result=FALSE;
        if($this->bundle->existsKey($key)) {
            $result=TRUE;
        } elseif($this->bundle->existsKey($key.'1')) {
            $result=TRUE;
        } else {
            //special way to call this function passing arguments
            //WTF_something means key=WTF and something as first argument
            $s=strpos($result, '_');
            if ($s!==FALSE) {
                $text=substr($result, 0, $s);
                $value=substr($result, $s + 1);
                $e=strrpos($value, '_');
                if ($e!==FALSE) {
                    $text.=substr($value, $e + 1);
                    $value=substr($value, 0, $e);
                }
                if ($this->bundle->existsKey($text)) {
                    $result=TRUE;
                }
            }
        }
        return $result;
    }
    //read the key from a text file with its translation. Try to translate using __(
    function L($key, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL) {
        global $tcmp;
        $result=$key;
        $args=array($v1, $v2, $v3, $v4, $v5);

        if($this->bundle==NULL || !$this->bundle->hasKeys()) {
            $result=__($result, $this->domain);
        } else {
            //i use the file to store the translations without writing it inside the code
            if ($this->bundle->existsKey($key)) {
                $result=$this->bundle->getString($key);
                $result=__($result, $this->domain);
            } elseif ($this->bundle->existsKey($key.'1')) {
                $result='';
                $n=1;
                while ($this->bundle->existsKey($key.$n)) {
                    if ($result != '') {
                        $result .= '<br/>';
                    }
                    $result .= __($this->bundle->getString($key . $n), $this->domain);
                    ++$n;
                }
            } else {
                //special way to call this function passing arguments
                //WTF_something means key=WTF and something as first argument
                $s=strpos($result, '_');
                if ($s!==FALSE) {
                    $text=substr($result, 0, $s);
                    $value=substr($result, $s + 1);
                    $e=strrpos($value, '_');
                    if ($e!==FALSE) {
                        $text .= substr($value, $e + 1);
                        $value=substr($value, 0, $e);
                    }
                    if ($this->bundle->existsKey($text)) {
                        $result=$this->bundle->getString($text);
                        $args=array($value);
                    }
                }
                $result=__($result, $this->domain);
            }
        }
        if($result==$key) {
            $this->bundle->pushString($key, '');
        }
        //here i translate it using WP
        foreach($args as $k=>$v) {
            $k='{'.$k.'}';
            while(strpos($result, $k)!==FALSE) {
                $result=str_replace($k, $v, $result);
            }
        }
        foreach($args as $k=>$v) {
            $k='{dt:'.$k.'}';
            $v=$tcmp->Utils->formatSmartDatetime($v);
            while(strpos($result, $k)!==FALSE) {
                $result=str_replace($k, $v, $result);
            }
        }
        return $result;
    }
}
