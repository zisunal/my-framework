<?php

    class Common{

        public $image;
        public $tmp_path;
        public $directory = "images" . DS . "temp";

        public $default_errors = array(
            UPLOAD_ERR_OK       => "Uploaded Successfully",
            UPLOAD_ERR_INI_SIZE => "Photo exceeds the max file size limit",
            UPLOAD_ERR_FORM_SIZE => "Photo exceeds the max file size limit",
            UPLOAD_ERR_PARTIAL => "Partially uploaded",
            UPLOAD_ERR_NO_FILE => "File can't be empty",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
            UPLOAD_ERR_EXTENSION => "A PHP extension stoped the file to upload"
        );
        public $errors = array();

        protected static function do_query($sql){
            global $database;
            $result_set = $database->query($sql);
            $obj_array = array();
            while($row = $database->fetch_array($result_set)){
                $obj_array[] = static::instant($row);
            }
            return $obj_array;
        }

        public static function find_all(){
            return static::do_query("SELECT * FROM " . static::$db_table);
        }

        public static function find_between(string $start, string $end, string $field = "", string $value = ""){
            $con = $field != '' && $value != '' ? "$field = '$value' AND " : "";
            return static::do_query("SELECT * FROM " . static::$db_table . " WHERE " . $con . " created_at BETWEEN '$start' AND '$end' ");
        }

        public static function find_all_desc(){
            return static::do_query("SELECT * FROM " . static::$db_table . " ORDER BY id DESC ");
        }

        public static function find_all_desc_limit($limit){
            return static::do_query("SELECT * FROM " . static::$db_table . " ORDER BY id DESC LIMIT $limit ");
        }

        public static function find_by_id($id){
            $array = static::do_query("SELECT * FROM " . static::$db_table . " WHERE id = $id ");
            return !empty($array) ? array_shift($array) : false;
        }

        public static function find_last(){
            $array = static::do_query("SELECT * FROM " . static::$db_table . " ORDER BY id DESC LIMIT 1 ");
            return !empty($array) ? array_shift($array) : false;
        }

        public static function find_without_id($id){
            return static::do_query("SELECT * FROM " . static::$db_table . " WHERE id != $id ");
        }

        public static function instant($row){
            $calling = get_called_class();
            $the_obj = new $calling;
            foreach($row as $property => $value){
                if($the_obj->has_the_property($property)){
                    $the_obj->$property = $value;
                }
            }
            return $the_obj;
        }

        private function has_the_property($property){
            $obj = get_object_vars($this);
            return array_key_exists($property,$obj);
        }

        public function create(){
            global $database;

            $properties = $this->clean_properties();

            $sql = "INSERT INTO " . static::$db_table . " (" . implode(", ",array_keys($properties)) . ") VALUES ('" . implode("', '",array_values($properties)) . "')";
            if($database->query($sql)){
                $this->id = $database->the_insert_id();
                return true;
            }
            else{
                return false;
            }
                       
        }

        public function update(){
            global $database;
            $properties = $this->clean_properties();
            $properties_pair = array();
            foreach ($properties as $key => $value) {
                $properties_pair[] = "$key='$value'";
            }

            $sql = "UPDATE " . static::$db_table . " SET " . implode(", ",$properties_pair) . " WHERE id = $this->id ";

            return ($database->query($sql)) ? true : false;

        }

        public function delete(){
            global $database;
            $sql = "DELETE FROM " . static::$db_table . " WHERE id = $this->id ";
            return ($database->query($sql)) ? true : false;

        }

        protected function properties(){
            $properties = array();
            foreach (static::$db_table_fields as $db_field) {
                if(property_exists($this,$db_field)){
                    $properties[$db_field] = $this->$db_field;
                }
            }
            return $properties;
        }

        protected function clean_properties(){
            global $database;
            $clean_properties = array();
            foreach ($this->properties() as $key => $value) {
                $clean_properties[$key] = $database->escape_string($value);
            }
            return $clean_properties;
        }

        public function set_image($file){

            if(empty($file) || !$file || !is_array($file)){
                $this->errors = "No file uploaded";
                return false;
            }
            elseif($file['error'] != 0){
                $this->errors = $this->default_errors[$file['error']];
                return false;
            }
            elseif($file['size'] > 5242880){
                $this->errors = "Image size must be less than 5 MB";
                return false;
            }
            elseif(strtolower(pathinfo($file['name'],PATHINFO_EXTENSION)) != "jpg" && strtolower(pathinfo($file['name'],PATHINFO_EXTENSION)) != "png" && strtolower(pathinfo($file['name'],PATHINFO_EXTENSION)) != "jpeg"){
                $this->errors = "File must be a jpg/png/jpeg image";
                return false;
                // (strtolower(pathinfo($file['name'],PATHINFO_EXTENSION))) !== 'jpg'
            }
            else{
                $this->errors = '';
                $this->image = basename(pathinfo($file['name'],PATHINFO_FILENAME)).rand(0,9999999999).".".basename(pathinfo($file['name'],PATHINFO_EXTENSION));
                $this->tmp_path = $file['tmp_name'];
                return true;
            }

        }

        public function save_image(){
            if($this->id){
                $this->update();
            }
            else{

                if(!empty($this->errors) || $this->errors != ''){
                    return false;
                }
                else{
                    $target_path = SITE_ROOT . $this->directory . DS . $this->image;
                    if(file_exists($target_path)){
                        $this->errors = $this->image . "is already exists";
                        return false;
                    }
                    elseif(move_uploaded_file($this->tmp_path,$target_path)){
                        unset($this->tmp_path);
                        return true;
                    }
                    else{
                        $this->errors = "Your uploading folder may not have permission to read and write.";
                        return false; 
                    }
                }
            }
        }

        public static function picture(){
            $calling = get_called_class();
            $the_obj = new $calling;
            return $the_obj->directory.DS.$the_obj->image;
        }

        public function last_id(){
            global $database;
            $result = $database->query("SELECT * FROM " . static::$db_table . " ORDER BY id DESC LIMIT 1 ");

            if (!$result) {
                die('Could not query:' . $database->conn->error);
            }
            else{
                while($row = $database->fetch_array($result)){
                    $id = $row['id'];
                }
                return $id;
            }
        }

        public function net(){
            global $database;
            $result = $database->query("SELECT * FROM " . static::$db_table);
            return $database->ct_row($result);
        }
        
        public static function find_except($field, $value){
            return static::do_query("SELECT * FROM " . static::$db_table . " WHERE $field != '$value' ");
        }

        public static function find_by($field, $value){
            return static::do_query("SELECT * FROM " . static::$db_table . " WHERE $field = '$value' ");
        }

        public static function multi_find(array $fields){
            $sql = "";
            $i = 0;
            foreach($fields as $k => $fd) {
                if($i == count($fields) - 1) {
                    $sql .= "$k = '$fd'";
                } else {
                    $sql .= " $k = '$fd' AND ";
                }
                $i++;
            }
            return static::do_query("SELECT * FROM " . static::$db_table . " WHERE $sql ");
        }

    }

    $common = new Common();

?>