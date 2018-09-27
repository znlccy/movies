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

    public function entry() {

    }

    public function save() {

    }

    /* 获取主播详情 */
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