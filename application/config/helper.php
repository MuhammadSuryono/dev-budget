<?php
require "session.php";

class Helper {
    protected $sess;
    public function __construct()
    {
        $this->sess = new Session();
        $this->getSession();
    }

    private function getSession()
    {
        $this->sess->checkSession();
    }
}