<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:38
 * Comment: 直播控制器
 */

namespace app\admin\controller;

use Qiniu\Pili\Client;
use Qiniu\Pili\Mac;
use think\Request;
use app\admin\validate\Live as LiveValidate;

class Live extends BasisController {

    /* 声明流 */
    protected $stream;

    /* 声明访问token */
    protected $ak;

    /* 声明秘钥token */
    protected $sk;

    /* 声明MAC加密 */
    protected $mac;

    /* 声明客户端 */
    protected $client;

    /* 声明hub名字 */
    protected $hubName;

    /* 声明hub */
    protected $hub;

    /* 声明直播验证器 */
    protected $live_validate;

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
        $this->live_validate = new LiveValidate();
    }

    /* RTMP推流地址 */
    public function rtmp_publish() {

        /* 接收参数 */
        $stream_key = request()->param('stream_key');

        /* 验证参数 */
        $validate_data = [
            'stream_key'        => $stream_key
        ];

        /* 验证结果 */
        $result = $this->live_validate->scene('rtmp_publish')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->live_validate->getError()
            ]);
        }

        $rtmp_publish = \Qiniu\Pili\RTMPPublishURL('pili-publish.tupiaopiao.com', $this->hubName, $stream_key, 3600, $this->ak, $this->sk);

        if ($rtmp_publish) {
            return json([
                'code'      => '200',
                'message'   => 'RTMP推流地址成功',
                'data'      => $rtmp_publish
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => 'RTMP推流地址失败'
            ]);
        }
    }

    /* RTMP播放地址 */
    public function rtmp_play() {

        /* 接收参数 */
        $stream_key = request()->param('stream_key');

        /* 验证参数 */
        $validate_data = [
            'stream_key'        => $stream_key
        ];

        /* 验证结果 */
        $result = $this->live_validate->scene('rtmp_play')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->live_validate->getError()
            ]);
        }

        /* 返回结果 */
        $rtmp_play = \Qiniu\Pili\RTMPPlayURL('pili-live-rtmp.tupiaopiao.com', $this->hubName, $stream_key);

        if ($rtmp_play) {
            return json([
                'code'      => '200',
                'message'   => 'RTMP播放地址成功',
                'data'      => $rtmp_play
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => 'RTMP播放地址失败'
            ]);
        }
    }

    /* HLS播放地址 */
    public function hls_play() {

        /* 接收参数 */
        $stream_key = request()->param('stream_key');

        /* 验证参数 */
        $validate_data = [
            'stream_key'        => $stream_key
        ];

        /* 验证结果 */
        $result = $this->live_validate->scene('hls_play')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->live_validate->getError()
            ]);
        }

        $hls_play = \Qiniu\Pili\HLSPlayURL('pili-live-hls.tupiaopiao.com', $this->hubName, $stream_key);

        if ($hls_play) {
            return json([
                'code'      => '200',
                'message'   => 'HLS播放地址成功',
                'data'      => $hls_play
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => 'HLS播放地址失败'
            ]);
        }
    }

    /* HDL播放地址 */
    public function hdl_play() {

        /* 接收参数 */
        $stream_key = request()->param('stream_key');

        /* 验证参数 */
        $validate_data = [
            'stream_key'        => $stream_key
        ];

        /* 验证结果 */
        $result = $this->live_validate->scene('hdl_play')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->live_validate->getError()
            ]);
        }

        /* 返回结果 */
        $hdl_play = \Qiniu\Pili\HDLPlayURL('pili-live-hdl.tupiaopiao.com', $this->hubName, $stream_key);

        if ($hdl_play) {
            return json([
                'code'      => '200',
                'message'   => 'HDL播放地址成功',
                'data'      => $hdl_play
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => 'HDL播放地址失败'
            ]);
        }
    }

    /* 直播封面地址 */
    public function snapshot_play() {

        /* 接收参数 */
        $stream_key = request()->param('stream_key');

        /* 验证参数 */
        $validate_data = [
            'stream_key'        => $stream_key
        ];

        /* 验证结果 */
        $result = $this->live_validate->scene('snapshot_play')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->live_validate->getError()
            ]);
        }

        /* 返回结果 */
        $snapshot = \Qiniu\Pili\SnapshotPlayURL('pili-live-snapshot.tupiaopiao.com', $this->hubName, $stream_key);

        if ($snapshot) {
            return json([
                'code'      => '200',
                'message'   => '直播封面成功',
                'data'      => $snapshot
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '直播封面失败'
            ]);
        }
    }

    /* 更改流 */
    public function converts() {

        /* 接收参数 */
        $id = request()->param('id');
        $start = request()->param('start');
        $end = request()->param('end');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id,
            'start'     => $start,
            'end'       => $end
        ];

        /* 验证结果 */
        $result = $this->live_validate->scene('converts')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->live_validate
            ]);
        }

        /* 返回结果 */
        $streamKey = "anchor".$id;
        $this->hub->stream($streamKey)->updateConverts(array($start.'p', $end.'p'));
        return json([
            'code'      => '200',
            'message'   => '转码成功'
        ]);
    }
}