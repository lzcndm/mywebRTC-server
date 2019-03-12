<?php
/**
 * Created by PhpStorm.
 * User: zengbs
 * Date: 2018/12/17
 * Time: 12:00 PM
 */

namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;

class WebSocket extends Controller
{
    public function index()
    {
        $content = file_get_contents(__DIR__.'/webscoket.html');
        $this->response()->write($content);
    }

    public function hello()
    {
        $this->writeJson('hello easyswoole');
    }
}