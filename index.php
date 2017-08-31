<?php
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
    $servicefd = $redis->get('service');
    if ($servicefd) {
        header('location:./chat.php');
    } else {
        header('location:./form.php');
    }
