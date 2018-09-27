<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:18
 * Comment: 管理员模型
 */

namespace app\admin\model;

class Admin extends BasisModel {

    /* 时间戳 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应表 */
    protected $table = 'tb_admin';

    /* 关联表 */
    public function roles() {
        return $this->belongsToMany('Role','tb_admin_role', 'role_id', 'user_id');
    }
}