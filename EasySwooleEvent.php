<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Socket\WebSocketEvent;
use App\Socket\WebSocketParser;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Socket\Dispatcher;
use Swoole\Server;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    /**
     * @param EventRegister $register
     * @throws \Exception
     */
    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
        $conf = new \EasySwoole\Socket\Config();
        $conf->setType($conf::WEB_SOCKET);
        $conf->setParser(new WebSocketParser());
        $dispatch = new Dispatcher($conf);
        $webSocketEvent = new WebSocketEvent();

        $register->set(EventRegister::onMessage, function (\swoole_websocket_server $server, \swoole_websocket_frame $frame) use ($dispatch) {
            $dispatch->dispatch($server, $frame->data, $frame);
        });
        $register->set(EventRegister::onClose, function (Server $server,int $fd, int $reactorId) use ($webSocketEvent) {
            $webSocketEvent->onClose($server,$fd,$reactorId);
        });
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }

    public static function onReceive(\swoole_server $server, int $fd, int $reactor_id, string $data):void
    {

    }

}