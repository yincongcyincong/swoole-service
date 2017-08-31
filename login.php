<?php
    require('./mysql.php');
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
    //$ser = new swoole_websocket_server("172.17.46.31",8080);
    $servicefd = $redis->get('service');
    if ($servicefd) {
        return false;
    }
    $username = $_POST['username'];
    $password = $_POST['password'];
    $con = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
    $res = mysqli_query($con, "select * from user where username = '{$username}'");
    $data = mysqli_fetch_assoc($res);
    if($data['password'] == $password){
	session_start();
	$_SESSION['service'] = $data['id'];
    }
    header('location:./service.php');
