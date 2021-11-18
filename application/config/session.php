<?php

require_once "database.php";

class Session {

    // Value on seconds
    protected $timeOut = 8640;

    public function __construct()
    {
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
        $con = new Database();
        $dataSession = [];
        $sql = mysqli_query($con->connect(), "SELECT * FROM tb_user WHERE id_user='$idUser'");

        if (mysqli_num_rows($sql) == 1) {
            $dataUser = mysqli_fetch_assoc($sql);
            $dataSession = [
                "nama_user" => $dataUser["nama_user"],
                "divisi" => $dataUser["divisi"],
                "jabatan" => $dataUser["jabatan"],
                "hak_akses" => $dataUser["hak_akses"],
                "id_user" => $dataUser["id_user"],
                "hak_page" => $dataUser["hak_page"]
            ];



            if($dataUser["aktif"] == "Y") {
                $this->setSession($dataSession);
            }
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
}