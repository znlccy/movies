<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 16:42
 * Comment: 订单模型
 */

namespace app\admin\model;

class Order extends BasisModel {

    /* 生成时间戳 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_order';


}