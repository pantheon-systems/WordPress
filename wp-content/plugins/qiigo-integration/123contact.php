<?php

//$_SERVER['HTTP_REFERER'] = 'http://123contactform.com';
// 3/6/17 sn comments out above line, utilize the session set in head instead. 
//if( !session_id()) session_start();
//$_SERVER['HTTP_REFERER'] = $_SESSION['org_referer']; 
 
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
