<?php



/**

 * @file

 * The PHP page that serves all page requests on a Ih Framework.

 *

 * The routines here dispatch control to the appropriate handler, which then

 * prints the appropriate page.

 */



/**

 * Root directory of Ih installation.

 */


if (substr($_SERVER['HTTP_HOST'],0,3) == 'www') {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: http://centromedicoandrade.com.br'.$_SERVER['REQUEST_URI']);
}


define('IH_ROOT', getcwd());



date_default_timezone_set('America/Sao_Paulo');



session_start();



require_once IH_ROOT . '/core/bootstrap.php';

require_once IH_ROOT . '/core/autoload.php';



$application = new bootstrap();



autoloader::init();



$core = new Core();

$core->run();







?>

