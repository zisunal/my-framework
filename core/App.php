<?php
class App {

    private array $gets;
    private array $posts;
    private array $updates;
    private array $deletes;
    private array $puts;
    private array $patches;
    private bool $get_state = false;
    private bool $post_state = false;
    private bool $update_state = false;
    private bool $delete_state = false;
    private bool $put_state = false;
    private bool $patch_state = false;

    public function run() {
        if ($this->get_state) {
            $this->make_req($this->gets);
        }
        if ($this->post_state) {
            $this->make_req($this->posts);
        }
        if ($this->update_state) {
            $this->make_req($this->updates);
        }
        if ($this->delete_state) {
            $this->make_req($this->deletes);
        }
        if ($this->put_state) {
            $this->make_req($this->puts);
        }
        if ($this->patch_state) {
            $this->make_req($this->patches);
        }
    }

    public function get (string $url_be, string $function, array $args = []) : void {
        $this->gets[$url_be . "%-%-%-" . $function] = $args;
        $this->get_state = true;
    }

    public function post (string $url_be, string $function, array $args = []) : void {
        $this->posts[$url_be . "%-%-%-" . $function] = $args;
        $this->post_state = true;
    }

    public function update (string $url_be, string $function, array $args = []) : void {
        $this->updates[$url_be . "%-%-%-" . $function] = $args;
        $this->update_state = true;
    }

    public function delete (string $url_be, string $function, array $args = []) : void {
        $this->deletes[$url_be . "%-%-%-" . $function] = $args;
        $this->delete_state = true;
    }

    public function put (string $url_be, string $function, array $args = []) : void {
        $this->puts[$url_be . "%-%-%-" . $function] = $args;
        $this->put_state = true;
    }

    public function patch (string $url_be, string $function, array $args = []) : void {
        $this->patches[$url_be . "%-%-%-" . $function] = $args;
        $this->patch_state = true;
    }

    private function make_req (array $methods) : void {
        foreach ($methods as $method_n_fun => $args) {
            $method = explode("%-%-%-", $method_n_fun)[0];
            $function = explode("%-%-%-", $method_n_fun)[1];
            if (!strpos($method, "{")) {
                $req_url = static::req_url();
                $url_has_to_be = $method;
                if ($req_url == $url_has_to_be) {
                    call_user_func($function, $args);
                }
            } else {
                global $database;
                $count_br = count(explode("{", $method)) - 1;
                $url_has_to_be = replace_with($method, "{", "}", "");
                for ($j = 1; $j < $count_br; $j++) {
                    $url_has_to_be = replace_with($url_has_to_be, "{", "}", "");
                }
                $url_has_to_be = explode("/", $url_has_to_be);
                $req_url = explode("/", static::req_url());
     
                foreach ($url_has_to_be as $i => $v) {
                    if ($i > 0 && $v == "") {
                        $req_url[$i] = $i;
                        $url_has_to_be[$i] = $i;
                    }
                }
                $req_url = implode("/", $req_url);
                $url_has_to_be = implode("/", $url_has_to_be);
                // echo $req_url, " = ", $url_has_to_be;
                if ($req_url == $url_has_to_be && count(explode("/", static::req_url())) == count(explode("/", $req_url))) {
                    $dynas = explode("/", $method);
                    $dyna_values = explode("/", static::req_url());
                    $url_args = [];
                    foreach ($dynas as $index => $dyna) {
                        if (!strpos($dyna, "{") && !strpos($dyna, "}")) {
                            continue;
                        } else {
                            $url_args[$database->escape_string(get_between($dyna, "{", "}"))] = $database->escape_string($dyna_values[$index]);
                        }
                    }
                    $args = array_merge($args, $url_args);
                    call_user_func($function, $args);
                }
            }
        }
    }

    private static function req_url () : string {
        $url = $_SERVER["REQUEST_URI"];
        if (SUBDIR != "") {
            $url = urldecode(str_replace(SUBDIR . "/", "", $url));
        }
        return $url;
    }
    
}