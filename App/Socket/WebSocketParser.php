<?php
/**
 * Created by PhpStorm.
 * User: zengbs
 * Date: 2018/12/17
 * Time: 11:35 AM
 */

namespace App\Socket;


use App\Socket\Websocket\Index;
use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

class WebSocketParser implements ParserInterface
{
    public function decode($raw, $client): ?Caller
    {
        $json = json_decode($raw);
        $caller = new Caller();
        $caller->setControllerClass(Index::class);
        $caller->setAction($json->action);
        $caller->setArgs(isset($json->content) ? [$json->content] : [null]);
        return $caller;
    }

    public function encode(Response $response, $client): ?string
    {
        return $response->getMessage();
    }
}
