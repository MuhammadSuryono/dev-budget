<?php
require "session.php";

class Helper {
    protected $sess;
    public function __construct($setNewSession = false)
    {
        $this->sess = new Session($setNewSession);
        $this->getSession();
    }

    private function getSession()
    {
        $this->sess->checkSession();
    }

    public function getHostUrl()
    {
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
        $host = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $url = explode('/', $host);
        $url = $url[0] . '/' . $url[1] . '/';
        return $protocol.$url;
    }

    public function getUrlSession($path, $idUser)
    {
        $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $url = explode('/', $url);
        $url = $url[0]. '/'. $url[1] . $path.'&session='.base64_encode(json_encode(["id_user" => $idUser, "timeout" => time()]));
        return $url;
    }
}