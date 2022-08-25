<?php
class Blog_controller {

    public static function blog (array $args = [])
    {
        return view('blog', $args);
    }

    public static function blog_author (array $args = [])
    {
        return view('blog-author', $args);
    }

}