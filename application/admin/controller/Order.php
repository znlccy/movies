<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 16:41
 * Comment: 订单控制器
 */

namespace app\admin\controller;

use think\Request;
use app\admin\model\Order as OrderModel;
use app\admin\validate\Order as OrderValidate;

class Order extends BasisController {

    /* 声明订单模型 */
    protected $order_model;

    /* 声明订单验证器 */
    protected $order_validate;

    /* 声明订单分页器 */
    protected $order_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->order_model = new OrderModel();
        $this->order_validate = new OrderValidate();
        $this->order_page = config('pagination');
    }

    /* 订单列表 */
    public function entry() {

        /* 接收参数 */
        $id = request()->param('id');

    }

    /* 订单添加更新 */
    public function save() {

    }

    /* 订单详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->order_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->order_validate->getError()
            ]);
        }

        /* 返回结果 */
        $detail = $this->order_model->where('id', $id)->find();

        if ($detail) {
            return json([
                'code'      => '200',
                'message'   => '查询订单成功',
                'data'      => $detail
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询订单失败'
            ]);
        }
    }

    /* 删除订单 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->order_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->order_validate->getError()
            ]);
        }

        /* 返回结果 */
        $delete = $this->order_model->where('id', $id)->delete();

        if ($delete) {
            return json([
                'code'      => '200',
                'message'   => '删除订单成功'
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '删除数据失败'
            ]);
        }
    }
}
