<?php
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
    $ser = new swoole_websocket_server("172.17.46.31",8080);
    $ser->on('open', function($ser, $request) use($redis) {
    });
    $ser->on('message', function($ser, $request) use($redis) {
        $data = $request->data;
	$data = json_decode($data, true);
        if ($data['server'] == 'server') {
            $service = $redis->get('service');
            if (!empty($data['message'])) {
                $ser->push($data['guest'], $data['message']);
            } else {
                $redis->set('service', $request->fd);
                $data['data'] = 'new server';
                $data['guest'] = $redis->hgetall('guest');
                $data = json_encode($data);
                $ser->push($request->fd, $data);
            }
        } else if ($data['server'] == "guest") {
            $member = $redis->hget('guest', $request->fd);
            if (empty($member)) {
                $redis->hmset('guest', [$request->fd => $request->fd]);
                $ser->push($request->fd, 'hello world!');
                $data['message'] = "new guest";
            }
            $service = $redis->get('service');
            if (empty($service)) {
                $ser->push($request->fd, 'service is not online');
            } else {
                $message = json_encode(['guest' => $request->fd, 'data' =>$data['message']], JSON_UNESCAPED_UNICODE);
                $ser->push($service, $message);
            }
        }
    });
    $ser->on('close', function($ser, $fd) use($redis) {
	$service = $redis->get('service');
        if ($service == $fd)  {
            $redis->del('service');
        } else {
            $user = json_encode(['guest' => $fd, 'data' => 'leave guest']);
            $ser->push($service, $user);
            $redis->hdel('guest', $fd);
        } 
    });
    $ser->start();

