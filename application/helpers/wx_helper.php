<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'vendor/autoload.php';
use EasyWeChat\Factory;

global $config;
$config = [
    'app_id'             => 'wx5d276a1e3d25bce5',
    'mch_id'             => '1602861157',

    'key'                => '04e7db69b3d659b399619e0eece27886',

    'cert_path'          => 'C:\phpstudy_pro\WWW\SkillTrain\public\cert\apiclient_cert.pem',
    'key_path'           => 'C:\phpstudy_pro\WWW\SkillTrain\public\cert\apiclient_key.pem',

    'notify_url'         => 'https://ywwuyi.top/Hanfu-world/public/wxpayv3/notify.php',

    'sandbox'            => false
];
// 获取统一订单id
function get_prepay_id($id,$price,$openid,$description){
    $pay = Factory::payment($GLOBALS['config']);
    return $pay->order->unify(['body' => $description, 'out_trade_no' => $id, 'total_fee' => $price, 'openid' => $openid, 'trade_type' => 'JSAPI']);
}
// 查询订单状态
function get_prepay_state($out_trade_no){
    $pay = Factory::payment($GLOBALS['config']);
    return $pay->order->queryByOutTradeNumber($out_trade_no);
}
// 调起支付
function pay($prepayId){
    $payment = Factory::payment($GLOBALS['config']);
    $js_sdk = $payment->jssdk;
    return $js_sdk->bridgeConfig($prepayId, FALSE);
}
// 生成商户订单号
function get_random_id($length,$s_key=''){
    $str = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's',
        't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $base_psw = '';
    $aim_len = $length;
    if($s_key !== ''){
        $aim_len = $length - strlen($s_key);
        $base_psw = $s_key;
    }
    $keys = array_rand($str, $aim_len);
    $password = '';
    for($i = 0; $i < $aim_len; $i++){
        $password .= $str[$keys[$i]];
    }
    return $base_psw.$password;
}