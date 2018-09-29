<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:38
 * Comment: 录制控制器
 */

namespace app\admin\controller;

use app\admin\validate\Record as RecordValidate;
use think\Request;

class Record extends BasisController {

    /* 声明流 */
    protected $stream;

    /* 声明访问token */
    protected $ak;

    /* 声明秘钥token */
    protected $sk;

    /* 声明mac */
    protected $mac;

    /* 声明client */
    protected $client;

    /* 声明hub名称 */
    protected $hubName;

    /* 声明hub */
    protected $hub;

    /* 声明记录验证器 */
    protected $record_validate;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->stream = config('stream');
        $this->ak = $this->stream['ak'];
        $this->sk = $this->stream['sk'];
        $this->mac = new Mac($this->ak, $this->sk);
        $this->client = new Client($this->mac);
        $this->hubName = $this->stream['hubName'];
        $this->hub = $this->client->hub($this->hubName);
        $this->record_validate = new RecordValidate();
    }

    /* 录制视频 */
    public function record() {

        /* 验证数据 */

    }
}