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

use app\store\service\GoodsService;
use controller\BasicAdmin;
use service\DataService;
use service\ToolsService;
use think\Db;
use think\exception\HttpResponseException;

/**
 * 商店商品管理
 * Class Goods
 * @package app\store\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/03/27 14:43
 */
class Goods extends BasicAdmin
{

    /**
     * 定义当前操作表名
     * @var string
     */
    public $table = 'StoreGoods';

    /**
     * 普通商品
     * @return array|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $this->title = '赛事管理';
        $get = $this->request->get();

        $where = array();
        $where[] = array('status','=',1);
        empty($get['title']) ? '' : $where[] = array('name','like','%'.$get['title'].'%');
        //empty($get['cate_id']) ? '' : $where[] = array('cate_id','=',$get['cate_id']);
        if(!empty($get['create_time'])){
            list($start, $end) = explode(' - ', $get['create_time']);
            $where[] = array('create_time','between',array($start,$end));
        }
        $db = Db::name('signup_compete')->where($where)->order('id','desc');
        return parent::_list($db);
    }

    /**
     * 商城数据处理
     * @param array $data
     */
    protected function _data_filter(&$data)
    {
        /*$result = GoodsService::buildGoodsList($data);
        $this->assign([
            'brands' => $result['brand'],
            'cates'  => ToolsService::arr2table($result['cate']),
        ]);*/
    }

