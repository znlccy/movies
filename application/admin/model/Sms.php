<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/8
 * Time: 11:34
 * Comment: 短信验证码模型
 */

namespace app\admin\model;

class Sms extends BasisModel {

    /* 读写时间 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_sms';
}