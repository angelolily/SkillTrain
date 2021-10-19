<?php
require './qcloudsms_php/src/index.php';
use Qcloud\Sms\SmsSingleSender;
class WxPayControl extends CI_Controller{
    private $receive_data;
    public function __construct(){
        parent::__construct();
        $this->load->helper('wx');
        $this->load->helper('tool');
        $this->load->helper('redis');
        $this->load->service('WxPay');
        $this->load->library('encryption');
        
        $receive = file_get_contents('php://input');
        $this->receive_data = json_decode($receive, TRUE);
    }
    // 验证服务器用函数
    public function check() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = "HTYZJT";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    // 根据微信授权code登陆
    public function user_login_with_code(){
        $url_param = get_url_param();
        $user_wx = get_user_wx_info($url_param[0],'snsapi_userinfo');
        $res = $this->wxpay->user_login_with_code($user_wx['openid']);
        if(!$res){
            set_reids_key($url_param[1],json_encode($user_wx),0,86400);
            header('Location: https://admin.wd-jk.com/#/login');
        }
        set_reids_key($url_param[1],json_encode($res[0]),0,86400);
        header('Location: https://admin.wd-jk.com/#/my');
    }
    // 根据账号密码登陆并在需要时更新openid
    public function user_login(){
        $where = array(
            'members_phone'=>$this->receive_data['phone'],
        );
        $res = $this->wxpay->user_login($where);
        if(!$res){
            $resultArr = build_resultArr('UL001', FALSE, 444,'找不到用户信息', null );
            http_data(200, $resultArr, $this);
        }
        $pwd_db = $this->encryption->decrypt($res[0]['members_pwd']);
        if($pwd_db != $this->receive_data['pwd']){
            $resultArr = build_resultArr('UL002', FALSE, 444,'密码错误', null );
            http_data(200, $resultArr, $this);
        }
        $keys = array_keys($res[0]);
        $index = array_search('members_pwd', $keys);
        if($index !== FALSE){
            array_splice($res[0], $index, 1);
        }
        if(array_key_exists('members_openid',$res[0]) && ($res[0]['members_openid'] == '' || !$res[0]['members_openid'])){
            $code = $this->receive_data['code'];
            $user_wx = get_user_wx_info($code,'snsapi_base');
            $res_update = $this->wxpay->update_user_openid($res[0]['members_phone'],$user_wx['openid']);
            if($res_update){
                $res[0]['members_openid'] = $user_wx['openid'];
            }
        }
        $resultArr = build_resultArr('UL001', TRUE, 0,'获取用户信息成功', $res[0] );
        http_data(200, $resultArr, $this);
    }
    // 用户注册
    public function user_reg(){
        $code = $this->receive_data['code'];
        $user_wx_info = get_user_wx_info($code,'snsapi_userinfo');
        $user_wx_info['sex'] = $user_wx_info['sex'] == 1 ? '男' : '女';
        $user_reg_info = $this->receive_data['reg_form'];
        $user_reg_info['members_pwd'] = $this->encryption->encrypt($user_reg_info['members_pwd']);
        $res = $this->wxpay->user_reg($user_reg_info,$user_wx_info);
        if(!$res){
            $resultArr = build_resultArr('UR001', FALSE, 0,'NOT FOUND USER', null );
            http_data(200, $resultArr, $this);
        }
        $where = array(
            'members_phone'=>$user_reg_info['members_phone'],
        );
        $user_info = $this->wxpay->user_login($where);
        if(!$user_info){
            $resultArr = build_resultArr('UR002', FALSE, 0,'NOT FOUND USER', null );
            http_data(200, $resultArr, $this);
        }
        $token = $this->receive_data['token'];
        set_reids_key($token,json_encode($user_info[0]),0,86400);
        $resultArr = build_resultArr('UR000', TRUE, 0, "OK", $token);
        http_data(200, $resultArr, $this);
    }
    // 根据token获取存储的用户信息
    public function get_user_info_token(){
        $token = $this->receive_data['token'];
        $resultArr = build_resultArr('ULA000', TRUE, 0, "OK", RedisGet($token));
        http_data(200, $resultArr, $this);
    }
    // 返回请求JSSDK用数据
    public function request_jssdk(){
        $url = $this->receive_data['url'];
        $APIs = $this->receive_data['sdk_arr'];
        $debug = $this->receive_data['is_debug'];
        $res = request_jssdk($APIs,$debug,$url);
        $resultArr = build_resultArr('RJ000', TRUE, 0, "OK", $res);
        http_data(200, $resultArr, $this);
    }
    // 根据手机号码发送验证码短信
    public function send_message(){
        $appid = 1400159743;
        $key = "49b360a1ba1a7dd2bac744bd0395658a";
        $send_info = array();
        $send_info[0] = rand(1111, 9999);
        $send_info[1] = 5;
        $s_sender = new SmsSingleSender($appid, $key);
        $requestSMS = $s_sender->sendWithParam("86", $this->receive_data['phone'], "1156354", $send_info, "职技通");
        $res = json_decode($requestSMS, true);
        if($res['errmsg'] != "OK"){
            $resultArr = build_resultArr('RJ001', FALSE, 0, "OK", null);
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('RJ000', TRUE, 0, "OK", $send_info[0]);
        http_data(200, $resultArr, $this);
    }
    // 返回公众号用支付信息
    public function get_pay_info(){
        $description = $this->receive_data['description'];
        $openid = $this->receive_data['openid'];
        $price = $this->receive_data['price'];
        $id = get_random_id(32,'ZJT');
        $prepay_info = get_prepay_id($id,$price,$openid,$description);
        $prepay_id = $prepay_info['prepay_id'];
        $config = pay_gzh($prepay_id);
        $resultArr = build_resultArr('GPI000', TRUE, 0, "ok", [$id,$config]);
        http_data(200, $resultArr, $this);
    }
    // 返回用户支付用信息
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
    // 获取测试用code
    public function get_url_param(){
        $res = array();
        $param_str = $_SERVER["QUERY_STRING"];
        $param_arr = explode("&",$param_str);
        $code_arr = explode("=",$param_arr[0]);
        $token_arr = explode("=",$param_arr[1]);
        return [$code_arr[1],$token_arr[1]];
        // for($i = 0; $i < count($param_arr); $i++){
        //     $tamp = explode("=",$param_arr[$i]);
        //     $res[$tamp[0]] = $tamp[1];
        // }
        // return $res;
    }
}