    /**
     * 添加商品
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    public function add()
    {
        if ($this->request->isGet()) {
            $this->title = '添加赛事';
            $this->_form_assign();
            return $this->_form($this->table, 'form');
        }

        try {
            //添加赛事数据
            $data = array();
            $data['title'] = $this->request->post('title', '');
            $data['title_second'] = $this->request->post('title_second', '');
            $data['cate_id'] = $this->request->post('cate_id', '');
            $data['img_url'] = $this->request->post('img_url', '');
            $data['describe'] = $this->request->post('describe', '');
            $data['price'] = $this->request->post('price', '');
            $data['min_age'] = $this->request->post('min_page', '');
            $data['max_age'] = $this->request->post('max_page', '');
            $data['car_address'] = $this->request->post('car_address', '');
            $data['receive_good_address'] = $this->request->post('receive_good_address', '');
            $data['start_time'] = $this->request->post('start_time', '');
            $data['end_time'] = $this->request->post('end_time', '');
            $data['signup_end_time'] = $this->request->post('signup_end_time', '');
            $data['people_num'] = $this->request->post('people_num', '');
            $data['content'] = $this->request->post('content', '');
            $data['cate_id'] = $this->request->post('cate_id', '');
            $data['compete_time'] = $this->request->post('compete_time', '');

            $compete_info['name'] = $data['title'];
            $compete_info['img_url'] = $data['img_url'];
            $compete_info['describe'] = $data['describe'];
            $compete_info['price'] = bcmul($data['price'],100);
            $compete_info['age'] = json(array($data['min_age'],$data['max_age']));
            $compete_info['create_time'] = date("Y-m-d H-i-s");
            $compete_info['cate_id'] = $data['cate_id'];
            $compete_info['compete_time'] = $data['compete_time'];

            $compete_notice['car_address'] = $data['car_address'];
            $compete_notice['receive_good_address'] = $data['receive_good_address'];
            $compete_notice['title'] = $data['title'];
            $compete_notice['title_second'] = $data['title_second'];
            $compete_notice['start_time'] = $data['start_time'];
            $compete_notice['end_time'] = $data['end_time'];
            $compete_notice['signup_end_time'] = $data['signup_end_time'];
            $compete_notice['people_num'] = $data['people_num'];
            $compete_notice['content'] = $data['content'];
            $compete_notice['create_time'] = date("Y-m-d H-i-s");

            $compete_id = Db::name('signup_compete')->insertGetId($compete_info);
            $compete_notice['compete_id'] = $compete_id;
            $info = Db::name('signup_compete_notice')->insert($compete_notice);

        } catch (HttpResponseException $exception) {
            return $exception->getResponse();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        list($base, $spm, $url) = [url('@admin'), $this->request->get('spm'), url('store/goods/index')];
        $this->success('添加赛事成功！', "{$base}#{$url}?spm={$spm}");
    }

    /**
     * 编辑商品
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        if (!$this->request->isPost()) {
            $compete_id = $this->request->get('id');
            $data = Db::name("signup_compete")->where(['id' => $compete_id, 'status' => '1'])->find();
            empty($data) && $this->error('需要编辑的赛事不存在！');
            $data_notice = Db::name("signup_compete_notice")->where(['compete_id' => $compete_id])->find();

            $this->_form_assign();
            return $this->fetch('form', ['vo' => array('compete_info' => $data,'compete_notice_info' => $data_notice), 'title' => '编辑赛事']);
        }
        try {

            $compete_id = $this->request->post('id');
            //添加赛事数据
            $data = array();
            $data['title'] = $this->request->post('title', '');
            $data['title_second'] = $this->request->post('title_second', '');
            $data['cate_id'] = $this->request->post('cate_id', '');
            $data['img_url'] = $this->request->post('img_url', '');
            $data['describe'] = $this->request->post('describe', '');
            $data['price'] = $this->request->post('price', '');
            $data['min_age'] = $this->request->post('min_page', '');
            $data['max_age'] = $this->request->post('max_page', '');
            $data['car_address'] = $this->request->post('car_address', '');
            $data['receive_good_address'] = $this->request->post('receive_good_address', '');
            $data['start_time'] = $this->request->post('start_time', '');
            $data['end_time'] = $this->request->post('end_time', '');
            $data['signup_end_time'] = $this->request->post('signup_end_time', '');
            $data['people_num'] = $this->request->post('people_num', '');
            $data['content'] = $this->request->post('content', '');
            $data['cate_id'] = $this->request->post('cate_id', '');
            $data['compete_time'] = $this->request->post('compete_time', '');

            $compete_info['name'] = $data['title'];
            $compete_info['img_url'] = $data['img_url'];
            $compete_info['describe'] = $data['describe'];
            $compete_info['price'] = bcmul($data['price'],100);
            $compete_info['age'] = json_encode(array($data['min_age'],$data['max_age']));
            $compete_info['create_time'] = date("Y-m-d H-i-s");
            $compete_info['cate_id'] = $data['cate_id'];
            $compete_info['compete_time'] = $data['compete_time'];

            $compete_notice['car_address'] = $data['car_address'];
            $compete_notice['receive_good_address'] = $data['receive_good_address'];
            $compete_notice['title'] = $data['title'];
            $compete_notice['title_second'] = $data['title_second'];
            $compete_notice['start_time'] = $data['start_time'];
            $compete_notice['end_time'] = $data['end_time'];
            $compete_notice['signup_end_time'] = $data['signup_end_time'];
            $compete_notice['people_num'] = $data['people_num'];
            $compete_notice['content'] = $data['content'];
            $compete_notice['create_time'] = date("Y-m-d H-i-s");

            Db::name('signup_compete')->where(array('id' => $compete_id))->update($compete_info);

            $compete_notice['compete_id'] = $compete_id;
            $info = Db::name('signup_compete_notice')->where(array('compete_id' => $compete_id))->update($compete_notice);


        } catch (HttpResponseException $exception) {
            return $exception->getResponse();
        } catch (\Exception $e) {
            $this->error('商品编辑失败，请稍候再试！' . $e->getMessage());
        }
        list($base, $spm, $url) = [url('@admin'), $this->request->get('spm'), url('store/goods/index')];
        $this->success('商品编辑成功！', "{$base}#{$url}?spm={$spm}");
    }

    /**
     * 表单数据处理
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _form_assign()
    {
        list($where, $order) = [['status' => '1', 'is_deleted' => '0'], 'sort asc,id desc'];
        $specs = (array)Db::name('StoreGoodsSpec')->where($where)->order($order)->select();
        $brands = (array)Db::name('StoreGoodsBrand')->where($where)->order($order)->select();
        $cates = (array)Db::name('StoreGoodsCate')->where($where)->order($order)->select();
        // 所有的商品信息
        $where = ['is_deleted' => '0', 'status' => '1'];
        $goodsListField = 'goods_id,goods_spec,goods_stock,goods_sale';
        $goods = Db::name('StoreGoods')->field('id,goods_title')->where($where)->select();
        $list = Db::name('StoreGoodsList')->field($goodsListField)->where($where)->select();
        foreach ($goods as $k => $g) {
            $goods[$k]['list'] = [];
            foreach ($list as $v) {
                ($g['id'] === $v['goods_id']) && $goods[$k]['list'][] = $v;
            }
        }
        array_unshift($specs, ['spec_title' => ' - 不使用规格模板 -', 'spec_param' => '[]', 'id' => '0']);
        $this->assign([
            'specs'  => $specs,
            'cates'  => ToolsService::arr2table($cates),
            'brands' => $brands,
            'all'    => $goods,
        ]);
    }

    /**
     * 读取POST表单数据
     * @return array
     */
    protected function _form_build_data()
    {
        list($main, $list, $post, $verify) = [[], [], $this->request->post(), false];

        empty($post['goods_logo']) && $this->error('商品LOGO不能为空，请上传后再提交数据！');

        // 商品主数据组装
        $main['cate_id'] = $this->request->post('cate_id', '0');
        $main['spec_id'] = $this->request->post('spec_id', '0');
        $main['brand_id'] = $this->request->post('brand_id', '0');
        $main['goods_logo'] = $this->request->post('goods_logo', '');
        $main['goods_title'] = $this->request->post('goods_title', '');
        $main['goods_video'] = $this->request->post('goods_video', '');
        $main['goods_image'] = $this->request->post('goods_image', '');
        $main['goods_desc'] = $this->request->post('goods_desc', '', null);
        $main['goods_content'] = $this->request->post('goods_content', '');
        $main['tags_id'] = ',' . join(',', isset($post['tags_id']) ? $post['tags_id'] : []) . ',';
        // 商品从数据组装
        if (!empty($post['goods_spec'])) {
            foreach ($post['goods_spec'] as $key => $value) {
                $goods = [];
                $goods['goods_spec'] = $value;
                $goods['market_price'] = $post['market_price'][$key];
                $goods['selling_price'] = $post['selling_price'][$key];
                $goods['status'] = intval(!empty($post['spec_status'][$key]));
                !empty($goods['status']) && $verify = true;
                $list[] = $goods;
            }
        } else {
            $this->error('没有商品规格或套餐信息哦！');
        }
        !$verify && $this->error('没有设置有效的商品规格！');
        return ['main' => $main, 'list' => $list];
    }

