<?php
/**
 * Created by PhpStorm.
 * User: zengbs
 * Date: 2018/12/20
 * Time: 12:36 PM
 */

namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{
    public function index()
    {
        $this->writeJson(200, 'hello');
    }
}