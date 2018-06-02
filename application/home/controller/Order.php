<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/16
 * Time: 10:06
 */
namespace app\home\controller;

use think\Controller;
use think\Db;
use think\facade\Config;

class Order extends Base {

	public function addOrder(){
		$user_id = $this->userId;

        $compete_id = $this->request->get('id', '');
        $data_notice = Db::name('signup_compete_notice')->where(array('compete_id' => $compete_id))->find();
        $data = Db::name('signup_compete')->find($compete_id);
        if (!$this->request->isPost()) {
			$user_info = getUserAllInfo($user_id);

			$select_info = getselect();

			$this->assign('select_info',$select_info);
			$this->assign('user_info',$user_info);
            $this->assign('id',$compete_id);
            $this->assign('data',$data);
            $this->assign('data_notice',$data_notice);
			//下载文件url
			$url = Config::get('common.uri')."home/user/downSignupTable";
			$this->assign('down_url',$url);
            return $this->fetch('index');
            exit();
        }else{
            //var_dump($_REQUEST);
            $order = array(
                'order_num' => time().rand(1000,9999),
                'price' => $data['price'],
                'type' => 0,
                'user_id' => $this->userId,
                'compete_id' => $compete_id,
                'people_num' => 1,
                'extra_info' => json_encode(array(
                    'data' => $data,
                    'data_notice' => $data_notice
                )),
                'attachment' => '',
                'create_time' => date("Y-m-d H-i-s"),
                'status' => 0,
				'code' => randcode(5)
            );

            $order_id = Db::name('signup_order')->insertGetId($order);
            Db::name('signup_compete')->where(array('id' => $data['id']))->update(array('sell_num' => ($data['sell_num'] + 1)));

            $order_people = array(
                'order_id' => $order_id,
                'name' => $_POST['name'],
                'phone' => $_POST['phone'],
                'sex' => $_POST['sex'],
                //'birth' => $_POST['birth'],
                'id_card' => $_POST['id_card'],
                'country' => $_POST['country'],
                'email' => $_POST['email'],
                'address' => $_POST['address'],
                'company' => $_POST['company'],
                'size' => $_POST['size'],
                'color' => $_POST['color'],
                'emergency_person' => $_POST['emergency_person'],
                'emergency_phone' => $_POST['emergency_phone'],
                'car_address' => $_POST['car_address'],
                'receive_good_address' => $_POST['receive_good_address'],
                'create_time' => date("Y-m-d H-i-s")
            );
            Db::name('signup_order_people_info')->insert($order_people);
			$url = Config::get('common.uri')."home/payment/callback?order_id=".$order_id;
			header("location: $url");

        }
    }

    public function addOrderTeam(){
        $user_id = $this->userId;

        $compete_id = $this->request->get('id', '');
        $data_notice = Db::name('signup_compete_notice')->where(array('compete_id' => $compete_id))->find();
        $data = Db::name('signup_compete')->find($compete_id);
        $select_info = getselect();

        $this->assign('select_info',$select_info);
        if (!$this->request->isPost()) {
            $user_info = getUserAllInfo($user_id);

            $this->assign('user_info',$user_info);
            $this->assign('id',$compete_id);
            $this->assign('data',$data);
            $this->assign('data_notice',$data_notice);
            //下载文件url
            $url = Config::get('common.uri')."home/user/downSignupTable";
            $this->assign('down_url',$url);
            return $this->fetch('index');
            exit();
        }else{
            //var_dump($_REQUEST);
            $order = array(
                'order_num' => time().rand(1000,9999),
                'price' => $data['price'],
                'type' => 0,
                'user_id' => $this->userId,
                'compete_id' => $compete_id,
                'people_num' => 1,
                'extra_info' => json_encode(array(
                    'data' => $data,
                    'data_notice' => $data_notice
                )),
                'attachment' => '',
                'create_time' => date("Y-m-d H-i-s"),
                'status' => 0,
                'code' => randcode(5)
            );

            $order_id = Db::name('signup_order')->insertGetId($order);
            Db::name('signup_compete')->where(array('id' => $data['id']))->update(array('sell_num' => ($data['sell_num'] + 1)));

            $order_people = array(
                'order_id' => $order_id,
                'name' => $_POST['name'],
                'phone' => $_POST['phone'],
                'sex' => $_POST['sex'],
                //'birth' => $_POST['birth'],
                'id_card' => $_POST['id_card'],
                'country' => $_POST['country'],
                'email' => $_POST['email'],
                'address' => $_POST['address'],
                'company' => $_POST['company'],
                'size' => $_POST['size'],
                'color' => $_POST['color'],
                'emergency_person' => $_POST['emergency_person'],
                'emergency_phone' => $_POST['emergency_phone'],
                'car_address' => $_POST['car_address'],
                'receive_good_address' => $_POST['receive_good_address'],
                'create_time' => date("Y-m-d H-i-s")
            );
            Db::name('signup_order_people_info')->insert($order_people);
            $this->assign('order_id',$order_id);
            return $this->fetch('people');
        }
    }

