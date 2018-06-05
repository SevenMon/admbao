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
use Env;

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
		//获取下拉框
		$select = Db::name('signup_system')->find(5);
		$select = $select['know'];
		$select = explode('|',$select);
		$this->assign('select',$select);
		
        $get = $this->request->get();

        $where = array();

        $where[] = array('status','=',1);

		
		if(!empty($get['good_recive_address'])){
			$good_where[] = array('receive_good_address','like','%'.$get['good_recive_address'].'%');
			$order_id_array = Db::name('signup_order_people_info')->where($good_where)->column('order_id');
            $where[] = array('id','in',$order_id_array);
        }
        $this->assign('good_recive_address',empty($get['good_recive_address'])?'':$get['good_recive_address']);
		
		if(!empty($get['code'])){
            $where[] = array('code','like','%'.$get['code'].'%');

        }
        $this->assign('code',empty($get['code'])?'':$get['code']);
		
        if(!empty($get['order_num'])){
            $where[] = array('order_num','like','%'.$get['order_num'].'%');

        }
        $this->assign('order_num',empty($get['order_num'])?'':$get['order_num']);

        if(!empty($get['create_time'])){
            list($start, $end) = explode(' - ', $get['create_time']);
            $where[] = array('create_time','between',array($start,$end));

        }
        $this->assign('create_time',empty($get['create_time'])?'':$get['create_time']);

        if(!empty($get['titles'])){
			$compete_id_array = array();
        	$compete_where = array();
			$compete_where[] = array('name','like','%'.$get['titles'].'%');
			$compete_where[] = array('status','=',1);
        	$compete_id_array = Db::name('signup_compete')->where($compete_where)->column('id');
			//$compete_id_str = implode(",", $compete_id_array);
			$where[] = array('compete_id','in',$compete_id_array);

		}
        $this->assign('titles',empty($get['titles'])?'':$get['titles']);

		if(!empty($get['user_nickname'])){
			$user_id_array = array();
			$user_where = array();
			$user_where[] = array('wechat_fans.nickname','like','%'.$get['user_nickname'].'%');
			$user_where[] = array('signup_user.name','like','%'.$get['user_nickname'].'%');
			$user_id_array = Db::table('signup_user')->leftjoin('wechat_fans','wechat_fans.id = signup_user.wechat_id')->whereOr($user_where)->column('signup_user.id');
			//$compete_id_str = implode(",", $compete_id_array);
			$where[] = array('user_id','in',$user_id_array);

		}
        $this->assign('user_nickname',empty($get['user_nickname'])?'':$get['user_nickname']);
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

    public function export1()
    {
        $get = $this->request->get();

        $where = array();

        $where[] = array('status','=',1);

        if(!empty($get['order_num'])){
            $where[] = array('order_num','like','%'.$get['order_num'].'%');

        }

        if(!empty($get['create_time'])){
            list($start, $end) = explode(' - ', $get['create_time']);
            $where[] = array('create_time','between',array($start,$end));

        }

        if(!empty($get['titles'])){
            $compete_id_array = array();
            $compete_where = array();
            $compete_where[] = array('name','like','%'.$get['titles'].'%');
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
        $data = Db::name('signup_order')->where($where)->order('id','desc')->select();

        $result_data = array();
        foreach ($data as $key => $value){
            $people_data = Db::name('signup_order_people_info')->where(array('order_id' => $value['id']))->select();
            $extra_info = json_decode($value['extra_info'],true);
            foreach ($people_data as $keyx => $valuex){
                $type_name = empty($value['type'])?'个人':'团体';
                $result_data[] = array(
                    $value['order_num'],$value['price']/100,$type_name,$extra_info['data']['name'],$value['people_num'],
                    $valuex['name'],$valuex['phone'],$valuex['sex'],$valuex['birth'],$valuex['id_card'],$valuex['country'],
                    $valuex['email'],$valuex['address'],$valuex['company'],$valuex['size'],$valuex['color'],
                    $valuex['emergency_person'],$valuex['emergency_phone'],$valuex['car_address'],$valuex['receive_good_address'],
                );
            }
        }

        $head = array(
            '订单号',
            '总金额',
            '订单类型',
            '赛事名字',
            '人数',
            '参赛者姓名',
            '电话',
            '性别',
            '出生日期',
            '身份证',
            '国籍',
            '邮箱',
            '居住地址',
            '工作单位',
            '服装尺寸',
            '服装颜色',
            '紧急联系人',
            '紧急联系人电话',
            '乘车摆渡地点',
            '领取参赛物品地点',
            );
        /*
        $head = array(
            '姓名',
            '电话',
            '性别',
            '出生日期',
            '身份证号',
            '国籍',
            '电子邮件',
            '居住地址',
            '工作单位',
            '服装尺寸（S/M/L/XL/XXM/L/XL/XXL）',
            '服装颜色（绿色/白色/黑色/黄色/蓝色）',
            '紧急联系人',
            '紧急联系人电话',
            '乘车摆渡地点（火车站/北站/花溪/南明/云岩）',
            '领取参赛物品地点（物流到家/现场自取）',
            );
            */

        //下载头.
        header('Content-Type: application/vnd.ms-excel;charset=gbk');
        header('Content-Disposition: attachment;filename="query.csv"');
        header('Cache-Control: max-age=0');
        //输出流;
        $file = 'php://output';

        $fp = fopen($file, 'a');
        $changCode = function ($changArr) {
            // 破Excel2003中文只支持GBK编码;
            foreach ($changArr as $k => $v) {
                $changArr [$k] = iconv('utf-8', 'gbk', $v);
            }
            //返回一个 索引数组;
            return array_values($changArr);
        };
        //写入头部;
        fputcsv($fp, $changCode($head));
        
        foreach ($result_data as $value){
            $value = $changCode($value);
            fputcsv($fp, $value);//写入数据;
        }
        

        flush();
        ob_flush();
        exit();

    }
	
	public function export(){
		$get = $this->request->get();
        $where = array();
        $where[] = array('status','=',1);
        if(!empty($get['order_num'])){
            $where[] = array('order_num','like','%'.$get['order_num'].'%');
        }
        if(!empty($get['create_time'])){
            list($start, $end) = explode(' - ', $get['create_time']);
            $where[] = array('create_time','between',array($start,$end));
        }
        if(!empty($get['titles'])){
            $compete_id_array = array();
            $compete_where = array();
            $compete_where[] = array('name','like','%'.$get['titles'].'%');
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
        $data = Db::name('signup_order')->where($where)->order('id','desc')->select();

        $result_data = array();
        foreach ($data as $key => $value){
            $people_data = Db::name('signup_order_people_info')->where(array('order_id' => $value['id']))->select();
            $extra_info = json_decode($value['extra_info'],true);
            foreach ($people_data as $keyx => $valuex){
                $type_name = empty($value['type'])?'个人':'团体';
                $result_data[] = array(
                    $value['order_num'],$value['price']/100,$type_name,$extra_info['data']['name'],$value['people_num'],
                    $valuex['name'],$valuex['phone'],$valuex['sex'],$valuex['birth'],$valuex['id_card'],$valuex['country'],
                    $valuex['email'],$valuex['address'],$valuex['size'],$valuex['color'],
                    $valuex['emergency_person'],$valuex['emergency_phone'],$valuex['car_address'],$valuex['receive_good_address']
                );
            }
        }

		include_once(Env::get('root_path').'application/common/PHPExcel/PHPExcel.php');
		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		//设置居中
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('楷体');
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '订单号');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '总金额');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '订单类型');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '赛事名字');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '人数');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', '参赛者姓名');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '电话');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', '性别');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', '出生日期');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', '身份证');
		$objPHPExcel->getActiveSheet()->setCellValue('K1', '国籍');
		$objPHPExcel->getActiveSheet()->setCellValue('L1', '邮箱');
		$objPHPExcel->getActiveSheet()->setCellValue('M1', '居住地址');
		$objPHPExcel->getActiveSheet()->setCellValue('N1', '服装尺寸');
		$objPHPExcel->getActiveSheet()->setCellValue('O1', '服装颜色');
		$objPHPExcel->getActiveSheet()->setCellValue('P1', '紧急联系人');
		$objPHPExcel->getActiveSheet()->setCellValue('Q1', '紧急联系人电话');
		$objPHPExcel->getActiveSheet()->setCellValue('R1', '乘车摆渡地点');
		$objPHPExcel->getActiveSheet()->setCellValue('S1', '领取参赛物品地点');

		$objPHPExcel->getActiveSheet()->getStyle('A1:S1')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A1:S1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:S1')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight('30');
		$payment = '';
		$statusC = '';
		$i=2;

		foreach ($result_data as $v) {
                $objPHPExcel->getActiveSheet()->setCellValue('A'.($i), $v[0]);               
                $objPHPExcel->getActiveSheet()->setCellValue('B'.($i), $v[1]);
                $objPHPExcel->getActiveSheet()->setCellValue('C'.($i), $v[2]);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.($i), $v[3]);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.($i), $v[4]);
                $objPHPExcel->getActiveSheet()->setCellValue('F'.($i), $v[5]);
                $objPHPExcel->getActiveSheet()->setCellValue('G'.($i), $v[6]);
                $objPHPExcel->getActiveSheet()->setCellValue('H'.($i), $v[7]);
                $objPHPExcel->getActiveSheet()->setCellValue('I'.($i), $v[8]);
                $objPHPExcel->getActiveSheet()->setCellValue('J'.($i), $v[9]);
                $objPHPExcel->getActiveSheet()->setCellValue('K'.($i), $v[10]);
                $objPHPExcel->getActiveSheet()->setCellValue('L'.($i), $v[11]);
                $objPHPExcel->getActiveSheet()->setCellValue('M'.($i), $v[12]);
                $objPHPExcel->getActiveSheet()->setCellValue('N'.($i), $v[13]);
                $objPHPExcel->getActiveSheet()->setCellValue('O'.($i), $v[14]);
                $objPHPExcel->getActiveSheet()->setCellValue('P'.($i), $v[15]);
                $objPHPExcel->getActiveSheet()->setCellValue('Q'.($i), $v[16]);
                $objPHPExcel->getActiveSheet()->setCellValue('R'.($i), $v[17]);
                $objPHPExcel->getActiveSheet()->setCellValue('S'.($i), $v[18]);               
                $i++;
        }
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="report-'.time().'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
	
	public function useCode(){
		$order_id = $_GET['order_id'];
		$data = Db::name('signup_order')->where(array('id' => $order_id))->find();
		if($data['code_status'] == 1){
		    echo "核销码已经使用过！";
		    return;
        }
		Db::name('signup_order')->where(array('id' => $order_id))->update(array('code_status' => 1));
		echo 1;
	}

	public function select(){
        $get = $this->request->get();
        $data = Db::name('signup_system')->order('id','asc')->select();
        $this->assign('data',$data);
        $this->assign('title','下拉框管理');
        if ($this->request->isGet()) {
            return $this->fetch();
        }else{
            Db::name('signup_system')->update(array('id' => 2,'know' => $this->request->post('size', '')));
            Db::name('signup_system')->update(array('id' => 3,'know' => $this->request->post('color', '')));
            Db::name('signup_system')->update(array('id' => 4,'know' => $this->request->post('car_address', '')));
            Db::name('signup_system')->update(array('id' => 5,'know' => $this->request->post('good_address', '')));
            list($base, $spm, $url) = [url('@admin'), $this->request->get('spm'), url('store/Order/select')];
            $this->success('保存成功！', "{$base}#{$url}?spm={$spm}");
        }

    }
}
