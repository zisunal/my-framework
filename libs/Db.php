<?php

    class Database{

        public $conn;

        public function __construct()
        {
                $this->connection();
        }

        public function connection(){
            if(!isset($_SESSION["db_host"]) & !isset($_SESSION["db_username"]) & !isset($_SESSION["db_pass"]) & !isset($_SESSION["db_name"]) & !isset($_SESSION["db_port"])){
                $host = isset($_ENV["DB_HOST"]) ? $_ENV["DB_HOST"] : "no" ;
                $user = isset($_ENV["DB_USER"]) ? $_ENV["DB_USER"] : "no" ;
                $pass = isset($_ENV["DB_PASS"]) ? $_ENV["DB_PASS"] : "no" ;
                $db_name = isset($_ENV["DB_NAME"]) ? $_ENV["DB_NAME"] : "no" ;
                $db_port = isset($_ENV["DB_PORT"]) ? $_ENV["DB_PORT"] : "no" ;
            }else{
                $host = $_SESSION["db_host"];
                $user = $_SESSION["db_username"];
                $pass = $_SESSION["db_pass"];
                $db_name = $_SESSION["db_name"];
                $db_port = $_SESSION["db_port"];
            }
            $this->conn = new mysqli($host,$user,$pass,$db_name,$db_port);
        }

        public function query($sql){
            return $this->conn->query($sql);
        }

        public function confirm_query($result){
            !$result ? $this->conn->error : null;
        }

        public function ct_row($result){
            return mysqli_num_rows($result);
        }

        public function escape_string($string){
            $escaped_string = $this->conn->real_escape_string($string);
            return $escaped_string;
        }

        public function the_insert_id(){
            return $this->conn->insert_id;
        }

        public function fetch_array($result){
            $row = mysqli_fetch_assoc($result);
            return $row;
        }

        public function secure($val){
            return (is_array($val))?array_map(array($this, 'secure'),$val):htmlspecialchars($val, ENT_COMPAT, 'UTF-8');
          }
      
          public function get($key=''){
              if(!empty($key)){ 
                  if(is_array($_GET[$key])){
                      $array = $this->secure($_GET[$key]);
                      return filter_var_array($array, FILTER_SANITIZE_STRING);
                  }else{
                      return htmlspecialchars(filter_input(INPUT_GET, $key), ENT_COMPAT, 'UTF-8');
                  }
              }else{
               $values = [];
              foreach($_GET as $key => $value){
                $values["$key"] = htmlspecialchars(filter_input(INPUT_GET, $key), ENT_COMPAT, 'UTF-8');
              }
              return $values;
          }
          }
      
          public function post($data=''){
              if(@is_array($_POST[$data]) or empty($data)){
                  if(empty($data)){
                      $array = $_POST;
                  }else{
                      $array = $_POST[$data];
                  }
                  // $array = call_user_func_array('mb_convert_encoding',array($array,'HTML-ENTITIES','UTF-8'));
                  $array = filter_var_array($array, FILTER_SANITIZE_STRING); 
                  return $array;
              }else{
                  $val = htmlspecialchars(filter_input(INPUT_POST,$data), ENT_COMPAT, 'UTF-8'); 
                  return $val;
              }
          }

    }

    $database = new Database();

?>