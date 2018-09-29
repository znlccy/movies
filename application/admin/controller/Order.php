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
use Zxing\Common\Detector\MathUtils;

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
        $order_no  = request()->param('order_no');
        $amount = request()->param('amount');
        $pay_start = request()->param('pay_start');
        $pay_end = request()->param('pay_end');
        $channel = request()->param('channel');
        $currency = request()->param('currency');
        $status = request()->param('status');
        $subject = request()->param('subject');
        $body = request()->param('body');
        $create_start = request()->param('create_start');
        $create_end = request()->param('create_end');
        $update_start = request()->param('update_start');
        $update_end = request()->param('update_end');
        $page_size = request()->param('page_size', $this->order_page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $this->order_page['JUMP_PAGE']);

        /* 验证参数 */
        $validate_data = [
            'id'            => $id,
            'order_no'      => $order_no,
            'amount'        => $amount,
            'pay_start'     => $pay_start,
            'pay_end'       => $pay_end,
            'channel'       => $channel,
            'currency'      => $currency,
            'status'        => $status,
            'subject'       => $subject,
            'body'          => $body,
            'create_start'  => $create_start,
            'create_end'    => $create_end,
            'update_start'  => $update_start,
            'update_end'    => $update_end,
            'page_size'     => $page_size,
            'jump_page'     => $jump_page
        ];

        /* 验证结果 */
        $result = $this->order_validate->scene('entry')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->order_validate->getError()
            ]);
        }

        /* 过滤参数 */
        $conditions = [];

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($order_no) {
            $conditions['order_no'] = ['like', '%'. $order_no .'%'];
        }

        if ($amount) {
            $conditions['amount'] = $amount;
        }

        if ($pay_start && $pay_end) {
            $conditions['pay_time'] = ['between time',[$pay_start, $pay_end]];
        }

        if ($channel) {
            $conditions['channel'] = ['like', '%' . $channel . '%'];
        }

        if ($currency) {
            $conditions['currency'] = ['like', '%' . $currency . '%'];
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

        if ($subject) {
            $conditions['subject'] = ['like', '%' . $subject . '%'];
        }

        if ($body) {
            $conditions['body'] = ['like', '%' . $body . '%'];
        }

        if ($create_start && $create_end) {
            $conditions['create_time'] = ['between time',[$create_start, $create_end]];
        }

        if ($update_start && $update_end) {
            $conditions['update_time'] = ['between time', [$update_start, $update_end]];
        }

        /* 返回结果 */
        $order = $this->order_model
            ->where($conditions)
            ->order('id', 'desc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        if ($order) {
            return json([
                'code'      => '200',
                'message'   => '查询消息成功',
                'data'      => $order
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询消息失败'
            ]);
        }
    }

    /* 订单添加更新 */
    public function save() {

        /* 接收参数 */
        $id = request()->param('id');
        $amount = request()->param('amount');
        $pay_time = date('Y-m-d H:i:s', time());
        $status = request()->param('status', '0');
        $order_no = 'OR'.date('YmdHis',time()).rand(100000, 999999);
        $channel = request()->param('channel');
        $currency = request()->param('currency');
        $subject = request()->param('subject');
        $body = request()->param('body');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id,
            'amount'    => $amount,
            'pay_time'  => $pay_time,
            'status'    => $status,
            'order_no'  => $order_no,
            'channel'   => $channel,
            'currency'  => $currency,
            'subject'   => $subject,
            'body'      => $body
        ];

        /* 验证结果 */
        $result = $this->order_validate->scene('save')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->order_validate->getError()
            ]);
        }

        /* 返回结果 */
        if (empty($id) || is_null($id)) {
            $operator = $this->order_model->save($validate_data);
        } else {
            $operator = $this->order_model->save($validate_data, ['id' => $id]);
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
