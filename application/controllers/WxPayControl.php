<?php
class WxPayControl extends CI_Controller{
    private $receive_data;
    public function __construct(){
        parent::__construct();
        $this->load->helper('wx');
        $this->load->helper('tool');
        $this->load->helper('redis');
        $this->load->service('WxPay');
        $this->load->library('encrypt');
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
    // 仅获取用户openid
    public function get_openid(){
        $url_param = get_url_param();
        $user_wx = get_user_wx_info($url_param[0],'snsapi_base');
        $openid = $user_wx['openid'];
        set_reids_key($url_param[1],$openid,0,86400);
        // todo 存储openid后跳转至?
    }
    // 根据微信授权code登陆
    public function user_login_with_code(){
        $url_param = get_url_param();
        $user_wx = get_user_wx_info($url_param[0],'snsapi_userinfo');
        $res = $this->wxpay->user_login_with_code($user_wx['openid']);
        if(!$res){
            set_reids_key($url_param[1],json_encode($user_wx),0,86400);
            header('Location: http://localhost:8080/#/login');
        }
        set_reids_key($url_param[1],json_encode($res[0]),0,86400);
        header('Location: http://localhost:8080/#/my');
    }
    // 根据账号密码登陆不更新openid
    public function user_login_with_account(){
        $this->receive_data['pwd'] = $this->encrypt->encode($this->receive_data['pwd'],"ZJT");
        $res = $this->wxpay->user_login_with_account($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('ULA001', FALSE, 0,'NOT FOUND USER', null );
            http_data(200, $resultArr, $this);
        }
        set_reids_key($this->receive_data['token'],json_encode($res[0]),0,86400);
        $resultArr = build_resultArr('ULA000', TRUE, 0, "OK", $res[0]);
        http_data(200, $resultArr, $this);
    }
    // 根据账号密码登陆并在需要时更新openid
    public function user_login(){
        $where = array(
            'members_phone'=>$this->receive_data['phone'],
            'members_pwd'=>$this->encrypt->encode($this->receive_data['pwd'],"ZJT"),
        );
        $res = $this->wxpay->user_login($where);
        if(!$res){
            $resultArr = build_resultArr('UL001', FALSE, 444,'找不到用户信息', null );
            http_data(200, $resultArr, $this);
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
    // 根据token获取存储的用户信息
    public function get_user_info_token(){
        $token = $this->receive_data['token'];
        $resultArr = build_resultArr('ULA000', TRUE, 0, "OK", RedisGet($token));
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