<?php
use Components\Control\Page;
use Components\Control\Action;
use Components\Widgets\Form\Form;
use Components\Widgets\Dialog\Message;
use Components\Widgets\Form\Entry;
use Components\Widgets\Form\Combo;
use Components\Widgets\Form\Text;
use Components\Widgets\Wrapper\FormWrapper;
use Components\Widgets\Container\Panel;
use Components\Database\Transaction;
use Components\Session\Session;

/**
 * Formulário de conclusão de venda
 */
class ConcluiVendaForm extends Page
{
    private $form;
    
    /**
     * método construtor
     */
    public function __construct()
    {
        parent::__construct();
        
        // instancia nova seção
        new Session;
        
        $this->form = new FormWrapper(new Form('form_conclui_venda'));
        $this->form->setTitle('Conclui venda');
        
        // cria os campos do formulário
        $cliente      = new Entry('id_cliente');
        $valor_venda  = new Entry('valor_venda');
        $desconto     = new Entry('desconto');
        $acrescimos   = new Entry('acrescimos');
        $valor_final  = new Entry('valor_final');
        $parcelas     = new Combo('parcelas');
        $obs          = new Text('obs');
        
        $parcelas->addItems(array(1=>'Uma', 2=>'Duas', 3=>'Três'));
        $parcelas->setValue(1);

        // define uma ação de cálculo Javascript
        $desconto->onBlur = "$('[name=valor_final]').val( Number($('[name=valor_venda]').val()) + Number($('[name=acrescimos]').val()) - Number($('[name=desconto]').val()) );";
        $acrescimos->onBlur = $desconto->onBlur;
        
        $valor_venda->setEditable(FALSE);
        $valor_final->setEditable(FALSE);
        
        $this->form->addField('Cliente', $cliente,   '50%');
        $this->form->addField('Valor', $valor_venda, '50%');
        $this->form->addField('Desconto', $desconto, '50%');
        $this->form->addField('Acréscimos', $acrescimos, '50%');
        $this->form->addField('Final', $valor_final, '50%');
        $this->form->addField('Parcelas', $parcelas, '50%');
        $this->form->addField('Obs', $obs, '50%');
        $this->form->addAction('Salvar', new Action(array($this, 'onGravaVenda')));
        
        parent::add($this->form);
    }
    
    /**
     * Carrega formulário de conclusão
     */
    public function onLoad($param)
    {
        $total = 0;
        $itens = Session::getValue('list');
        if ($itens)
        {
            // percorre os itens
            foreach ($itens as $item)
            {
                $total += $item->preco * $item->quantidade;
            }
        }
        
        $data = new StdClass;
        $data->valor_venda = $total;
        $data->valor_final = $total;
        $this->form->setData($data);
    }
    
    /**
     * Grava venda
     */
    public function onGravaVenda()
    {
        try 
        {
            // inicia transação com o banco 'livro'
            Transaction::open('livro');
            
            $dados = $this->form->getData();
            
            $cliente = Pessoa::find($dados->id_cliente);
            if (!$cliente) {
                throw new Exception('Cliente não encontrado');
            }
            if ($cliente->totalDebitos() > 0)
            {
                throw new Exception('Débitos impedem esta operação');
            }
            
            $venda = new Venda;
            $venda->cliente     = $cliente;
            $venda->data_venda  = date('Y-m-d');
            $venda->valor_venda = $dados->valor_venda;
            $venda->desconto    = $dados->desconto;
            $venda->acrescimos  = $dados->acrescimos;
            $venda->valor_final = $dados->valor_final;
            $venda->obs         = $dados->obs;
    
            // lê a variável $list da seção
            $itens = Session::getValue('list');
            if ($itens)
            {
                // percorre os itens
                foreach ($itens as $item)
                {
                    // adiciona o item na venda
                    $venda->addItem(new Produto($item->id_produto), $item->quantidade);
                }
            }
            
            $venda->store(); // armazena venda no banco de dados
            
            // gera o financeiro
            Conta::geraParcelas($dados->id_cliente, 2, $dados->valor_final, $dados->parcelas);
            
            Transaction::close(); // finaliza a transação
            Session::setValue('list', array()); // limpa lista de itens da seção
    
            // exibe mensagem de sucesso
            new Message('info', 'Venda registrada com sucesso');
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
        }
    }
}
