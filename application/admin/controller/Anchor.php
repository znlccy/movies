<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:14
 * Comment: 主播控制器
 */

namespace app\admin\controller;

use think\Request;
use app\admin\model\Anchor as AnchorModel;
use app\admin\validate\Anchor as AnchorValidate;

class Anchor extends BasisController {

    /* 主播模型 */
    protected $anchor_model;

    /* 主播验证器 */
    protected $anchor_validate;

    /* 主播分页器 */
    protected $anchor_page;

    /* 默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->anchor_model = new AnchorModel();
        $this->anchor_validate = new AnchorValidate();
        $this->anchor_page = config('pagination');
    }

    /* 主播列表 */
    public function entry() {

        /* 接收参数 */
        $id = request()->param('id');
        $name = request()->param('name');
        $live_room = request()->param('live_room');
        $create_start = request()->param('create_start');
        $create_end = request()->param('create_end');
        $update_start = request()->param('update_start');
        $update_end = request()->param('update_end');
        $page_size = request()->param('page_size');
        $jump_page = request()->param('jump_page');

        /* 验证结果 */
        $validate_data = [
            'id'            => $id,
            'name'          => $name,
            'live_room'     => $live_room,
            'create_start'  => $create_start,
            'create_end'    => $create_end,
            'update_start'  => $update_start,
            'update_end'    => $update_end,
            'page_size'     => $page_size,
            'jump_page'     => $jump_page
        ];

        /* 验证结果 */
        $result = $this->anchor_validate->scene('entry')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->anchor_validate->getError()
            ]);
        }

        /* 筛选条件 */
        $conditions = [];

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($name) {
            $conditions['name'] = ['like', '%' . $name . '%'];
        }

        if ($live_room) {
            $conditions['live_room'] = ['like', '%' . $live_room . '%'];
        }

        if ($create_start && $create_end) {
            $conditions['create_time'] = ['between time', [$create_start, $create_end]];
        }

        if ($update_start && $update_end) {
            $conditions['update_time'] = ['between time', [$update_start, $update_end]];
        }

        /* 返回结果 */
        $anchor = $this->anchor_model
            ->where($conditions)
            ->order('id', 'desc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        if ($anchor) {
            return json([
                'code'      => '200',
                'message'   => '查询数据成功',
                'data'      => $anchor
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询数据失败'
            ]);
        }
    }

    /* 主播添加更新 */
    public function save() {

        /* 接收参数 */
        $id = request()->param('id');
        $name = request()->param('name');
        $live_room = request()->param('live_room');
        $avatar = request()->file('avatar');
        if ($avatar) {
            $info = $avatar->move(ROOT_PATH . 'public' . DS . 'images');
            if ($info) {
                //成功上传后，获取上传信息
                //输出文件保存路径
                $sub_path = str_replace('\\', '/', $info->getSaveName());
                $avatar  = '/images/' . $sub_path;
            }
        }

        /* 验证参数 */
        $validate_data = [
            'id'        => $id,
            'name'      => $name,
            'live_room' => $live_room,
            'avatar'    => $avatar
        ];

        /* 验证结果 */
        $result = $this->anchor_validate->scene('save')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->anchor_validate->getError()
            ]);
        }

        /* 返回结果 */
        if (empty($id) || is_null($id)) {
            $operator = $this->anchor_model->save($validate_data);
        } else {
            $operator = $this->anchor_model->save($validate_data,['id' => $id]);
        }

        if ($operator) {
            return json([
                'code'      => '200',
                'message'   => '数据操作成功'
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '数据操作失败'
            ]);
        }
    }

    /* 主播详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->anchor_validate->scene('detail')->check($validate_data);

        if ($result) {
            return json([
                'code'      => '401',
                'message'   => $this->anchor_validate->getError()
            ]);
        }

        /* 返回结果 */
        $detail = $this->anchor_model->where('id',$id)->find();

        if ($detail) {
            return json([
                'code'      => '200',
                'message'   => '查询主播成功',
                'data'      => $detail
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询主播失败'
            ]);
        }
    }

    /* 删除主播 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->anchor_validate->scene('delete')->check($validate_data);

        if ($result) {
            return json([
                'code'      => '401',
                'message'   => $this->anchor_validate->getError()
            ]);
        }

        /* 返回数据 */
        $delete = $this->anchor_model->where('id',$id)->delete();

        if ($delete) {
            return json([
                'code'      => '200',
                'message'   => '删除成功'
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '删除失败'
            ]);
        }
    }
}