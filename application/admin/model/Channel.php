<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/28
 * Time: 11:00
 * Comment: 渠道模型
 */

namespace app\admin\model;

class Channel extends BasisModel {

    /* 生成时间戳 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_channel';
}