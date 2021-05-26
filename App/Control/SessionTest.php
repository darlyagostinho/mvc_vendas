<?php
use Components\Control\Page;
use Components\Session\Session;

class SessionTest extends Page
{
    public function __construct()
    {
        parent::__construct();
        
        new Session;
        
        Session::setValue('teste1', 456);
        
        parent::add( Session::getValue('teste1') );
    }
}
