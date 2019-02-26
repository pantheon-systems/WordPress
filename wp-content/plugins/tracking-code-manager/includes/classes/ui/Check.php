<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class TCMP_Check {
    var $data;

    public function __construct() {
        $this->data=array_merge($_POST, $_GET);
    }

    public function is($name, $value, $ignoreCase=TRUE) {
        $result=FALSE;
        if(isset($this->data[$name])) {
            if($ignoreCase) {
                $result=(strtolower($this->data[$name])==strtolower($value));
            } else {
                $result=($this->data[$name]==$value);
            }
        }
        return $result;
    }
    public function of($name, $default='') {
        $result=$default;
        if(isset($this->data[$name])) {
            $result=$this->data[$name];
        }
        return $result;
    }
    public function nonce($action, $nonce='_wpnonce') {
        if(isset($_REQUEST[$nonce])) {
            $nonce=$_REQUEST[$nonce];
        }
        return wp_verify_nonce($nonce, $action);
    }

    //check if is a mandatory field by checking the .txt language file
    private function error($name) {
        global $tcmp;

        $result=FALSE;
        $k=$tcmp->Form->prefix.'.'.$name.'.check';
        $v=$tcmp->Lang->L($k);
        if($v!=$k) {
            //this is a mandatory field so we give error
            $tcmp->Options->pushErrorMessage($v);
            $result=TRUE;
        }
        return $result;
    }

    public function value($name) {
        $result='';
        if(isset($this->data[$name])) {
            $result=sanitize_text_field($this->data[$name]);
        }
        if($result=='') {
            $this->error($name);
        }
        $this->data[$name]=$result;
        return $result;
    }
    public function values($name) {
        $result=array();
        if(is_string($name)) {
            $name=explode(',', $name);
        }
        foreach($name as $v) {
            $result[]=$this->value(trim($v));
        }
        return $result;
    }
    public function email($name) {
        $result=$this->value($name);
        if($result!='') {
            $result=sanitize_email($result);
            if(!is_email($result)) {
                $this->error($name);
            }
        }
        $this->data[$name]=$result;
        return $result;
    }
    public function float($name) {
        $result=$this->value($name);
        if($result!='' && !is_float($result)) {
            $this->error($name);
        }
        $result=floatval($result);
        $this->data[$name]=$result;
        return $result;
    }
    public function integer($name) {
        $result=$this->value($name);
        if($result!='' && !is_int($result)) {
            $this->error($name);
        }
        $result=intval($result);
        $this->data[$name]=$result;
        return $result;
    }

    public function hasErrors() {
        global $tcmp;
        return $tcmp->Options->hasErrorMessages();
    }
}