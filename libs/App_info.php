<?php
class App_info extends Common{
    public static $db_table = "app_info";
    public static $db_table_fields = array("name","logo","ct_email","ct_phone","ct_addr","seo_tit","seo_des","seo_kw","seo_author","cp_text","cp_link","version","php_ver","created_at");
    public $id;
    public $name;
    public $logo;
    public $ct_email;
    public $ct_phone;
    public $ct_addr;
    public $seo_tit;
    public $seo_des;
    public $seo_kw;
    public $seo_author;
    public $cp_text;
    public $cp_link;
    public $version;
    public $php_ver;
    public $created_at;
    public $updated_at;

    public $directory = "assets/img/";
}

$app_info = new App_info();