<?php
/*header('Location: '."/");
use google\appengine\api\users\UserService;
UserService::createLogoutUrl($_SERVER['REQUEST_URI']);*/
use google\appengine\api\users\User;
use google\appengine\api\users\UserService;

               UserService::createLogoutUrl('/');
?>