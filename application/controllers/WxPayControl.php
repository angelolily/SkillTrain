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
        $echostr = $_GET["echostr"];
        $token = "HTYZJT";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            if($echostr){
                header('content-type:text');
                echo($echostr);
                exit;
            }
        }else{
            echo(false);
        }
    }
    // 教师根据微信授权code登陆
    public function teacher_login_with_code(){
        $url_param_t = $this->get_url_param();
        $user_wx = get_user_wx_info($url_param_t[0],'snsapi_userinfo');
        if(!$user_wx){
            header('Location: https://admin.wd-jk.com/codetoany/getcode.php?auk=teacher_login');
            return;
        }
        $res = $this->wxpay->teacher_login_with_code($user_wx['openid']);
        if(!$res){
            header('Location: https://admin.wd-jk.com/codetoany/getcode.php?auk=teacher_login');
            return;
        }
        $user_info = $res[0];
        $key_arr = array();
        foreach($user_info as $key => $value){
            $key = ucwords(str_replace(['-', '_'], ' ', $key));
            $key_e = lcfirst(str_replace(' ', '', $key));
            array_push($key_arr,$key_e);
        }
        $user_info_e = array_combine($key_arr,$user_info);
        set_reids_key($url_param_t[1],json_encode($user_info_e),0,86400);
        header('Location: http://teacher.wd-jk.com/#/');
        return;
    }
    // 用户根据微信授权code登陆
    public function user_login_with_code(){
        $url_param = $this->get_url_param();
        $user_wx = get_user_wx_info($url_param[0],'snsapi_userinfo');
        $res = $this->wxpay->user_login_with_code($user_wx['openid']);
        if(!$res){
            // $url = urlencode('http://gzh.wd-jk.com/#/login');
            // $aim_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx5d276a1e3d25bce5&redirect_uri="
            //             .$url
            //             ."&response_type=code&scope=snsapi_base&state=ZJT#wechat_redirect";
            header('Location: https://admin.wd-jk.com/codetoany/getcode.php?auk=user_login');
        }else{
            set_reids_key($url_param[1],json_encode($res[0]),0,86400);
            header('Location: http://gzh.wd-jk.com/#/my');
        }
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
    // 仅根据code获取用户数据
    public function user_default_login(){
        $code = $this->receive_data['code'];
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx5d276a1e3d25bce5&secret=530ba6273fa62b9dcb10658f2231b6b7&code=".$code."&grant_type=authorization_code";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output_url = curl_exec($ch);
        curl_close($ch);
        $res_url = json_decode($output_url, TRUE);
        $user_wx = $res_url;
        // $user_wx = get_user_wx_info($code,'snsapi_base');
        if(array_key_exists("errcode",$user_wx) && $user_wx['errcode']==40163){
            $user_wx = false;
        }
        if(!$user_wx){
            $resultArr = build_resultArr('UDL001', FALSE, 0,'CODE ERR', null );
            http_data(200, $resultArr, $this);
        }
        $res = $this->wxpay->user_login_with_code($user_wx['openid']);
        if(!$res){
            $resultArr = build_resultArr('UDL002', FALSE, 0,'cant find user', null );
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('UDL000', TRUE, 0,'ok', $res[0] );
        http_data(200, $resultArr, $this);
    }
    // 用户注册
    public function user_register(){
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
    // 根据token获取存储的信息
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
        $beta = array_key_exists('is_beta',$this->receive_data) ? $this->receive_data['is_beta'] : false;
        $json = array_key_exists('json',$this->receive_data) ? $this->receive_data['json'] : true;
        $openTagList = array_key_exists('openTagList',$this->receive_data) ? $this->receive_data['openTagList'] : [];
        $res = request_jssdk($APIs,$debug,$url,$beta,$json,$openTagList);
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
        $id = $this->receive_data['order_mic_id'];
        $prepay_info = get_prepay_id($id,$price,$openid,$description);
        if(!$prepay_info){
            $resultArr = build_resultArr('GPI001', FALSE, 0, "err prepay info", null);
            http_data(200, $resultArr, $this);
        }
        $prepay_id = $prepay_info['prepay_id'];
        $config = pay_gzh($prepay_id);
        if(!$config){
            $resultArr = build_resultArr('GPI002', FALSE, 0, "err pay config", $prepay_id);
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('GPI000', TRUE, 0, "ok", [$id,$config,$prepay_id]);
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
    // 未付款订单付款后更新订单
    public function update_order_info(){
        $res = $this->wxpay->update_order_info($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('UOI001', FALSE, 204,'更新订单信息错误，请联系客服', null);
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('UOI000', TRUE, 0,'更新订单信息成功', null);
        http_data(200, $resultArr, $this);
    }
    //处理微信支付回调
    public function notify(){
        // 回送微信成功响应
        get_notify();
        // 重新获取数据进行处理
        $test_xml  = file_get_contents("php://input");
        $json_xml = json_encode(simplexml_load_string($test_xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        $result = json_decode($json_xml, true);
        if($result){
            if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
                $order_mic_id = $result['out_trade_no'];
                $openid = $result['openid'];
                $this->wxpay->update_order_info_notify($openid,$order_mic_id);
            }
        }
    }
    // 菜单
    public function get_menu(){
        $res = get_menu_now();
        $resultArr = build_resultArr('ULA000', TRUE, 0, "OK", $res);
        http_data(200, $resultArr, $this);
    }
    // 设置菜单
    public function set_menu(){
        $menu = [
            [
                'name'=>'培训中心',
                'sub_button'=>[
                    [
                        'type'=>'view',
                        'name'=>'学生页面',
                        'url'=>'http://gzh.wd-jk.com/#/'
                    ],
                    [
                        'type'=>'view',
                        'name'=>'教师页面',
                        'url'=>'http://teacher.wd-jk.com/#/'
                    ]
                ]
            ],
            [
                'name'=>'产品中心',
                'sub_button'=>[
                    [
                        'type'=>'media_id',
                        'name'=>'沃顿干细胞',
                        'media_id'=>'IxCZZG3rY_D23YqR2bgM4B4rueAoIV7WEpqmAQmykfM'
                    ],
                    [
                        'type'=>'media_id',
                        'name'=>'沃顿免疫细胞',
                        'media_id'=>'IxCZZG3rY_D23YqR2bgM4B1PiXwMGm9hiaI2U95JqQo'
                    ],
                    [
                        'type'=>'media_id',
                        'name'=>'沃顿海外健康',
                        'media_id'=>'IxCZZG3rY_D23YqR2bgM4PwFecabmNDeYpehUvU5htU'
                    ]
                ]
            ],
            [
                'name'=>'关于沃顿',
                'sub_button'=>[
                    [
                        'type'=>'view',
                        'name'=>'沃顿简介',
                        'url'=>'https://x.eqxiu.com/s/lkyKMiXh'
                    ],
                    [
                        'type'=>'media_id',
                        'name'=>'沃顿风采',
                        'media_id'=>'IxCZZG3rY_D23YqR2bgM4O_JqDZABf5B1DgED7FTbfk'
                    ],
                    [
                        'type'=>'click',
                        'name'=>'沃顿系统',
                        'key'=>'WDSys_info'
                    ],
                ]
            ]
        ];
        $res = set_menu($menu);
        $resultArr = build_resultArr('ULA000', TRUE, 0, "OK", $res);
        http_data(200, $resultArr, $this);
    }
    // 获取文章列表
    public function get_article(){
        $appid = 'wx5d276a1e3d25bce5';
        $secret = '530ba6273fa62b9dcb10658f2231b6b7';
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($output, TRUE);
        $access_token = $res['access_token'];
        $url_art = 'https://api.weixin.qq.com/cgi-bin/freepublish/batchget?access_token='.$access_token;
        $body = [
            'offset'=>0,
            'count'=>20,
            "no_content"=>1
        ];
        $payload = json_encode($body);
        $ch_art = curl_init();
        curl_setopt($ch_art, CURLOPT_URL, $url_art);
        curl_setopt($ch_art, CURLOPT_POST, TRUE);
        curl_setopt($ch_art, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch_art, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch_art, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch_art, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch_art, CURLOPT_HTTPHEADER, array(

                'Content-Type: application/json',

                'Content-Length: ' . strlen($payload))

        );
        $output_url = curl_exec($ch_art);
        curl_close($ch_art);
        $res_url = json_decode($output_url, TRUE);
        $resultArr = build_resultArr('GPI000', TRUE, 0,'openid', $res_url );
        http_data(200, $resultArr, $this);
    }
    // 发送模板消息
    public function send_model_msg(){
        $open_id = $this->receive_data['openid'];
        if(is_array($open_id)){
            for($i=0; $i<count($open_id); $i++)
            $tamp_data = array(
                'openid'=>$open_id[$i],
                'template_id'=>$this->receive_data['template_id'],
                'aim_page'=>$this->receive_data['aim_page'],
                'aim_miniprogram'=>$this->receive_data['aim_miniprogram'],
                'msg_body'=>$this->receive_data['msg_body'],
            );
            send_model_msg($tamp_data);
        }else{
            send_model_msg($this->receive_data);
        }
    }
    // 获取code
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
    // 测试code是否合法
    public function test_code(){
        $code = $this->receive_data['code'];
        // $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx5d276a1e3d25bce5&secret=530ba6273fa62b9dcb10658f2231b6b7&code=".$code."&grant_type=authorization_code";
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $output_url = curl_exec($ch);
        // curl_close($ch);
        // $res_url = json_decode($output_url, TRUE);
        // if(!$res_url){
        //     $resultArr = build_resultArr('GPI000', FALSE, 0,'openid', $user_info );
        //     http_data(200, $resultArr, $this);
        // }
        $res_url = get_user_wx_info($code,'snsapi_base');
        $resultArr = build_resultArr('GPI000', TRUE, 0,'openid', $res_url );
        http_data(200, $resultArr, $this);
    }
}