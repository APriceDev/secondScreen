<?php
$dev = 1;
require_once 'solarlite-bootstrap.php';

if (isset($argv[1])) {
    $user = $argv[1];
} else {
    die('Missing username parameter');
}

if (isset($argv[2])) {
    $email = $argv[2];
} else {
    die('Missing email parameter');
}

if (isset($argv[3])) {
    $password = $argv[3];
} else {
    die('Missing password parameter');
}


$model->users->createUser($user, $email, $password);
