<?php
use Components\Control\Page;
use Components\Database\Transaction;

class TesteView extends Page
{
    public function __construct()
    {
        parent::__construct();
        
        Transaction::open('livro');
        $all = ViewSaldoPessoa::all();
        Transaction::close();
        
        var_dump($all);
    }
}
