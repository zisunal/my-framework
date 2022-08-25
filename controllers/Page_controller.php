<?php
class Page_controller {

    public static function about_contact (array $args = [])
    {
        return view('about_contact', $args);
    }

    public static function home (array $args = [])
    {
        return view('home', $args);
    }

}