<?php
function create_table(string $tbl, array $fields, string $pr_key, array $default = array(), array $extras = array()): bool
{
    global $database;

    $sql = "CREATE TABLE $tbl (";

    foreach ($fields as $key => $field) {
        if (!empty($default)) {
            if (array_key_exists($key, $default)) {
                $def_value = $default[$key];
                $sql .= $key . " " . $field . " DEFAULT $def_value,";
            } else {
                if ($pr_key == $key) {
                    $sql .= $key . " " . $field . " NOT NULL " .  "AUTO_INCREMENT" . ",";
                } else {
                    $sql .= $key . " " . $field . " NOT NULL,";
                }
            }
        } else {
            $sql .= $key . " " . $field . " NOT NULL " . ($pr_key == $key) ? "AUTO_INCREMENT" : "" . " ,";
        }
    }

    $sql .= " PRIMARY KEY (" . $pr_key . ")
            );";

    if ($database->query($sql)) {
        $new_fields = $fields;
        foreach ($default as $key11 => $v) {
            unset($fields[$key11]);
        }
        unset($fields[$pr_key]);

        $class_file = fopen(__DIR__ . DS . ucfirst($tbl) . ".php", "w");
        $php_code = '<?php' . "\n" . 'class ' . ucfirst($tbl) . ' extends Common{' . "\n";
        $php_code .= "\t" . 'public static $db_table = "' . $tbl . '";' . "\n";
        $php_code .= "\t" . 'public static $db_table_fields = [';
        $i = 1;
        foreach ($fields as $k => $val) {
            if ($i == count($fields)) {
                $php_code .= '"' . $k . '"';
            } else {
                $php_code .= '"' . $k . '",';
            }

            $i++;
        }
        $php_code .= '];' . "\n";
        foreach ($new_fields as $key1 => $value1) {
            $php_code .= "\t" . "public $$key1;\n";
        }

        if (!empty($extras)) {
            foreach ($extras as $key2 => $value2) {
                if ($value2 == "") {
                    $php_code .= "\t" . "public $$key2;\n";
                } else {
                    $php_code .= "\t" . "public $$key2 = $value2;\n";
                }
            }
        }

        $php_code .= "}\n";
        $php_code .= '$' . $tbl . 's = new ' . ucfirst($tbl) . '();';

        if (fwrite($class_file, $php_code)) {
            fclose($class_file);
            $init_file = fopen(__DIR__ . DS . "init.php", "a");
            $add_file = "\n" . '<?php require_once("' . ucfirst($tbl) . '.php"); ?>';

            if (fwrite($init_file, $add_file)) {
                fclose($init_file);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function _full($code){
    return Lan::_full($code);
}
function _abbr($name){
    return Lan::_abbr($name);
}
function __($str)
{
    $lan = isset($_COOKIE["cus_lan"]) && $_COOKIE["cus_lan"] != "" ? $_COOKIE["cus_lan"] : "en";
    return Transl::translate($str, $lan);
}
function _esc($str) {
    return str_replace("\\", "", $str);
}
function _update(string $tbl, array $fields, int $id) {
    global $database;
    $sql = "";
    $i = 1;
    foreach($fields as $k => $v) {
        if(count($fields) > $i) {
            $sql .= $k . " = '" . $v . "', ";
        } else {
            $sql .= $k . " = '" . $v . "'";
        }
        $i++;
    }
    if ($database->query("UPDATE $tbl SET $sql WHERE id = $id ")) {
        return true;
    } else {
        return false;
    }
}
function _sort(array $arr, string $field, bool $asc = true, bool $str = true){
    $arr = $arr;
    for($j = 0; $j < count($arr) - 1; $j++) {
        if ($asc) {
            for($i = 1; $i < count($arr); $i++) {
                $compare = $str ? strnatcasecmp($arr[$i - 1]->$field, $arr[$i]->$field) : $arr[$i - 1]->$field - $arr[$i]->$field;
                if($compare > 0){
                    $a = $arr[$i - 1];
                    $arr[$i - 1] = $arr[$i];
                    $arr[$i] = $a;
                }
            }
        } else {
            for($i = 1; $i < count($arr); $i++) {
                $compare = $str ? strnatcasecmp($arr[$i - 1]->$field, $arr[$i]->$field) : $arr[$i - 1]->$field - $arr[$i]->$field;
                if($compare < 0){
                    $a = $arr[$i - 1];
                    $arr[$i - 1] = $arr[$i];
                    $arr[$i] = $a;
                }
            }
        }
    }
    return $arr;
}
function _mail($to, $sub, $msg, $header_from) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= 'From: ' . $header_from . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    mail(
        $to,
        $sub,
        $msg,
        $headers
    );
}
function _rand($arr) {
    return $arr[rand(0, count($arr) - 1)];
}
function _t(string $dt, string $from, string $to, string $format) {
    $date = new DateTime($dt, new DateTimeZone($from));
    $date->setTimezone(new DateTimeZone($to));
    return $date->format($format);
}

function _rand_str($length = 25) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function _os() { 

    $os_platform = "Android";

    $os_array = array(
        '/windows nt 10/i'      =>  'Windows 10',
        '/windows nt 6.3/i'     =>  'Windows 8.1',
        '/windows nt 6.2/i'     =>  'Windows 8',
        '/windows nt 6.1/i'     =>  'Windows 7',
        '/windows nt 6.0/i'     =>  'Windows Vista',
        '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     =>  'Windows XP',
        '/windows xp/i'         =>  'Windows XP',
        '/windows nt 5.0/i'     =>  'Windows 2000',
        '/windows me/i'         =>  'Windows ME',
        '/win98/i'              =>  'Windows 98',
        '/win95/i'              =>  'Windows 95',
        '/win16/i'              =>  'Windows 3.11',
        '/macintosh|mac os x/i' =>  'Mac OS X',
        '/mac_powerpc/i'        =>  'Mac OS 9',
        '/linux/i'              =>  'Linux',
        '/ubuntu/i'             =>  'Ubuntu',
        '/iphone/i'             =>  'iPhone',
        '/ipod/i'               =>  'iPod',
        '/ipad/i'               =>  'iPad',
        '/android/i'            =>  'Android',
        '/blackberry/i'         =>  'BlackBerry',
        '/webos/i'              =>  'Mobile'
    );

    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $_SERVER['HTTP_USER_AGENT'])) {
            $os_platform = $value;
        }
    }

    return $os_platform;
}

