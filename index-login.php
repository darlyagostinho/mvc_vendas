<?php
// Lib loader
require_once 'Lib/Livro/Core/ClassLoader.php';
$al= new Livro\Core\ClassLoader;
$al->addNamespace('Livro', 'Lib/Livro');
$al->register();

// App loader
require_once 'Lib/Livro/Core/AppLoader.php';
$al= new Livro\Core\AppLoader;
$al->addDirectory('App/Control');
$al->addDirectory('App/Model');
$al->register();

// Vendor
$loader = require 'vendor/autoload.php';
$loader->register();

use Livro\Session\Session;

new Session;


if (Session::getValue('logged'))
{
    $template = file_get_contents('App/Templates/template.html');
    $class = isset($_GET['class']) ? $_GET['class'] : '';
}
else
{
    $template = file_get_contents('App/Templates/login.html');
    $class = 'LoginForm';
}


$content  = '';

if (class_exists($class))
{
    try
    {
        $pagina = new $class;
        ob_start();
        $pagina->show();
        $content = ob_get_contents();
        ob_end_clean();
    }
    catch (Exception $e)
    {
        $content = $e->getMessage() . '<br>' .$e->getTraceAsString();
    }
}

$output = str_replace('{content}', $content, $template);
$output = str_replace('{class}',   $class, $output);
echo $output;
