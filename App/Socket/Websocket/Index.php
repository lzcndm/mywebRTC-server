<?php
/**
 * Created by PhpStorm.
 * User: zengbs
 * Date: 2018/12/17
 * Time: 11:37 AM
 */

namespace App\Socket\Websocket;


use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\FastCache\Cache;
use EasySwoole\Socket\AbstractInterface\Controller;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;
use EasySwoole\Socket\Config;

class Index extends Controller
{
    private $cache;
    private $content;
    private $client;
    public function __construct(\swoole_server $server, Config $config, Caller $request, Response $response)
    {
        $this->cache = Cache::getInstance();
        $args = $request->getArgs();
        $this->content =array_shift( $args);
        $this->client = $request->getClient();
        parent::__construct($server, $config, $request, $response);
    }

    public function login()
    {
        $name = $this->content;
        $users = $this->getUsers();
        $login_fd = $this->client->getFd();

        if (in_array($name, $users)) {
            $this->response()->setMessage('someone has login');
        } else {
            $this->cache->set($name, $login_fd);
            $this->response()->setMessage(json_encode(['action' => 'login', 'content' => compact('name', 'login_fd')]));
            if (!empty($users)) {

                $server = ServerManager::getInstance()->getSwooleServer();
                foreach ($users as $user) {
                    $fd = $this->cache->get($user);
                    $server->push($fd, json_encode([
                        'action' => 'loginNotice',
                        'content' => compact('name', 'login_fd')
                    ]));
                }
            }
        }
    }

    public function exchangeDes()
    {
        $fd = $this->cache->get($this->content->target);
        $server = ServerManager::getInstance()->getSwooleServer();
        if (null == $fd) {
            $this->response()->setMessage(json_encode('target not found'));
            return ;
        }
        $server->push($fd,json_encode(['action' => __FUNCTION__, 'des' => $this->content->des, 'from' => $this->content->from]));
        $this->response()->setMessage(json_encode(['success']));
    }

    public function exchangeCandidate()
    {
        $fd = $this->cache->get($this->content->target);
        $server = ServerManager::getInstance()->getSwooleServer();

        if (null == $fd) {
            $this->response()->setMessage(json_encode(['err' => 'target not found']));
            return;
        }

        $server->push($fd, json_encode(['action' => __FUNCTION__, 'candidate' => $this->content->candidate, 'from' => $this->content->from]));
        $this->response()->setMessage(json_encode(['success']));
    }

    public function getOnlineUsers()
    {
        $users = $this->cache->keys();
        $this->response()->setMessage(json_encode([
            'action' => __FUNCTION__,
            'content' => $users
        ]));
    }

    private function getUsers()
    {
        return $this->cache->keys();
    }

    public function echo()
    {
        $this->response()->setMessage('fuck');
    }
}