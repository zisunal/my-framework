<?php
_APP->get('/post/{name}/{id}', 'Blog_controller::blog');
_APP->get('/post/{name}/{id}/{author}', 'Blog_controller::blog_author');