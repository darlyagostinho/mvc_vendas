<?php
use Components\Control\Page;
use Components\Control\Action;
use Components\Widgets\Form\Form;
use Components\Widgets\Form\Entry;
use Components\Widgets\Container\VBox;
use Components\Widgets\Datagrid\Datagrid;
use Components\Widgets\Datagrid\DatagridColumn;
use Components\Database\Transaction;

use Components\Traits\DeleteTrait;
use Components\Traits\ReloadTrait;

use Components\Widgets\Wrapper\DatagridWrapper;
use Components\Widgets\Wrapper\FormWrapper;
use Components\Widgets\Container\Panel;

/**
 * Página de produtos
 */
class ProdutosList extends Page
{
    private $form;
    private $datagrid;
    private $loaded;
    private $connection;
    private $activeRecord;
    private $filters;
    
    use DeleteTrait;
    use ReloadTrait {
        onReload as onReloadTrait;
    }
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        
        // Define o Active Record
        $this->activeRecord = 'Produto';
        $this->connection   = 'livro';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_busca_produtos'));
        $this->form->setTitle('Produtos');
        
        // cria os campos do formulário
        $descricao = new Entry('descricao');
        
        $this->form->addField('Descrição',   $descricao, '100%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Cadastrar', new Action(array(new ProdutosForm, 'onEdit')));
        
        // instancia objeto Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);
        
        // instancia as colunas da Datagrid
        $codigo   = new DatagridColumn('id',             'Código',    'center',  '10%');
        $descricao= new DatagridColumn('descricao',      'Descrição', 'left',   '30%');
        $fabrica  = new DatagridColumn('nome_fabricante','Fabricante','left',   '30%');
        $estoque  = new DatagridColumn('estoque',        'Estoq.',    'right',  '15%');
        $preco    = new DatagridColumn('preco_venda',    'Venda',     'right',  '15%');
        
        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($descricao);
        $this->datagrid->addColumn($fabrica);
        $this->datagrid->addColumn($estoque);
        $this->datagrid->addColumn($preco);
        
        $this->datagrid->addAction( 'Editar',  new Action([new ProdutosForm, 'onEdit']), 'id', 'fa fa-edit fa-lg blue');
        $this->datagrid->addAction( 'Excluir', new Action([$this, 'onDelete']),          'id', 'fa fa-trash fa-lg red');
        
        // monta a página através de uma caixa
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);
        
        parent::add($box);
    }
    
    public function onReload()
    {
        // obtém os dados do formulário de buscas
        $dados = $this->form->getData();
        
        // verifica se o usuário preencheu o formulário
        if ($dados->descricao)
        {
            // filtra pela descrição do produto
            $this->filters[] = ['descricao', 'like', "%{$dados->descricao}%", 'and'];
        }
        
        $this->onReloadTrait();   
        $this->loaded = true;
    }
    
    /**
     * Exibe a página
     */
    public function show()
    {
         // se a listagem ainda não foi carregada
         if (!$this->loaded)
         {
	        $this->onReload();
         }
         parent::show();
    }
}
