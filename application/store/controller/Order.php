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
class Order extends BasicAdmin
{

    /**
     * 定义当前操作表名
     * @var string
     */
    public $table = 'StoreOrder';

    /**
     * 订单列表
     * @return array|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $this->title = '赛事订单';
        $get = $this->request->get();

        $where = array();

        $where[] = array('status','=',1);

        empty($get['order_num']) ? '' : $where[] = array('order_num','like','%'.$get['order_num'].'%');

        if(!empty($get['create_time'])){
            list($start, $end) = explode(' - ', $get['create_time']);
            $where[] = array('create_time','between',array($start,$end));
        }

        if(!empty($get['title'])){
			$compete_id_array = array();
        	$compete_where = array();
			$compete_where[] = array('name','like','%'.$get['title'].'%');
			$compete_where[] = array('status','=',1);
        	$compete_id_array = Db::name('signup_compete')->where($compete_where)->column('id');
			//$compete_id_str = implode(",", $compete_id_array);
			$where[] = array('compete_id','in',$compete_id_array);
		}

		if(!empty($get['user_nickname'])){
			$user_id_array = array();
			$user_where = array();
			$user_where[] = array('wechat_fans.nickname','like','%'.$get['user_nickname'].'%');
			$user_where[] = array('signup_user.name','like','%'.$get['user_nickname'].'%');
			$user_id_array = Db::table('signup_user')->leftjoin('wechat_fans','wechat_fans.id = signup_user.wechat_id')->whereOr($user_where)->column('signup_user.id');
			//$compete_id_str = implode(",", $compete_id_array);
			$where[] = array('user_id','in',$user_id_array);
		}
        $db = Db::name('signup_order')->where($where)->order('id','desc');
        return parent::_list($db);
    }

    public function  detail(){
		$get = $this->request->get();
		$order_id = $get['id'];
		$order_info = Db::name('signup_order')->find($order_id);
		$order_info['extra_info'] = json_decode($order_info['extra_info'],true);

		$order_people_info = Db::name('signup_order_people_info')->where(array('order_id'=>$order_id))->select();

		$user_info = Db::name('signup_user')->find($order_info['user_id']);
		$wechat_user_info = Db::name('wechat_fans')->find($user_info['wechat_id']);
		$this->assign('order_info',$order_info);
		$this->assign('order_people_info',$order_people_info);
		$this->assign('user_info',$user_info);
		$this->assign('wechat_user_info',$wechat_user_info);
		return $this->fetch('', ['title' => '订单详情']);
	}

    /**
     * 订单列表数据处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _data_filter(&$data)
    {
        OrderService::buildOrderList($data);
    }

    /**
     * 订单地址修改
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    public function address()
    {
        $order_no = $this->request->get('order_no');
        if ($this->request->isGet()) {
            $order = Db::name('StoreOrder')->where(['order_no' => $order_no])->find();
            empty($order) && $this->error('该订单无法进行地址修改，订单数据不存在！');
            $orderExpress = Db::name('StoreOrderExpress')->where(['order_no' => $order_no])->find();
            empty($orderExpress) && $this->error('该订单无法进行地址修改！');
            return $this->fetch('', $orderExpress);
        }
        $data = [
            'order_no' => $order_no,
            'username' => $this->request->post('express_username'),
            'phone'    => $this->request->post('express_phone'),
            'province' => $this->request->post('form_express_province'),
            'city'     => $this->request->post('form_express_city'),
            'area'     => $this->request->post('form_express_area'),
            'address'  => $this->request->post('express_address'),
            'desc'     => $this->request->post('express_desc'),
        ];
        if (DataService::save('StoreOrderExpress', $data, 'order_no')) {
            $this->success('收货地址修改成功！', '');
        }
        $this->error('收货地址修改失败，请稍候再试！');
    }


}
