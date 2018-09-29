<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/28
 * Time: 12:10
 * Comment: 货币类型控制器
 */

namespace app\admin\controller;

use think\Request;
use app\admin\model\Currency as CurrencyModel;
use app\admin\validate\Currency as CurrencyValidate;

class Currency extends BasisController {

    /* 声明货币模型 */
    protected $currency_model;

    /* 声明货币验证器 */
    protected $currency_validate;

    /* 声明货币分页器 */
    protected $currency_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->currency_model = new CurrencyModel();
        $this->currency_validate = new CurrencyValidate();
        $this->currency_page = config('pagination');
    }

    /* 货币列表 */
    public function entry() {

        /* 接收参数 */
        $id = request()->param('id');
        $name = request()->param('name');
        $status = request()->param('status');
        $create_start = request()->param('create_start');
        $create_end = request()->param('create_end');
        $update_start = request()->param('update_start');
        $update_end = request()->param('update_end');
        $page_size = request()->param('page_size',$this->currency_page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page',$this->currency_page['JUMP_PAGE']);

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
        $result = $this->currency_validate->scene('entry')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->currency_validate->getError()
            ]);
        }

        /* 筛选条件 */
        $conditions = [];

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($name) {
            $conditions['name'] = ['like', '%'. $name .'%'];
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
        $currency = $this->currency_model
            ->where($conditions)
            ->order('id', 'desc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        if ($currency) {
            return json([
                'code'      => '200',
                'message'   => '查询数据成功',
                'data'      => $currency
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询数据失败'
            ]);
        }
    }

    /* 货币添加更新 */
    public function save() {

        /* 接收参数 */
        $id = request()->param('id');
        $name = request()->param('name');
        $status = request()->param('status',1);

        /* 验证参数 */
        $validate_data = [
            'id'        => $id,
            'name'      => $name,
            'status'    => $status
        ];

        /* 返回结果 */
        $result = $this->currency_validate->scene('save')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->currency_validate->getError()
            ]);
        }

        /* 返回结果 */
        if (empty($id) || is_null($id)) {
            $operator = $this->currency_model->save($validate_data);
        } else {
            $operator = $this->currency_model->save($validate_data, ['id' => $id]);
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

    /* 货币详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->currency_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->currency_validate->getError()
            ]);
        }

        /* 返回结果 */
        $currency = $this->currency_model->where('id', $id)->find();

        if ($currency) {
            return json([
                'code'      => '200',
                'message'   => '查询货币成功',
                'data'      => $currency
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询货币失败'
            ]);
        }
    }

    /* 货币删除 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->currency_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->currency_validate->getError()
            ]);
        }

        /* 返回结果 */
        $delete = $this->currency_model->where('id', $id)->delete();

        if ($delete) {
            return json([
                'code'      => '200',
                'message'   => '删除信息成功'
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '删除信息失败'
            ]);
        }
    }

}