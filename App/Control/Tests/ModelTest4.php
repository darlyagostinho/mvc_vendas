<?php
use Components\Control\Page;
use Components\Database\Transaction;

class ModelTest4 extends Page
{
    public function show()
    {
        try {
            Transaction::open('livro');
            
            $p1 = Pessoa::find(1);
            print 'Valor total: ' . $p1->totalDebitos() . '<br>';
            echo '<hr>';
            
            $contas = $p1->getContasEmAberto();
            if ($contas) {
                foreach ($contas as $conta) {
                    print $conta->dt_emissao . ' - ';
                    print $conta->dt_vencimento . ' - ';
                    print $conta->valor . '<br>';
                }
            }
            Transaction::close();
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}