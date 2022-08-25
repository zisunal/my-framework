<?php
_APP->get('/', 'Page_controller::home');
_APP->get('/about/contact/{id}', 'Page_controller::about_contact');