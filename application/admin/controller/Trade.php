<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 16:51
 * Comment: 交易控制器
 */

namespace app\admin\controller;

use Endroid\QrCode\QrCode;
use Pingpp\Pingpp;
use think\Request;

class Trade extends BasisController {

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
    }

    /* 一般支付 */
    public function pay() {
        Pingpp::setApiKey("sk_test_nbLa9SD84qfHezj1qD1WfPeT");
        Pingpp::setPrivateKeyPath(__DIR__ . '/my_private.pem');
        $charge = \Pingpp\Charge::create(array('order_no'  => '123456789',
                'amount'    => '1',//订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
                'app'       => array('id' => 'app_jj9irPO80arTrDOm'),
                'channel'   => 'alipay',
                'currency'  => 'cny',
                'client_ip' => '127.0.0.1',
                'subject'   => 'Your Subject',
                'body'      => 'Your Body')
        );

        return json([
            'code'      => '200',
            'message'   => '支付成功',
            'data'      => $charge
        ]);
    }

    /* 扫码支付 */
    public function scan_pay() {

        Pingpp::setApiKey("sk_test_nbLa9SD84qfHezj1qD1WfPeT");
        Pingpp::setPrivateKeyPath(__DIR__ . '/my_private.pem');
        $charge = \Pingpp\Charge::create(array('order_no'  => '123456789',
                'amount'    => '1',//订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
                'app'       => array('id' => 'app_jj9irPO80arTrDOm'),
                'channel'   => 'alipay_qr',
                'currency'  => 'cny',
                'client_ip' => '127.0.0.1',
                'subject'   => 'Your Subject',
                'body'      => 'Your Body')
        );

        $url = $charge['credential']['alipay_qr'];

        $qrCode = new QrCode();
        $qrCode->setText($url)
            ->setSize(300)//大小
            /*->setLabelFontPath(VENDOR_PATH .'\endroid\qrcode\assets\noto_sans.otf')*/
            ->setErrorCorrectionLevel('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            /*->setLabel('推广码')*/
            ->setLabelFontSize(16);
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
        exit;

    }

    /* 申请退款 */
    public function refund() {

    }


    public function qrcode() {
        $qrCode = new QrCode();
        $url = 'http://www.baidu.com';
        $qrCode->setText($url)
            ->setSize(300)//大小
            /*->setLabelFontPath(VENDOR_PATH .'\endroid\qrcode\assets\noto_sans.otf')*/
            ->setErrorCorrectionLevel('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            /*->setLabel('推广码')*/
            ->setLabelFontSize(16);
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
        exit;

    }
}