    public function addPeople(){
        $order_id = $_GET['order_id'];
        $this->assign('order_id',$order_id);
        $select_info = getselect();

        $this->assign('select_info',$select_info);
        if ($this->request->isPost()) {
            $order_people = array(
                'order_id' => $order_id,
                'name' => $_POST['name'],
                'phone' => $_POST['phone'],
                'sex' => $_POST['sex'],
                //'birth' => $_POST['birth'],
                'id_card' => $_POST['id_card'],
                'country' => $_POST['country'],
                'email' => $_POST['email'],
                'address' => $_POST['address'],
                'company' => $_POST['company'],
                'size' => $_POST['size'],
                'color' => $_POST['color'],
                'emergency_person' => $_POST['emergency_person'],
                'emergency_phone' => $_POST['emergency_phone'],
                'car_address' => $_POST['car_address'],
                'receive_good_address' => $_POST['receive_good_address'],
                'create_time' => date("Y-m-d H-i-s")
            );
            Db::name('signup_order_people_info')->insert($order_people);
            if($_POST['act'] == 'pay'){
                //只有团体报名的支付才走这个方法  更新order 的price
                $order_info =  Db::name('signup_order')->find($order_id);
                $num = Db::name('signup_order_people_info')->where(array('order_id' => $order_id))->count();
                Db::name('signup_order')->where(array('id' => $order_id))->update(array('price' => $order_info['price'] * $num,'type' => 1));
                $url = Config::get('common.uri')."home/payment/callback?order_id=".$order_id;
                header("location: $url");
                exit();
            }
        }
        return $this->fetch('people');
    }
    public function pay(){
        $order_id = $_GET['order_id'];
        //只有团体报名的支付才走这个方法  更新order 的price
        $order_info =  Db::name('signup_order')->find($order_id);
        $num = Db::name('signup_order_people_info')->where(array('order_id' => $order_id))->count();
        Db::name('signup_order')->where(array('id' => $order_id))->update(array('price' => $order_info['price'] * $num));
        $url = Config::get('common.uri')."home/payment/callback?order_id=".$order_id;
        header("location: $url");
	}
	public function downSignupTable(){
		//$file_name = "signup";
		$file_name = "signup.csv";     //下载文件名
		$file_dir = "static/file/";        //下载文件存放目录
		//检查文件是否存在
		if (! file_exists ( $file_dir . $file_name )) {
			header('HTTP/1.1 404 NOT FOUND');
		} else {
			//以只读和二进制模式打开文件
			$file = fopen ( $file_dir . $file_name, "rb" );
			//告诉浏览器这是一个文件流格式的文件
			Header ( "Content-type: application/octet-stream" );
			//请求范围的度量单位
			Header ( "Accept-Ranges: bytes" );
			//Content-Length是指定包含于请求或响应中数据的字节长度
			Header ( "Accept-Length: " . filesize ( $file_dir . $file_name ) );
			//用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
			Header ( "Content-Disposition: attachment; filename=" . $file_name );
			//读取文件内容并直接输出到浏览器
			//echo fread ( $file, filesize ( $file_dir . $file_name ) );
			//fclose ( $file );
			readfile($file_dir . $file_name);
			exit ();
		}
	}
	public function downSignupTable1($file){

		$file = "static/file/signup.csv";
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="signup.csv"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}else{exit($file."文件不存在");}
	}
	public function uploadTable(){
		$user_id = $this->userId;
		//setlocale(LC_ALL, 'zh_CN');  //设置地区信息（地域信息）  
		// 获取表单上传文件 例如上传了001.jpg
    	$file = request()->file('file');
    	// 移动到框架应用根目录/uploads/ 目录下
    	$info = $file->validate(['ext'=>'csv'])->move( 'uploads/');
		if($info){
			// 成功上传后 获取上传信息
			// 输出 jpg
			$ext = $info->getExtension();
			// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
			$service_name = $info->getSaveName();
			// 输出 42a79759f284b767dfcb2a0197904287.jpg
			$file_name = $info->getFilename(); 

			$old_name = $_FILES["file"]["name"];

			$csv_file = fopen('uploads/'.$service_name,'r');   
			$people_info = array();
			while ($data = fgetcsv($csv_file)) { //每次读取CSV里面的一行内容  
			//print_r($data); //此为一个数组，要获得每一个数据，访问数组下标即可  
				$people = array();
				foreach ($data as $key => $value) {
					$value = iconv('gbk','utf-8//ignore',$value);
					$people[] = $value;
				}
				$people_info[] = $people;

			 }  
			fclose($csv_file);  
			$title = array(
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
			//检查数据 并且生成订单

			foreach ($title as $key => $value) {
				if($value != $people_info[0][$key]){
					$result = array(
						'status' => 0,
						'msg' => '文件格式错误，请重新编辑！（请用指定地点下载的表格进行填写）',
					);
					echo json_encode($result);					
					return;
				}
			}

			if(count($people_info) > 1){
				//生成订单
				$compete_id = $this->request->get('id', '');
		        $data_notice = Db::name('signup_compete_notice')->where(array('compete_id' => $compete_id))->find();
		        $data = Db::name('signup_compete')->find($compete_id);
				//判断库存是否充足
		        if($data_notice['people_num'] < ($data['sell_num'] + count($people_info) - 1)){		        
			        $result = array(
						'status' => 0,
						'msg' => '报名人数过多，超过了已经参赛人数!',
					);
					echo json_encode($result);					
					return;
			    }

		        $order = array(
	                'order_num' => time().rand(1000,9999),
	                'price' => $data['price'] * (count($people_info) - 1),
	                'type' => 0,
	                'user_id' => $this->userId,
	                'compete_id' => $compete_id,
	                'people_num' => 1,
	                'extra_info' => json_encode(array(
	                    'data' => $data,
	                    'data_notice' => $data_notice
	                )),
	                'attachment' => '',
	                'create_time' => date("Y-m-d H-i-s"),
	                'status' => 0,
					'code' => randcode(5)
            	);
	            $order_id = Db::name('signup_order')->insertGetId($order);
	            Db::name('signup_compete')->where(array('id' => $data['id']))->update(array('sell_num' => ($data['sell_num'] + count($people_info) - 1)));

				foreach ($people_info as $key => $value) {
					if($key == 0)continue;
					$order_people = array(
		                'order_id' => $order_id,
		                'name' => $value[0],
		                'phone' => $value[1],
		                'sex' => $value[2],
		                //'birth' => $value[3],
		                'id_card' => $value[4],
		                'country' => $value[5],
		                'email' => $value[6],
		                'address' => $value[7],
		                'company' => $value[8],
		                'size' => $value[9],
		                'color' => $value[10],
		                'emergency_person' => $value[11],
		                'emergency_phone' => $value[12],
		                'car_address' => $value[13],
		                'receive_good_address' => $value[14],
		                'create_time' => date("Y-m-d H-i-s")
		            );
		            Db::name('signup_order_people_info')->insert($order_people);
				}
				$result = array(
					'status' => 1,
					'msg' => '成功!',
					'data' => array(
							'order_id' => $order_id,
							'old_name' => $old_name,
							'people_num' => count($people_info) - 1,
							'prize' => $data['price'],
							'url' => Config::get('common.uri')."home/payment/callback?order_id=".$order_id
						)
				);
			}else{
				$result = array(
					'status' => 0,
					'msg' => '请按要求填写参赛人信息！'
				);
			}
			echo json_encode($result);
			//echo json_encode($goods_list);
			//echo 123123;
			
			
		}else{
			// 上传失败获取错误信息
			echo '上传失败，请稍后再试！';
		}
	}
}