function _browser() {

    $browser = "Other";

    $browser_array = array(
        '/mobile/i'    => 'Handheld',
        '/msie/i'      => 'IE',
        '/firefox/i'   => 'Firefox',
        '/safari/i'    => 'Safari',
        '/chrome/i'    => 'Chrome',
        '/edg/i'      => 'Edge',
        '/OPR/i'     => 'Opera',
        '/netscape/i'  => 'Netscape',
        '/maxthon/i'   => 'Maxthon',
        '/konqueror/i' => 'Konqueror'
    );

    foreach ($browser_array as $regex => $value)
        if (preg_match($regex, $_SERVER['HTTP_USER_AGENT']))
            $browser = $value;

    return $browser;
}

function _device() {
    $tablet_browser = 0;
    $mobile_browser = 0;
    
    if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
        $tablet_browser++;
    }
    
    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
        $mobile_browser++;
    }
    
    if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
        $mobile_browser++;
    }
    
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
        'newt','noki','palm','pana','pant','phil','play','port','prox',
        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
        'wapr','webc','winw','winw','xda ','xda-');
    
    if (in_array($mobile_ua,$mobile_agents)) {
        $mobile_browser++;
    }
    
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
        $mobile_browser++;
        //Check for tablets on opera mini alternative headers
        $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
        $tablet_browser++;
        }
    }
    
    if ($tablet_browser > 0) {
        return "Tablet";
    }
    else if ($mobile_browser > 0) {
        return "Mobile";
    }
    else {
        return "Desktop";
    }   
}

function _prev_month(int $how = 1) {
    $tgl = date("d M Y", time());
    return date("M Y",mktime(0,0,0,date("m", strtotime($tgl))-$how,1,date("Y", strtotime($tgl))));
}

function _sum_ar(array $arr, string $field, bool $multi = false) {
    $sum = 0;
    if($multi) {
        foreach($arr as $ar) {
            foreach($ar as $a) {
                $sum += $a->$field;
            }
        }
    } else {
        foreach($arr as $ar) {
            $sum += $ar->$field;
        }
    }
    return $sum;
}

function _rand_col() {
    $characters = '0123456789abcdefABCDEF';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 6; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return "#" . $randomString;
}

function _dt_diff(string $date1, string $date2 = "", string $out = "y"){
    if($date2 == "") {
        $date2 = date("Y-m-d", time());
    }
    $d1 = new DateTime($date1);
    $d2 = new DateTime($date2);
    $interval = $d1->diff($d2);
    if($out == "d") {
        return ($interval->invert == 1 ? $interval->y * 365 : 0) + ($interval->invert == 1 ? $interval->m * 30 : 0) + ($interval->invert == 1 ? $interval->d : 0) + ($interval->invert == 1 && $interval->h > 0 ? 1 : 0);
    } else {
        return $interval->$out;
    }
    
}

