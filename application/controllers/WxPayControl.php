<?php
class WxPayControl extends CI_Controller{
    private $receive_data;
    public function __construct(){
        parent::__construct();
        $this->load->helper('wx');
        $this->load->helper('tool');
        $this->load->service('WxPay');
        $receive = file_get_contents('php://input');
        $this->receive_data = json_decode($receive, TRUE);
    }
    public function get_prepay_id(){
        $description = $this->receive_data['description'];
        $openid = $this->receive_data['openid'];
        $price = $this->receive_data['price'];
        $id = get_random_id(32,'ZJT');
        $result = get_prepay_id($id,$price,$openid,$description);
        $msg = $result['return_msg'];
        $res = null;
        if(isset($result['return_code'])){
            $msg = $result['return_msg'];
        }
        if(isset($result['result_code'])){
            if($result['result_code'] === 'SUCCESS'){
                $appId = $result['appid'];
                $nonceStr = $result['nonce_str'];
                $prepay_id = $result['prepay_id'];
                $timeStamp = time();
                $key = '04e7db69b3d659b399619e0eece27886';
                $paySign = md5("appId=$appId&nonceStr=$nonceStr&package=prepay_id=$prepay_id&signType=MD5&timeStamp=$timeStamp&key=$key");
                $state = get_prepay_state($id);
                $res = [
                    'nonceStr' => $nonceStr,
                    'prepay_id' => $prepay_id,
                    'timeStamp' => strval($timeStamp),
                    'paySign' => $paySign,
                    'signType' => 'MD5'
                ];
                $resultArr = build_resultArr('GPI000', TRUE, 0, $msg, [$res,$result,$state,$id]);
                http_data(200, $resultArr, $this);
            }
        }
        $resultArr = build_resultArr('GPI001', FALSE, 0, $msg, $res);
        http_data(200, $resultArr, $this);
    }
}