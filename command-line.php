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
$al->addDirectory('App/Services');
$al->register();

use Components\Database\Transaction;

try
{
    Transaction::open('livro');
    var_dump( Pessoa::find(1)->toArray() );
    Transaction::close();
}
catch (Exception $e)
{
    echo $e->getMessage();
}