//Script native functions
function get_header (string $title, array $dependencies = [], array $meta_tags = [], array $schema = [], array $body_classes = [], string $extra_css = '', string $extra_header_tags = '') : void {
    $app_info = App_info::find_by_id(1);
    $file = fopen(__DIR__ . '/../templates/header.php', 'r');
    $data = fread($file, filesize(__DIR__ . '/../templates/header.php'));
    fclose($file);
    $css = '';
    foreach ($dependencies as $dep) {
        $css .= (DEBUG ? str_replace('{{VERSION}}', '?v=' . time(), $dep) : str_replace('{{VERSION}}', '', $dep)) . "\n";
    }
    $class = '';
    foreach ($body_classes as $body_class) {
        $class .= $body_class . ' ';
    }
    $data = $class != '' ? str_replace('{{CLASSES}}', "class=\"$class\"", $data) : str_replace(' {{CLASSES}}', '', $data);
    $data = str_replace('{{PLUGINS_CSS}}', $css, $data);
    $data = str_replace('{{TITTLE}}', _esc($title), $data);
    $meta_str = "";
    if (count($meta_tags) > 0) {
        foreach ($meta_tags as $name => $content) {
            $meta_str .= '<meta name="' . _esc($name) . '" content="' . _esc($content) . '" />' . "\n";
        }
    } else {
        $meta_str .= '<meta name="title" content="' . _esc($app_info->seo_tit) . '" />' . "\n";
        $meta_str .= '<meta name="description" content="' . _esc($app_info->seo_des) . '" />' . "\n";
        $meta_str .= '<meta name="keywords" content="' . _esc($app_info->seo_kw) . '" />' . "\n";
        $meta_str .= '<meta name="author" content="' . _esc($app_info->seo_author) . '" />' . "\n";
        $meta_str .= '<meta property="og:title" content="' . _esc($app_info->seo_tit) . '" />
        <meta property="og:description" content="' . _esc($app_info->seo_des) . '" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="' . PROTOCOL . (SUBDIR != "" ? str_replace("/" . SUBDIR, "", SITE_ROOT) : SITE_ROOT) . $_SERVER["REQUEST_URI"] . '" />
        <meta property="og:image" content="' . PROTOCOL . SITE_ROOT . '/' . $app_info->logo . '" />
        <meta name="twitter:title" content="' . _esc($app_info->seo_tit) . '">
        <meta name="twitter:description" content="' . _esc($app_info->seo_des) . '"">
        <meta name="twitter:image" content="' . PROTOCOL . SITE_ROOT . '/' . $app_info->logo . '">
        <meta name="twitter:card" content="website">';
    }
    $schema_str = "";
    if (count($schema) > 1 && is_array($schema[1])) {

    }
    $data = str_replace("{{SCHEMA}}", $schema_str, $data);
    $data = str_replace("{{META_STR}}", $meta_str, $data);
    $data = $extra_css != '' ? str_replace('{{EXTRA_CSS}}', "<style>$extra_css</style>", $data) : str_replace('{{EXTRA_CSS}}', '', $data);
    $data = str_replace('{{FAVICON}}', PROTOCOL . SITE_ROOT . '/' . FAVICON, $data);
    $data = $extra_header_tags != '' ? str_replace('{{EXTRA_HEADER_TAGS}}', $extra_header_tags, $data) : str_replace('{{EXTRA_HEADER_TAGS}}', '', $data);
    echo $data . "\n\n";
}

function get_footer (array $dependencies = [] , string $extra_js = '') : void {
    global $database;
    global $session;
    $file = fopen(__DIR__ . '/../templates/footer.php', 'r');
    $data = fread($file, filesize(__DIR__ . '/../templates/footer.php'));
    fclose($file);
    $js = '';
    foreach ($dependencies as $dep) {
        $js .= (DEBUG ? str_replace('{{VERSION}}', '?v=' . time(), $dep) : str_replace('{{VERSION}}', '', $dep)) . "\n";
    }
    $data = str_replace('{{PLUGINS_JS}}', $js, $data);
    if ($session->message != "") {
        $alert_msg = $database->escape_string($session->message);
        $notifi_js = "Swal.fire({
            position: 'bottom-end',
            icon: '" . $_SESSION['msg_type'] . "',
            title: '$alert_msg',
            showConfirmButton: false,
            timer: " . $_SESSION['msg_timer'] . "
          })";
        $data = str_replace('{{NOTIFI_JS}}', "<script>\n$notifi_js\n</script>", $data);
    } else {
        $data = str_replace('{{NOTIFI_JS}}', '', $data);
    }
    
    $data = $extra_js != '' ? str_replace('{{EXTRA_JS}}', "<script>\n$extra_js\n</script>", $data) : str_replace('{{EXTRA_JS}}', '', $data);
    echo "\n" . $data;
}

