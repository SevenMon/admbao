<?php

// +----------------------------------------------------------------------
// | Think.Admin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/Think.Admin
// +----------------------------------------------------------------------

namespace app\store\controller;

use app\store\service\OrderService;
use controller\BasicAdmin;
use service\DataService;
use think\Db;

/**
 * 商店订单管理
 * Class Order
 * @package app\store\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/03/27 14:43
 */
class User extends BasicAdmin{
    public function index(){
        /*
        $user_list = Db::name('signup_user')->order('id','desc')->select();
        foreach ($user_list as $key => $value){
            $user_list[$key]['watch_info'] = Db::name('wechat_fans')->find($value['wechat_id']);
        }
        */
        $where = array();
        $get = $this->request->get();
        if(!empty($get['nickname'])){
            $where[] = array('wechat_fans.nickname','like','%'.$get['nickname'].'%');

        }
        $this->assign('nickname',empty($get['nickname'])?'':$get['nickname']);

        if(!empty($get['name'])){
            $where[] = array('signup_user.name','like','%'.$get['name'].'%');

        }
        $this->assign('name',empty($get['name'])?'':$get['name']);

        if(!empty($get['phone'])){
            $where[] = array('signup_user.phone','=',$get['phone']);

        }
        $this->assign('phone',empty($get['phone'])?'':$get['phone']);


        $db = Db::name('signup_user')->field("*,signup_user.id user_id")->leftJoin('wechat_fans','signup_user.wechat_id=wechat_fans.id')->where($where)->order('signup_user.id','desc');
        return parent::_list($db);

    }
    public function detail(){
        $user_id = $_GET['user_id'];
        $data = getUserAllInfo($user_id);
        $this->assign('user_info',$data);
        $this->assign('title','用户详情');
        return $this->fetch();
    }
}