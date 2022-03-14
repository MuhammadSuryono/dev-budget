<?php

require_once "database.php";
require_once "httpRequest.php";

class Session {

    // Value on seconds
    protected $timeOut = 8640;

    public function __construct($setNewSession = false)
    {
        if (!$this->alreadySetSession() && !$setNewSession) {
            header("location: login.php", true, 301);
            exit();
        } 
    }

    public function setSession($data = [])
    {
        if (count($data) > 0) {
            foreach ($data as $k => $v) {
                $_SESSION[$k] = $v;
            }
        }
    }

    public function setAuthLogin($idUser, $password = "")
    {
        $resp = HTTPRequester::HTTPPost('/auth/login', ["username" => $idUser, "password" => $password], "json");

        if ($resp->status == true) {
            $dataUser = $resp->data;
            $dataSession = [
                "nama_user" => $dataUser->nama_user,
                "divisi" => $dataUser->divisi,
                "jabatan" => $dataUser->level,
                "hak_akses" => $dataUser->hak_akses,
                "id_user" => $dataUser->id_user,
                "hak_page" => $dataUser->hak_page,
                "is_session" => true,
                "level" => $dataUser->level,
                "role" => $dataUser->role
            ];
            
            if($dataUser->aktif == "Y") {
                $this->setSession($dataSession);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function checkSession() {
        $base64 = $_GET["session"];
        if (isset($base64)) {
            $stringB64 = base64_decode($base64);
            $jsonDecode = json_decode($stringB64, true);
            $timeOut = $this->timeOutSession($jsonDecode["timeout"]);

            if ($timeOut) {
                $uriPath = $_SERVER["REQUEST_URI"];
                $nextPath = $this->getNewPathUrlSessionTimeOut($uriPath);
                $dataNextPath = json_encode(["next_path" => $nextPath, "id_user" => $jsonDecode["id_user"]]);
                $this->setSession(["before_session_next_path" => $dataNextPath]);

                echo "<script language='JavaScript'>
                alert('URL session anda telah expired, Mohon login kembali!');
                document.location = 'login.php';
                </script>";
            }else {
                $this->setAuthLogin($jsonDecode["id_user"]);
            }
        } 

        if (!isset($_SESSION['nama_user'])) {
            header("location:login.php");
        }
    }

    public function timeOutSession($timeStart = 0) {
        $timeNow = time();
        return ($timeNow - $timeStart) >= $this->timeOut;
    }

    private function getNewPathUrlSessionTimeOut($path) {
        $explodePath = explode("?", $path);
        $explodeQuery = explode("&", $explodePath[count($explodePath) - 1]);

        $newQuery = "";
        foreach ($explodeQuery as $query) {
            if (preg_match('/\bsession\b/', $query) == false) {
                $newQuery .= $query . "&";
            }
        }

        $newUrl = $explodePath[0] . "?" . $newQuery;
        return $newUrl;
    }

    public function getSession() {
        return $_SESSION;
    }

    public function alreadySetSession() {
        if (isset($_SESSION['is_session']) && $_SESSION['is_session'] == true) {
            return true;
        } else {
            false;
        }
    }
}