<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class TCMP_Logger {
    private $name;
    private $context=array();

    public function __construct($name='TCMP') {
        if($name=='') $name='TCMP';
        $this->name=$name;
    }

    public function pushContext($context) {
        array_push($this->context, $context);
    }
    public function popContext() {
        array_pop($this->context);
    }

    public function fatal($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL, $v6=NULL) {
        $what=$this->write('[FATAL]', $message, $v1, $v2, $v3, $v4, $v5, $v6);
        die($what);
    }
    public function debug($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL, $v6=NULL) {
        $this->write('[DEBUG]', $message, $v1, $v2, $v3, $v4, $v5, $v6);
    }
    public function info($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL, $v6=NULL) {
        $this->write('[INFO] ', $message, $v1, $v2, $v3, $v4, $v5, $v6);
    }
    public function error($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL, $v6=NULL) {
        $this->write('[ERROR]', $message, $v1, $v2, $v3, $v4, $v5, $v6);
    }
    private function dump($v) {
        if(is_array($v) && count($v)==0) {
            $v='[]';
        }
        if($v!=NULL) {
            if(is_array($v) || is_object($v)) {
                $v=print_r($v, TRUE);
            }
        }
        if(is_bool($v)) {
            $v=($v ? 'TRUE' : 'FALSE');
        }
        return $v;
    }
    private function write($verbosity, $message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL, $v6=NULL) {
        global $tcmp;

        $text=sprintf($message
            , $this->dump($v1)
            , $this->dump($v2)
            , $this->dump($v3)
            , $this->dump($v4)
            , $this->dump($v5)
            , $this->dump($v6));
        $message=date("d/m/Y H:i:s")." ".$verbosity." ";
        if(count($this->context)>0) {
            $message.='{'.$this->context[count($this->context)-1].'} ';
        }
        $message="\n".$message.$text;
        if(!$tcmp->Options->isLoggerEnable()) {
            return $message;
        }

        $hasErrors=false;
        $filename=TCMP_PLUGIN_DIR."logs/".$this->name."_".date("Ym").".txt";
        if (!$handle = fopen($filename, 'a')) {
            $hasErrors=true;
        }

        if(!$hasErrors && fwrite($handle, $message)===FALSE) {
            $hasErrors=true;
        }

        if(!$hasErrors) {
            fclose($handle);
        }
        return $message;
    }
}