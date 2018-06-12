<?php

class PMXE_Installer
{
    const MIN_PHP_VERSION = "5.3.0";

    const WRONG_PHP_VERSION_MESSAGE = "WP All Export requires PHP %1s or greater, you are using PHP %2s. Please contact your host and tell them to update your server to at least PHP %1s.";

    public function checkActivationConditions()
    {
        if (version_compare(phpversion(), self::MIN_PHP_VERSION  , "<")) {
            $this->error(sprintf(
                self::WRONG_PHP_VERSION_MESSAGE,
                self::MIN_PHP_VERSION,
                phpversion(),
                self::MIN_PHP_VERSION
            ));
        }
    }

    private function error($message){

        $message = __($message);
        $error = <<<EOT
<style type="text/css">
    body, html {
        margin: 0;
        padding: 0;
    }
</style>
<div class="error">
    <p style="padding-left:2px; font-size:13px; color: #444; font-family: 'Open Sans',sans-serif; -webkit-font-smoothing: subpixel-antialiased;">
        $message
    </p>
</div>
EOT;
        echo $error;
        die;
    }
}