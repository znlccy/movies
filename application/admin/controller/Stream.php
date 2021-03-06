<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/25
 * Time: 16:37
 * Comment: 流控制器
 */

namespace app\admin\controller;

use Qiniu\Pili\Client;
use Qiniu\Pili\Mac;
use think\Request;
use app\admin\validate\Stream as StreamValidate;
use app\admin\model\Anchor as AnchorModel;

class Stream extends BasisController {

    /* 声明流 */
    protected $stream;

    /* 声明accessToken */
    protected $ak;

    /* 声明secretToken */
    protected $sk;

    /* 声明mac */
    protected $mac;

    /* 声明客户端 */
    protected $client;

    /* 声明直播空间名 */
    protected $hubName;

    /* 声明hub */
    protected $hub;

    /* 声明流验证器 */
    protected $stream_validate;

    /* 声明主播模型 */
    protected $anchor_model;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->stream = config('stream');
        $this->ak = $this->stream['ak'];
        $this->sk = $this->stream['sk'];
        $this->mac = new Mac($this->ak,$this->sk);
        $this->client = new Client($this->mac);
        $this->hubName = $this->stream['hubName'];
        $this->hub = $this->client->hub($this->hubName);
        $this->stream_validate = new StreamValidate();
        $this->anchor_model = new AnchorModel();
    }

    /* 创建流地址 */
    public function create() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->stream_validate->scene('create')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->stream_validate->getError()
            ]);
        }

        /* 查询数据 */
        $anchor = $this->anchor_model->where('id',$id)->find();

        /* 返回数据 */
        if ($anchor) {
            $streamKey = "anchor".$id;

            try {
                $stream = $this->hub->stream($streamKey)->info();
                if ($stream) {
                    return json([
                        'code'      => '201',
                        'message'   => '已创建该流',
                        'data'      => $stream
                    ]);
                }
            } catch (\Exception $e) {
                $resp = $this->hub->create($streamKey);
                return json([
                    'code'      => '200',
                    'message'   => '创建流成功'
                ]);
            }
        } else {
            return json([
                'code'      => '404',
                'message'   => '没有该主播'
            ]);
        }
    }

    /* 查询流信息 */
    public function find() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->stream_validate->scene('find')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->stream_validate->getError()
            ]);
        }

        /* 查询数据 */
        $anchor = $this->anchor_model->where('id',$id)->find();
        if ($anchor) {
            $streamKey = "anchor".$id;
            try {
                $resp = $this->hub->stream($streamKey)->info();
                if ($resp) {
                    return json([
                        'code'      => '200',
                        'message'   => '查询流成功',
                        'data'      => $resp
                    ]);
                }
            } catch (\Exception $e) {
                return json([
                    'code'      => '404',
                    'message'   => '流没有找到',
                ]);
            }
        } else {
            return json([
                'code'      => '404',
                'message'   => '该主播不存在'
            ]);
        }

    }

    /* 查询流列表 */
    public function entry() {

        /* 接收客户端提交过来的数据 */
        $limit = request()->param('limit', 1000);
        $streamKey = request()->param('stream_key', 'anchor');

        /* 验证数据 */
        $validate_data = [
            'limit'     => $limit,
            'streamKey' => $streamKey
        ];

        /* 验证结果 */
        $result = $this->stream_validate->scene('entry')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->stream_validate->getError()
            ]);
        }

        /* 查询数据 */
        $streams = $this->hub->listStreams($streamKey, $limit, '');

        /* 返回结果 */
        if ($streams) {
            return json([
                'code'      => '200',
                'message'   => '查询流列表成功',
                'data'      => $streams
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询流列表失败',
            ]);
        }

    }

    /* 禁播流 */
    public function disable() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->stream_validate->scene('forbidden')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->stream_validate->getError()
            ]);
        }

        /* 查询结果 */
        $anchor = $this->anchor_model->where('id',$id)->find();
        if ($anchor) {
            $streamKey = "anchor".$id;
            $this->hub->stream($streamKey)->disable();
            return json([
                'code'      => '200',
                'message'   => '禁流成功'
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '该主播不存在'
            ]);
        }

    }

    /* 启用流 */
    public function enable() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->stream_validate->scene('enable')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->stream_validate->getError()
            ]);
        }

        /* 返回结果 */
        $anchor = $this->anchor_model->where('id', $id)->find();

        if ($anchor) {
            $streamKey = "anchor".$id;
            $this->hub->stream($streamKey)->enable();
            return json([
                'code'      => '200',
                'message'   => '启用流成功'
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '该主播不存在'
            ]);
        }
    }

    /* 直播实时信息 */
    public function live() {

        /* 接收参数 */
        $id = request()->param('id');
        $limit = request()->param('limit',1000);

        /* 验证参数 */
        $validate_data = [
            'id'        => $id,
            'limit'     => $limit
        ];

        /* 验证结果 */
        $result = $this->stream_validate->scene('live')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->stream_validate->getError()
            ]);
        }

        /* 验证直播流 */
        $streamKey = 'dao'.$id;

        $liveStatus = $this->hub->listLiveStreams($streamKey, $limit, '');
        if ($liveStatus) {
            return json([
                'code'      => '200',
                'message'   => '直播流成功',
                'data'      => $liveStatus
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '直播流失败'
            ]);
        }
    }

    /* 批量查询直播实时信息 */
    public function batch_live() {

        /* 接收参数 */
        $streamKey = request()->param('streamKey/a');

        /* 验证参数 */
        $validate_data = [
            'streamKey'        => $streamKey
        ];

        /* 验证结果 */
        $result = $this->stream_validate->scene('batch_live')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->stream_validate->getError()
            ]);
        }

        /* 返回结果 */
        $stream = $this->hub->batchLiveStatus($streamKey);

        if ($stream) {
            return json([
                'code'      => '200',
                'message'   => '查询流信息成功',
                'data'      => $stream
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询流信息失败'
            ]);
        }
    }

    /* 直播历史记录 */
    public function history() {

        /* 接收参数 */
        $id = request()->param('id');
        $start = strtotime(request()->param('start', 0));
        $end  = strtotime(request()->param('end', 0));

        /* 验证参数 */
        $validate_data = [
            'id'        => $id,
            'start'     => $start,
            'end'       => $end
        ];

        /* 验证结果 */
        $result = $this->stream_validate->scene('history')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->stream_validate->getError()
            ]);
        }

        /* 返回结果 */
        $streamKey = "anchor".$id;
        try {
            $records = $this->hub->stream($streamKey)->historyActivity($start, $end);
            if ($records) {
                return json([
                    'code'      => '200',
                    'message'   => '查询流历史成功',
                    'data'      => $records
                ]);
            }
        } catch (\Exception $e) {
            return json([
                'code'      => '404',
                'message'   => '查询流历史失败'
            ]);
        }
    }
}