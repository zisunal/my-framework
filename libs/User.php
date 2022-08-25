<?php
/*
    @type---|0->user, 1->seller, 2->super admin, 3->stuff, 4->worker
*/
class User extends Common{
    public static $db_table = "users";
    public static $db_table_fields = array("name","email","type","password","addr","city","pincode","ctry","lat","lon","hash","created_at");
    public $id;
    public $name;
    public $email;
    public $password;
    public $hash;
    public $status;
    public $type;
    public $sub;
    public $sub_exp;
    public $addr;
    public $city;
    public $pincode;
    public $ctry;
    public $lat;
    public $lon;
    public $cart;
    public $wish;
    public $activity;
    public $rem;
    public $created_at;
    public $updated_at;

    public $con_pass;
    public $ins_id;
    public $alert;


    public function user_avail($user){
        global $database;
        $result = $database->query("SELECT * FROM users WHERE email = '$user' ");
        $count = $database->ct_row($result);
        if($count == 1){
            return false;
        }
        else{
            return true;
        }
    }

    public function verify_user($user, $pass){
        global $database;
        $user = $database->escape_string($user);
        $result = $database->query("SELECT * FROM users WHERE email = '$user' ");
        if ($database->confirm_query($result) != null) {
            $this->alert = $database->conn->error;
            return false;
        } else {
            if ($database->ct_row($result) == 0) {
                $this->alert = "You do not have any account with us.";
                return false;
            } else {
                while ($row = $database->fetch_array($result)) {
                    $this->password = $row['password'];
                    $this->id = $row['id'];
                    $this->email = $row['email'];
                    $this->hash = $row['hash'];
                    $this->name = $row['name'];
                    $this->status = $row['status'];
                    $this->sub = $row['sub'];
                    $this->sub_exp = $row['sub_exp'];
                }
                if (!password_verify($pass, $this->password)) {
                    $this->alert = "Please type the correct password.";
                    return false;
                } else {
                    $this->alert = "";
                    return true;
                }
            }
        }
    }

    public function rem_user($id){
        global $database;
        $rem_br = $this->rem = rand(0, 999999999);
        $rem = md5($this->rem);
        $name = "rem_site";
        $name_id = "rem_id";
        $result = $database->query("UPDATE users SET rem = '$rem' WHERE id = $id ");
        if($result){
            if(setcookie($name, $rem_br, time() + (86400 * 30))){
                if(setcookie($name_id, $id, time() + (86400 * 30))){
                    return true;
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }
        else{
            $this->alert = "You are loged in but not remembered to this browser";
            return false;
        }
    }

    public function hash_verify($user_id,$user_hash){
        global $database;
        $db_hash = self::find_by_id($user_id);
        $user_hash2 = $db_hash->hash;
        if($user_hash2 == $user_hash){
            if($database->query("UPDATE users SET status = 1 WHERE id = $user_id ")){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    public function total(){
        $results = self::do_query("SELECT * FROM users WHERE type == 0 AND 1 ");
        $total = 0;
        foreach($results as $result){
            $total = $total + 1;
        }
        return $total;
    }

    public function pass_len($pass){
        global $database;
        $pass = $database->escape_string($pass);
        if(strlen($pass) >= 8){
            return true;
        }
        else{
            return false;
        }
    }

    public function pass_con($pass, $con_pass){
        global $database;
        $pass = $database->escape_string($pass);
        $con_pass = $database->escape_string($con_pass);
        if($pass == $con_pass){
            return true;
        }
        else{
            return false;
        }
    }

    public function find_by_u($u)
    {
        $array = static::do_query("SELECT * FROM " . static::$db_table . " WHERE email = '$u' ");
        return !empty($array) ? array_shift($array) : false;
    }

    public function register() : bool 
    {
        if(!$this->find_by_u($this->email)) {
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
            $this->status = 1;
            $this->created_at = date("Y-m-d H:i:s", time());
            $this->hash = md5(rand(0, 999999999));
            if($this->create()){
                return true;
            } else {
                $this->alert = "Sorry! Something went wrong. Try again";
                return false;
            }         
        } else {
            $this->alert = "You already have an account with us. Please login";
            return false;
        }
    }

    public function reset_pass(string $email, string $app_name) : bool {
        global $database;
        $email = $database->escape_string($email);
        if ($this->user_avail($email)) {
            $u = User::find_by("email", $email)[0];
            $pass = md5(rand(0, 99999));
            $pass_hash = password_hash($pass, PASSWORD_BCRYPT);
            if ($database->query("UPDATE users SET password = '$pass_hash' WHERE id = $u->id ")) {
                return mail(
                    $email, 
                    "New password for " . $app_name, 
                    "Here is your new password for logging into " . $app_name . ": $pass. Use these credentials to login into " . $app_name . " at " . PROTOCOL . SITE_ROOT . "login"
                );
            } else {
                $this->alert = "Password did not changed. Please try again after sometimes";
            }
        } else {
            $this->alert = "Please check the email you entered. This email doesn't have any account with us.";
        }
    }

}

$user = new User();