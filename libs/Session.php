<?php

    class Session{

        private $signed_in = false;
        public $user_id;
        public $type;
        public $msg_sel;
        public $msg_type;

        function __construct()
        {
            session_start();
            $this->check_login();
            $this->check_message();
            $this->check_rem();
        }

        public function message($msg = ""){
            if(!empty($msg)){
                $_SESSION['message'] = $msg;
            }
            else{
                return $this->message;
            }
        }

        private function check_message(){
            if(isset($_SESSION['message'])){
                $this->message = $_SESSION['message'];
                unset($_SESSION['message']);
            }
            else{
                $this->message = "";
            }
        }

        private function check_rem(){
            $name = 'rem_site';
            $id1 = 'rem_id';
            if(isset($_COOKIE[$id1])){
                $id = $_COOKIE[$id1];
                $user = User::find_by_id($id);
                $db_value = $user->rem;
                if(isset($_COOKIE[$name])){
                    $value = $_COOKIE[$name];
                    if($db_value == md5($value)){
                        $this->login($user->id);
                    }
                }
            }
        }

        public function is_signed_in(){
            return $this->signed_in;
        }

        public function login($id){
            global $database;
            if(!empty($id)){
                $now = date("Y-m-d H:i:s", time());
                $database->query("UPDATE users SET activity = '$now' WHERE id = $id ");
                $this->user_id = $_SESSION['user_id'] = $id;
                $this->signed_in = true;
                return true;
            }
            else{
                return false;
            }
        }

        public function logout(){
            unset($this->user_id);
            unset($_SESSION['user_id']);
            if(isset($_SESSION['ord_id'])){
                unset($_SESSION['ord_id']);
            }
            if(isset($_SESSION['admin'])){
                unset($_SESSION['admin']);
            }
            $this->signed_in = false;
            session_destroy();
            $name = 'rem_site';
            $id1 = 'rem_id';
            if(isset($_COOKIE[$id1])){
                unset($_COOKIE[$id1]); 
                setcookie($id1, null, time()-3600);
            }
            if(isset($_COOKIE[$name])){
                unset($_COOKIE[$name]); 
                setcookie($name, null, time()-3600);
            }
        }

        private function check_login(){
            if(isset($_SESSION['user_id'])){
                $this->user_id = $_SESSION['user_id'];
                $this->signed_in = true;
            }
            else{
                unset($this->user_id);
                $this->signed_in = false;
            }
        }

    }

    $session = new Session();

?>