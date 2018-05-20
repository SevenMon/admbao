<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

namespace app\store\controller;


use controller\BasicAdmin;


class Prize extends BasicAdmin
{
    public function index(){
        echo "奖品页面";
    }

    public function bigpan(){
        echo "大转盘";
    }

    public function prizeUser(){
        echo "用户获奖页面";
    }
}