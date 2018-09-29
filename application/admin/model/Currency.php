<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/28
 * Time: 11:02
 * Comment: 货币类型模型
 */

namespace app\admin\model;

class Currency extends BasisModel {

    /* 生成时间戳 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_currency';
}