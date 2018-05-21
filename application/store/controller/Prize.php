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
use think\Db;


class Prize extends BasicAdmin
{
    public function index(){
		$this->title = '奖品设置';
		$get = $this->request->get();
		$where = array();
		$where[] = array('status','=',1);

		$db = Db::name('signup_prize')->where($where)->order('id','desc');
		return parent::_list($db);
    }

    public function bigpan(){
		if (!$this->request->isPost()) {
			$data = Db::name('signup_prize_active')->order('id','desc')->select();
			$this->assign('data',$data);
			return $this->fetch();
		}else{
			try {
				$data = array();
				$data['name'] = $this->request->post('name', '');
				$data['start_time'] = $this->request->post('start_time', '');
				$data['end_time'] = $this->request->post('end_time', '');
				$data['create_time'] = date('Y-m-d H-i-s');
				$info = Db::name('signup_prize_active')->insert($data);
			} catch (HttpResponseException $exception) {
				return $exception->getResponse();
			} catch (\Exception $e) {
				$this->error('添加失败，请稍候再试！' . $e->getMessage());
			}
		}
		list($base, $spm, $url) = [url('@admin'), $this->request->get('spm'), url('store/prize/bigpan')];
		$this->success('添加成功！', "{$base}#{$url}?spm={$spm}1");
    }

    public function prizeuser(){
        return $this->fetch();
    }
    public function  edit(){
		$get = $this->request->get();
		$prize_id = $get['id'];
		if (!$this->request->isPost()) {
			$data = Db::name('signup_prize')->find($prize_id);
			$this->assign('data',$data);
			return $this->fetch();
		}else{
			try {
				$data = array();
				$data['name'] = $this->request->post('name', '');
				$data['content'] = $this->request->post('content', '');
				$data['rate'] = $this->request->post('rate', '');
				$data['stock'] = $this->request->post('stock', '');
				$data['img_url'] = $this->request->post('img_url', '');
				//$data['id'] = $prize_id;
				$info = Db::name('signup_prize')->insert($data);
			} catch (HttpResponseException $exception) {
				return $exception->getResponse();
			} catch (\Exception $e) {
				$this->error('编辑失败，请稍候再试！' . $e->getMessage());
			}
		}
		list($base, $spm, $url) = [url('@admin'), $this->request->get('spm'), url('store/prize/index')];
		$this->success('商品编辑成功！', "{$base}#{$url}?spm={$spm}");
	}

	public function updateStatus(){
		$get = $this->request->get();
		$prize_id = $get['id'];
		$data = Db::name('signup_prize_active')->where(array('status' => 1))->update(array('status' => 0));
        Db::name('signup_prize_active')->where(array('id' => $prize_id))->update(array('status' => 1));
		echo 1;
	}
}