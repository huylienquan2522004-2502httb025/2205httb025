<?php


require_once __DIR__ . '/../src/Helpers/Session.php';

Session::start();
Session::destroy();

header('Location: login.php');
exit;

