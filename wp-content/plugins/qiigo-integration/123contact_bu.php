<?php

$_SERVER['HTTP_REFERER'] = 'http://123contactform.com';

$content = null;
$path = null;

require( '../../../wp-load.php' );

ob_start();
$content = '';

\Qiigo\Plugin\Integration\FrontEnd\WebHook::Run();

$content .= date('Ymd-His', time()).': '.ob_get_contents();
ob_end_clean();

$content .= "\n-----------------------------------------------------------\n";
$path = dirname(__FILE__).DIRECTORY_SEPARATOR.'123contact.log';
file_put_contents($path, $content, FILE_APPEND);

echo $content;
exit();