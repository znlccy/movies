<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:17
 * Comment: 主播模型
 */

namespace app\admin\model;

class Anchor extends BasisModel {

    /* 时间戳 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应表 */
    protected $table = 'tb_anchor';

}