function set_noti(string $text, string $icon = "success", int $timer = 5555) : void {
    $_SESSION["message"] = $text;
    $_SESSION["msg_type"] = $icon;
    $_SESSION["msg_timer"] = $timer;
}

function welcome_mail(string $name, string $email, string $pass, $app_info) : bool {
    return mail(
        $email, 
        "Welcome to " . $app_info->name, 
        "Dear $name, welcome to " . $app_info->name . ". Your login email is: $email and password is: $pass. Use these credentials to login into " . $app_info->name . " at " . PROTOCOL . SITE_ROOT . "login"
    );
}

function distance($lat1, $lat2, $lon1, $lon2) : float {
    $longi1 = deg2rad($lon1); 
    $longi2 = deg2rad($lon2); 
    $lati1 = deg2rad($lat1); 
    $lati2 = deg2rad($lat2); 
            
    //Haversine Formula 
    $difflong = $longi2 - $longi1; 
    $difflat = $lati2 - $lati1; 
    $val = pow(sin($difflat/2), 2) + cos($lati1) * cos($lati2) * pow(sin($difflong/2), 2);   
    return number_format(6378.8 * (2 * asin(sqrt($val))), 2);
}

function change_env(string $name, string $new_value) : bool {
    $new_value = str_replace("=", "", htmlspecialchars($new_value));
    $env_file = __DIR__ . "/.zisunal";
    $handle = fopen($env_file, "r");
    $data = fread($handle, filesize($env_file));
    fclose($handle);
    $lines = explode("\n", $data);
    $new_data = "";
    foreach ($lines as $index=>$line) {
        $vars = explode("=", $line);
        if ($index == 0) {
            if ($vars[0] == $name) {
                $new_data .= "$vars[0]=$new_value";
            } else {
                $new_data .= "$vars[0]=$vars[1]";
            }
        } else {
            if ($vars[0] == $name) {
                $new_data .= "\n$vars[0]=$new_value";
            } else {
                $new_data .= "\n$vars[0]=$vars[1]";
            }
        }
    }
    try {
        $rt_handle = fopen($env_file, "w");
        $rt_status = fwrite($rt_handle, $new_data);
        fclose($rt_handle);
        if (!$rt_status) {
            throw new Exception("Zisunal file permission error", 101);
            return false;
        } else {
            return true;
        }
    } catch (Exception $e) {
        return false;
    }
}

function include_config (string $directory) : void {
    $dir = __DIR__ . "/../" . $directory . "/";
    $files = scandir($dir);
    unset($files[0]);
    unset($files[1]);
    unset($files[2]);
    foreach ($files as $file) {
        require_once $dir . $file;
    }
}

function view(string $filename, array $args) : void {
    $_args = $args;
    $view_file = __DIR__ . "/../views/" . $filename . ".php";
    require_once $view_file;
}

function replace_between($str, $needle_start, $needle_end, $replacement) {
    $pos = strpos($str, $needle_start);
    $start = $pos === false ? 0 : $pos + strlen($needle_start);

    $pos = strpos($str, $needle_end, $start);
    $end = $pos === false ? strlen($str) : $pos;

    return substr_replace($str, $replacement, $start, $end - $start);
}

function replace_with($str, $needle_start, $needle_end, $replacement) {
    $pos = strpos($str, $needle_start);
    $start = $pos === false ? 0 : $pos + strlen($needle_start) -1;

    $pos = strpos($str, $needle_end, $start);
    $end = $pos === false ? strlen($str) : $pos + 1;

    return substr_replace($str, $replacement, $start, $end - $start);
}

function get_between($str, $needle_start, $needle_end) {
    $pos = strpos($str, $needle_start);
    $start = $pos === false ? 0 : $pos + strlen($needle_start);

    $pos = strpos($str, $needle_end, $start);
    $end = $pos === false ? strlen($str) : $pos;

    return substr($str, $start, $end - $start);
}