    /**
     * 商品库存信息更新
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function stock()
    {
        if (!$this->request->post()) {
            $goods_id = $this->request->get('id');
            $goods = Db::name('StoreGoods')->where(['id' => $goods_id, 'is_deleted' => '0'])->find();
            empty($goods) && $this->error('该商品无法操作入库操作！');
            $where = ['goods_id' => $goods_id, 'status' => '1', 'is_deleted' => '0'];
            $goods['list'] = Db::name('StoreGoodsList')->where($where)->select();
            return $this->fetch('', ['vo' => $goods]);
        }
        // 入库保存
        $goods_id = $this->request->post('id');
        list($post, $data) = [$this->request->post(), []];
        foreach ($post['spec'] as $key => $spec) {
            if ($post['stock'][$key] > 0) {
                $data[] = [
                    'goods_stock' => $post['stock'][$key],
                    'stock_desc'  => $this->request->post('desc'),
                    'goods_spec'  => $spec, 'goods_id' => $goods_id,
                ];
            }
        }
        empty($data) && $this->error('无需入库的数据哦！');
        if (Db::name('StoreGoodsStock')->insertAll($data) !== false) {
            GoodsService::syncGoodsStock($goods_id);
            $this->success('商品入库成功！', '');
        }
        $this->error('商品入库失败，请稍候再试！');
    }

    /**
     * 删除商品
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        $compete_id = $this->request->post('id');

        if(empty($compete_id)){
            $this->error("商品删除失败，请稍候再试！");
        }
        $info = Db::name('signup_compete')->where(array('id'=>$compete_id))->update(array('status' => 0));
        if(empty($info)){
            $this->error("商品删除失败，请稍候再试！");
        }else{
            $this->success("商品删除成功！", '');
        }
    }

    /**
     * 商品禁用
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function forbid()
    {
        if (DataService::update($this->table)) {
            $this->success("商品下架成功！", '');
        }
        $this->error("商品下架失败，请稍候再试！");
    }

    /**
     * 商品禁用
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function resume()
    {
        if (DataService::update($this->table)) {
            $this->success("商品上架成功！", '');
        }
        $this->error("商品上架失败，请稍候再试！");
    }

    public function know(){

        if (!$this->request->isPost()) {
            $know_content = Db::name('signup_system')->where(array('id' => 1))->find();
            $know_content = $know_content['know'];
            $this->assign('know',$know_content);
            return $this->fetch('');
        }else{
            try{
                $content = $this->request->post('content', '');
                $know_content = Db::name('signup_system')->where(array('id' => 1))->update(array('know' => $content));
            }catch (HttpResponseException $exception) {
                return $exception->getResponse();
            } catch (\Exception $e) {
                $this->error('编辑失败，请稍候再试！' . $e->getMessage());
            }
            list($base, $spm, $url) = [url('@admin'), $this->request->get('spm'), url('store/goods/know')];
            $this->success('修改成功！', "{$base}#{$url}?spm={$spm}");
        }

    }

    //首页轮播图设置
    public function banner(){
		if (!$this->request->isPost()) {
			$data = Db::name('signup_banner')->select();
			$this->assign('data',$data);
			return $this->fetch('', ['title' => '编辑首页轮播图']);
		}else{
			$compete_id = $_POST['compete_id'];
			$status = $_POST['status'];
			$img1 = $_POST['img_url1'];
			$img2 = $_POST['img_url2'];
			$img3 = $_POST['img_url3'];
			$img4 = $_POST['img_url4'];
			$img5 = $_POST['img_url5'];
			$data = array();
			foreach ($compete_id as $key => $value){
				$img_name = 'img'.($key + 1);
				$data[] = array(
					'id' => $key+1,
					'img_url' => $$img_name,
					'compete_id' => $value,
					'status' => in_array($key+1,$_POST['status']) ? 1 : 0,
				);
			}
			foreach ($data as $value){
				Db::name('signup_banner')->update($value);
			}
			list($base, $spm, $url) = [url('@admin'), $this->request->get('spm'), url('store/goods/banner')];
			$this->success('修改成功！', "{$base}#{$url}?spm={$spm}");
		}

	}

}
