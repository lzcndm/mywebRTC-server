<?php
/**
 * Created by PhpStorm.
 * User: zengbs
 * Date: 2018/12/22
 * Time: 5:10 PM
 */

namespace App\Socket;

use EasySwoole\FastCache\Cache;
use Swoole\Server;

class WebSocketEvent
{
    public function onClose(Server $server, int $fd, int $reactor)
    {
        $cache = Cache::getInstance();
        $users = $cache->keys();
        foreach ($users as $user) {
            if ($cache->get($user) === $fd) {
                $offlineUser = $user;
            }
        }
        foreach ($users as $user) {
            $user_fd = $cache->get($user);
            if ($user_fd === $fd) {
                $cache->unset($user);
            }
            if ($server->exist($user_fd)) {
                $server->push($user_fd, json_encode([
                    'action' => 'userOffline',
                    'content' => $offlineUser ?? null
                ]));
            }
        }
    }

}