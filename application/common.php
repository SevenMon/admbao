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

use service\DataService;
use service\NodeService;
use think\Db;

/**
 * 打印输出数据到文件
 * @param mixed $data 输出的数据
 * @param bool $force 强制替换
 * @param string|null $file
 */
function p($data, $force = false, $file = null)
{
    is_null($file) && $file = env('runtime_path') . date('Ymd') . '.txt';
    $str = (is_string($data) ? $data : (is_array($data) || is_object($data)) ? print_r($data, true) : var_export($data, true)) . PHP_EOL;
    $force ? file_put_contents($file, $str) : file_put_contents($file, $str, FILE_APPEND);
}

/**
 * RBAC节点权限验证
 * @param string $node
 * @return bool
 */
function auth($node)
{
    return NodeService::checkAuthNode($node);
}

/**
 * 设备或配置系统参数
 * @param string $name 参数名称
 * @param bool $value 默认是null为获取值，否则为更新
 * @return string|bool
 * @throws \think\Exception
 * @throws \think\exception\PDOException
 */
function sysconf($name, $value = null)
{
    static $config = [];
    if ($value !== null) {
        list($config, $data) = [[], ['name' => $name, 'value' => $value]];
        return DataService::save('SystemConfig', $data, 'name');
    }
    if (empty($config)) {
        $config = Db::name('SystemConfig')->column('name,value');
    }
    return isset($config[$name]) ? $config[$name] : '';
}

/**
 * 日期格式标准输出
 * @param string $datetime 输入日期
 * @param string $format 输出格式
 * @return false|string
 */
function format_datetime($datetime, $format = 'Y年m月d日 H:i:s')
{
    return date($format, strtotime($datetime));
}

/**
 * UTF8字符串加密
 * @param string $string
 * @return string
 */
function encode($string)
{
    list($chars, $length) = ['', strlen($string = iconv('utf-8', 'gbk', $string))];
    for ($i = 0; $i < $length; $i++) {
        $chars .= str_pad(base_convert(ord($string[$i]), 10, 36), 2, 0, 0);
    }
    return $chars;
}

/**
 * UTF8字符串解密
 * @param string $string
 * @return string
 */
function decode($string)
{
    $chars = '';
    foreach (str_split($string, 2) as $char) {
        $chars .= chr(intval(base_convert($char, 36, 10)));
    }
    return iconv('gbk', 'utf-8', $chars);
}

/**
 * 下载远程文件到本地
 * @param string $url 远程图片地址
 * @return string
 */
function local_image($url)
{
    return \service\FileService::download($url)['url'];
}

//根据competeid 获取name
function getCompeteInfo($compete_id){
	$name = Db::name('signup_compete')->where(array('id' => $compete_id))->column('name');
	if(empty($name)){
		return false;
	}
	return $name[0];
}
//根据competeid 获取name
function getCompeteTime($compete_id){
    $time = Db::name('signup_compete')->where(array('id' => $compete_id))->column('compete_time');
    if(empty($time)){
        return false;
    }
    return $time[0];
}

function getCompeteDes($compete_id){
	$describe = Db::name('signup_compete')->where(array('id' => $compete_id))->column('describe');
	if(empty($describe)){
		return false;
	}
	return $describe[0];
}

//根据id 获取活动名字
function getActiveName($active_id){
	$name = Db::name('signup_prize_active')->where(array('id' => $active_id))->column('name');
	return $name[0];
}

//根据用户id 获取用户 姓名电话昵称
function getUserInfo($user_id){
	$user_info = Db::name('signup_user')->find($user_id);
	$user_watch_info = Db::name('wechat_fans')->find($user_info['wechat_id']);
	return '姓名：'.$user_info['name']."<br/>电话：".$user_info['phone']."<br/>昵称：".$user_watch_info['nickname'];
}
//获取用户信息
function getUserAllInfo($user_id){
	$user_info = Db::name('signup_user')->find($user_id);
	$user_watch_info = Db::name('wechat_fans')->find($user_info['wechat_id']);
	return array(
		'user_info' => $user_info,
		'user_watch_info' => $user_watch_info,
	);
}
//判断是否可以报名
function signupOr($compete_id){
    $data_notice = Db::name('signup_compete_notice')->where(array('compete_id' => $compete_id))->find();
    $data = Db::name('signup_compete')->find($compete_id);
    if($data_notice['people_num'] >= $data['sell_num']){
        return true;
    }else{
        return false;
    }
}
