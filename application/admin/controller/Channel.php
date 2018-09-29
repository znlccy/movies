<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/28
 * Time: 12:10
 * Comment: 渠道控制器
 */

namespace app\admin\controller;

use app\admin\model\Channel as ChannelModel;
use app\admin\validate\Channel as ChannelValidate;
use think\Request;

class Channel extends BasisController {

    /* 渠道模型 */
    protected $channel_model;

    /* 渠道验证器 */
    protected $channel_validate;

    /* 渠道分页 */
    protected $channel_page;

    /* 默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->channel_model = new ChannelModel();
        $this->channel_validate = new ChannelValidate();
        $this->channel_page = config('pagination');
    }

    /* 渠道列表 */
    public function entry() {

        /* 接收参数 */
        $id = request()->param('id');
        $name = request()->param('name');
        $status = request()->param('status');
        $create_start = request()->param('create_start');
        $create_end = request()->param('create_end');
        $update_start = request()->param('update_start');
        $update_end = request()->param('update_end');
        $page_size = request()->param('page_size');
        $jump_page = request()->param('jump_page');

        /* 验证参数 */
        $validate_data = [
            'id'            => $id,
            'name'          => $name,
            'status'        => $status,
            'create_start'  => $create_start,
            'create_end'    => $create_end,
            'update_start'  => $update_start,
            'update_end'    => $update_end,
            'page_size'     => $page_size,
            'jump_page'     => $jump_page
        ];

        /* 验证结果 */
        $result = $this->channel_validate->scene('entry')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->channel_validate->getError()
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

        if (is_null($status)) {
            $conditions['status'] = ['in',[0,1]];
        } else {
            switch ($status) {
                case 0:
                    $conditions['status'] = $status;
                    break;
                case 1:
                    $conditions['status'] = $status;
                    break;
                default:
                    break;
            }
        }

        if ($create_start && $create_end) {
            $conditions['create_time'] = ['between time', [$create_start, $create_end]];
        }

        if ($update_start && $update_end) {
            $conditions['update_time'] = ['between time', [$update_start, $update_end]];
        }

        /* 返回结果 */
        $channel = $this->channel_model
            ->where($conditions)
            ->order('id','desc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        if ($channel) {
            return json([
                'code'      => '200',
                'message'   => '查询信息成功',
                'data'      => $channel
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询信息失败'
            ]);
        }
    }

    /* 渠道添加更新 */
    public function save() {

        /* 接收参数 */
        $id = request()->param('id');
        $name = request()->param('name');
        $status = request()->param('status');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id,
            'name'      => $name,
            'status'    => $status
        ];

        /* 验证结果 */
        $result = $this->channel_validate->scene('save')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->channel_validate->getError()
            ]);
        }

        /* 返回结果 */
        if (empty($id) || is_null($id)) {
            $operator = $this->channel_model->save($validate_data);
        } else {
            $operator = $this->channel_model->save($validate_data, ['id' => $id]);
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

    /* 渠道详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->channel_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->channel_validate->getError()
            ]);
        }

        /* 返回结果 */
        $detail = $this->channel_model->where('id', $id)->find();

        if ($detail) {
            return json([
                'code'      => '200',
                'message'   => '查询消息成功',
                'data'      => $detail
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询消息失败'
            ]);
        }

    }

    /* 渠道删除 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->channel_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->channel_validate->getError()
            ]);
        }

        /* 返回结果 */
        $delete = $this->channel_model->where('id', $id)->delete();

        if ($delete) {
            return json([
                'code'      => '200',
                'message'   => '删除消息成功'
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '删除消息失败'
            ]);
        }
    }
}