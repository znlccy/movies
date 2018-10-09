<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:11
 * Comment: 管理员控制器
 */

namespace app\admin\controller;

use think\Request;
use app\admin\model\Admin as AdminModel;
use app\admin\validate\Admin as AdminValidate;
use app\admin\model\Sms as SmsModel;
use app\admin\model\Role as RoleModel;
use app\admin\model\AdminRole as AdminRoleModel;
use gmars\rbac\Rbac;
use think\Session;

class Admin extends BasisController {

    /* 声明管理员模型 */
    protected $admin_model;

    /* 声明短信模型 */
    protected $sms_model;

    /* 声明角色模型 */
    protected $role_model;

    /* 声明用户角色模型 */
    protected $admin_role_model;

    /* 声明管理员验证器 */
    protected $admin_validate;

    /* 声明管理员分页器 */
    protected $admin_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->admin_model = new AdminModel();
        $this->sms_model = new SmsModel();
        $this->role_model = new RoleModel();
        $this->admin_role_model = new AdminRoleModel();
        $this->admin_validate = new AdminValidate();
        $this->admin_page = config('pagination');
    }

    /* 管理员手机登录 */
    public function mobile_login() {

        /* 接收参数 */
        $mobile = request()->param('mobile');
        $code = request()->param('code');

        /* 验证参数 */
        $validate_data = [
            'mobile'        => $mobile,
            'code'          => $code
        ];

        /*  验证结果 */
        $result = $this->admin_validate->scene('mobile_login')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->admin_validate->getError()
            ]);
        }

        /* 实例化模型 */
        $admin = $this->admin_model->where('mobile', $mobile)
            ->where('status', 1)
            ->find();

        if (empty($admin)) {
            return json([
                'code'      => '402',
                'message'   => '登录失败'
            ]);
        }

        /* 对比短信验证码 */
        $sms_code = $this->sms_model->where('mobile', '=', $mobile)->find();

        if (empty($sms_code)) {
            return json([
                'code'      => '404',
                'message'   => '该手机还没有生成注册码'
            ]);
        }

        if (strtotime($sms_code['expiration_time']) - time() < 0) {
            return json([
                'code'      => '405',
                'message'   => '验证码已经过期'
            ]);
        }

        if ($sms_code['code'] != $code) {
            return json([
                'code'      => '407',
                'message'   => '登录失败'
            ]);
        }

        /* 更新用户登录记录 */
        $data = [
            'login_time'        => date('Y-m-d H:i:s', time()),
            'login_ip'          => request()->ip(),
            'authentication'    => 1
        ];

        /* 更新结果 */
        $result = $this->admin_model->where('mobile', '=', $mobile)->update($data);

        if ($result) {
            Session::set('admin', $admin);
            $token = general_token($mobile, time());
            Session::set('admin_token', $token);

            // 验证码使用一次后立即失效
            $this->sms_model->where('mobile', $mobile)->update(['create_time' => date('Y-m-d H:i:s', time())]);
            return json(['code' => '200', 'message' => '登录成功', 'admin_token' => $token, 'real_name' => $admin['real_name']]);
        } else{
            return json(['code' => '408', 'message' => '登录失败']);
        }
    }

    /* 管理员账号登录 */
    public function account_login() {

        /* 接收参数 */
        $mobile = request()->param('mobile');
        $password = request()->param('password');

        /* 验证参数 */
        $validate_data = [
            'mobile'        => $mobile,
            'password'      => $password
        ];

        /* 验证结果 */
        $result = $this->admin_validate->scene('account_login')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->admin_validate->getError()
            ]);
        }

        /* 数据库实例化 */
        $admin = $this->admin_model->where('mobile', $mobile)
            ->where('password', md5($password))
            ->where('status', '=', '1')
            ->find();

        /* 检查是否实名制 */
        if (!($admin['authentication'] === 1)) {
            $authentication_data = ['mobile' => $mobile];
            return json([
                'code'      => '302',
                'message'   => '需要进行手机真实验证',
                'data'      => $authentication_data
            ]);
        }

        Session::set('admin',$admin);
        $token = general_token($mobile, $password);
        Session::set('admin_token', $token);
        return json([
            'code'          => '200',
            'message'       => '登录成功',
            'admin_token'   => $token,
            'real_name'     => $admin['real_name']
        ]);

    }

    /* 管理员列表 */
    public function entry() {

        /* 接收参数 */
        $id = request()->param('id');
        $mobile = request()->param('mobile');
        $status = request()->param('status');
        $real_name = request()->param('real_name');
        $register_start = request()->param('register_start');
        $register_end = request()->param('register_end');
        $update_start = request()->param('update_start');
        $update_end = request()->param('update_end');
        $login_start = request()->param('login_start');
        $login_end = request()->param('login_end');
        $login_ip = request()->param('login_ip');
        $create_ip = request()->param('create_ip');
        $page_size = request()->param('page_size');
        $jump_page = request()->param('jump_page');

        /* 验证数据 */
        $validate_data = [
            'id'            => $id,
            'mobile'        => $mobile,
            'status'        => $status,
            'real_name'     => $real_name,
            'register_start'=> $register_start,
            'register_end'  => $register_end,
            'update_start'  => $update_start,
            'update_end'    => $update_end,
            'login_start'   => $login_start,
            'login_end'     => $login_end,
            'login_ip'      => $login_ip,
            'create_ip'     => $create_ip,
            'page_size'     => $page_size,
            'jump_page'     => $jump_page
        ];

        /* 验证结果 */
        $result = $this->admin_validate->scene('account_login')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->admin_validate->getError()
            ]);
        }

        /* 过滤条件 */
        $conditions = [];

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($mobile) {
            $conditions['mobile'] = $mobile;
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

        if ($real_name) {
            $conditions['real_name'] = ['like', '%' . $real_name . '%'];
        }

        if ($register_start && $register_end) {
            $conditions['register_time'] = ['between time', [$register_start, $register_end]];
        }

        if ($update_start && $update_end) {
            $conditions['update_time'] = ['between time', [$update_start, $update_end]];
        }

        if ($login_start && $login_end) {
            $conditions['login_time'] = ['between time', [$login_start, $login_end]];
        }

        if ($login_ip) {
             $conditions['login_ip'] = ['like', '%' . $login_ip . '%'];
        }

        if ($create_ip) {
            $conditions['create_ip'] = ['like', '%' . $create_ip . '%'];
        }

        /* 返回数据 */
        $admin = $this->admin_model->where($conditions)
            ->with(['role' => function($query) {
                $query->withField('id,name');
            }])
            ->paginate($page_size, false, ['page' => $jump_page]);

        if ($admin) {
            return json([
                'code'      => '200',
                'message'   => '获取管理员列表成功',
                'data'      => $admin
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '获取管理员列表失败'
            ]);
        }
    }

    /* 管理员详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->admin_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->admin_validate->getError()
            ]);
        }

        /* 返回结果 */
        $admin = $this->admin_model->where('id', $id)->find();

        if ($admin) {
            return json([
                'code'      => '200',
                'message'   => '查询消息成功',
                'data'      => $admin
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询消息失败'
            ]);
        }

    }

    /* 管理员删除 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->admin_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->admin_validate->getError()
            ]);
        }

        /* 返回结果 */
        $delete = $this->admin_model->where('id', $id)->delete();

        if ($delete) {
            return json([
                'code'      => '200',
                'message'   => '删除管理员成功'
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '删除管理员失败'
            ]);
        }

    }

    /* 分配用户角色 */
    public function assign_user_role() {

        //实例化权限控制器
        $rbac = new Rbac();

        /* 接收客户端提供的数据 */
        $id = request()->param('id');
        $mobile = request()->param('mobile');
        $password = request()->param('password');
        $confirm_pass = request()->param('confirm_pass');
        $real_name = request()->param('real_name');
        $status = request()->param('status');
        $role_id = request()->param('role_id/a');

        $admin_model = new AdminModel();
        $admin_role_model = new AdminRoleModel();
        $rule = [
            'id'            => 'number',
            'password'      => 'alphaDash|length:8,25',
            'confirm_pass'  => 'alphaDash|length:8,25|confirm:password',
            'real_name'     => 'require|max:40',
            'status'        => 'require|number',
            'role_id'       => 'require|array'
        ];

        //如果是更新修改passwrod验证规则
        if (empty($id)){
            $rule_add = [
                'mobile'        => 'require|length:11|unique:tb_admin',
                'password'      => 'require|alphaDash|length:8,25',
                'confirm_pass'  => 'require|alphaDash|length:8,25|confirm:password',
            ];
            $rule = array_merge($rule, $rule_add);
        }

        $message = [
            'id'            => 'ID',
            'mobile'        => '手机号',
            'password'      => '密码',
            'confirm_pass'  => '确认密码',
            'real_name'     => '姓名',
            'status'        => '状态',
            'role_id'       => '角色ID',
        ];

        $validate_data = [
            'id'            => $id,
            'mobile'        => $mobile,
            'password'      => $password,
            'confirm_pass'  => $confirm_pass,
            'real_name'     => $real_name,
            'status'        => $status,
            'role_id'       => $role_id
        ];

        $validate = new Validate($rule, [], $message);
        $result = $validate->check($validate_data);

        if (!$result) {
            return json(['code' => '401', 'message' => $validate->getError()]);
        }

        /* 封装数据 */
        $user_data = [
            'real_name'      => $real_name,
            'status'         => $status,
            'create_ip'      => request()->ip()
        ];

        if ( empty($id) ){
            $data_add = [
                'create_time'  => date('Y-m-d H:i:s'),
                'mobile'       => $mobile,
                'password'     => md5($password)
            ];
            $user_data = array_merge($user_data, $data_add);
        }else{
            if ( !empty($password) && !empty($confirmpass) ){
                $data_add = [
                    'password'  => md5($password)
                ];
                $user_data = array_merge($user_data, $data_add);
            }
        }

        $rbacObj = new Rbac();
        if (!empty($id)) {

            $update_result = $admin_model->where('id','=', $id)->update($user_data);
            $admin_role_model->where('user_id',$id)->delete();
            $result = $rbacObj->assignUserRole($id, $role_id);

            if ($result) {
                return json([
                    'code'      => '200',
                    'message'   => '更新成功'
                ]);
            }
        } else {
            /* 添加用户表之后，再添加用户角色表 */
            $uid = $admin_model->insertGetId($user_data);

            /* 添加用户角色表 */
            if ($uid) {
                /* 用户添加成功后，添加用户角色表 */
                $result = $rbacObj->assignUserRole($uid, $role_id);

                if ($result) {
                    return json([
                        'code'      => '200',
                        'message'   => '添加成功'
                    ]);
                } else {
                    return json([
                        'code'      => '403',
                        'message'   => '添加失败'
                    ]);
                }
            } else {
                return json([
                    'code'      => '403',
                    'message'   => '添加失败'
                ]);
            }
        }
    }

    /* 角色下拉列表 */
    public function spinner() {

        /* 获取数据 */
        $roles = $this->role_model->where('status','=', '1')->field('id, name')->select();

        /* 返回数据 */
        if (empty($roles)) {
            return json([
                'code'      => '200',
                'message'   => '获取角色列表成功',
                'data'      => $roles
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '获取角色列表失败'
            ]);
        }
    }

    /* 获取管理员个人信息 */
    public function info() {

        /* 获取管理员手机 */
        $admin = Session::get('admin');

        /* 获取用户主键 */
        $id = $admin['id'];

        /* 返回数据 */
        if ($id) {
            $admin_data = $this->admin_model->where('id', $id)->find();
            return json([
                'code'      => '200',
                'message'   => '查询数据成功',
                'data'      => $admin_data
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询数据失败'
            ]);
        }
    }

    /* 改变管理员密码 */
    public function change_password() {

        /* 接收参数 */
        $password = request()->param('password');
        $confirm_pass = request()->param('confirm_pass');

        /* 验证数据 */
        $validate_data = [
            'password'      => $password,
            'confirm_pass'  => $confirm_pass
        ];

        /* 验证结果 */
        $result = $this->admin_validate->scene('change_password')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->admin_validate->getError()
            ]);
        }

        /* 获取Session中的数据 */
        $admin = Session::get('admin');
        $id = $admin['id'];

        /* 返回数据 */
        if ($id) {
            $update_data = [
                'password'      => md5($password)
            ];
            $this->admin_model->save($update_data, ['id' => $id]);
            return json([
                'code'      => '200',
                'message'   => '更改密码成功'
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '更改密码失败'
            ]);
        }
    }

    /* 管理员退出 */
    public function logout() {

        /* 删除Session中的数据 */
        Session::delete('admin');
        Session::delete('admin_token');
        if (Session::get('admin') == null && Session::get('admin_token') == null) {
            return json([
                'code'      => '200',
                'message'   => '管理员退出成功'
            ]);
        } else {
            return json([
                'code'      => '401',
                'message'   => '管理员退出失败'
            ]);
        }
    }
}