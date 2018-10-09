<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/8
 * Time: 16:15
 * Comment: 角色用户模型
 */

namespace app\admin\model;

class AdminRole extends BasisModel {

    /* 读存时间 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_admin_role';

}
