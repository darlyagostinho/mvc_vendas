<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Password;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Container\Panel;
use Livro\Session\Session;

class LoginForm extends Page
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new FormWrapper(new Form('form_login'));
        $this->form->setTitle('Login');
        
        $login = new Entry('login');
        $password = new Password('password');
        
        $this->form->addField('Login', $login, 200);
        $this->form->addField('Senha', $password, 200);
        
        $this->form->addAction('Login', new Action( [$this, 'onLogin'] ));
        
        parent::add($this->form);
    }
    
    public function onLogin($param)
    {
        $data = $this->form->getData();
        
        if ($data->login == 'admin' AND $data->password == 'admin')
        {
            Session::setValue('logged', TRUE);
            echo "<script> window.location = 'index.php'; </script>";
        }
    }
    
    public function onLogout($param)
    {
        Session::setValue('logged', FALSE);
        echo "<script> window.location = 'index.php'; </script>";
    }
}
