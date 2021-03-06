<?php
// Lib loader
require_once 'Lib/Components/Core/ClassLoader.php';
$al= new Components\Core\ClassLoader;
$al->addNamespace('Components', 'Lib/Components');
$al->register();

// App loader
require_once 'Lib/Components/Core/AppLoader.php';
$al= new Components\Core\AppLoader;
$al->addDirectory('App/Control');
$al->addDirectory('App/Model');
$al->register();

// Vendor
$loader = require 'vendor/autoload.php';
$loader->register();

$template = file_get_contents('App/Templates/template.html');
$content  = '';
$class    = '';

if ($_GET)
{
    $class = $_GET['class'];
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
    else
    {
        $content = "Class <b>{$class}</b> not found"; 
    }
}
$output = str_replace('{content}', $content, $template);
$output = str_replace('{class}',   $class, $output);
echo $output;
