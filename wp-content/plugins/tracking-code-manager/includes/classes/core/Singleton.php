<?php
class TCMP_Singleton {
    var $Lang;
    var $Utils;
    var $Form;
    var $Check;
    var $Options;
    var $Log;
    var $Cron;
    var $Tracking;
    var $Manager;
    var $Ecommerce;
    var $Plugin;
    var $Tabs;

    function __construct() {
        $this->Lang=new TCMP_Language();
        $this->Tabs=new TCMP_Tabs();
        $this->Utils=new TCMP_Utils();
        $this->Form=new TCMP_Form();
        $this->Check=new TCMP_Check();
        $this->Options=new TCMP_Options();
        $this->Log=new TCMP_Logger();
        $this->Cron=new TCMP_Cron();
        $this->Tracking=new TCMP_Tracking();
        $this->Manager=new TCMP_Manager();
        $this->Ecommerce=new TCMP_Ecommerce();
        $this->Plugin=new TCMP_Plugin();
    }
    public function init() {
        $this->Lang->load('tcmp', TCMP_PLUGIN_DIR.'languages/Lang.txt');
        $this->Tabs->init();
        $this->Cron->init();
        $this->Manager->init();
    }
}