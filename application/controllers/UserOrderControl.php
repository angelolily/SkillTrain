<?php
class UserOrderControl  extends CI_Controller{
    private $receive_data;
    public function __construct(){
        parent::__construct();
        $this->load->helper('tool');
        $this->load->service('UserOrder');
        $receive = file_get_contents('php://input');
        $this->receive_data = json_decode($receive, true);
    }
    // 生成订单与报名信息
    public function set_enroll_form(){
        $order_mic_id = get_random_tool(32,'ZPT');
        $this->receive_data['enroll_form']['sign_created_time'] = date('Y-m-d H:i:s');
        $this->receive_data['enroll_form']['sign_created_by'] = "ZPTSys";
        $this->receive_data['order_form']['order_customer_name'] = $this->receive_data['enroll_form']['sign_name'];
        $this->receive_data['order_form']['order_mic_id'] = $order_mic_id;
        $this->receive_data['order_form']['order_datetime'] = date('Y-m-d H:i:s');
        $this->receive_data['order_form']['created_time'] = date('Y-m-d H:i:s');
        $this->receive_data['order_form']['created_by'] = "ZPTSys";
        $res = $this->userorder->set_enroll_form($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('SEF001', FALSE, 204,'报名信息存储错误', null );
            http_data(204, $resultArr, $this);
        }
        $resultArr = build_resultArr('SEF000', TRUE, 0,'报名信息存储成功', [$res,$order_mic_id] );
        http_data(200, $resultArr, $this);
    }
    // 获取报名信息
    public function get_enroll_info(){
        $res = $this->userorder->get_enroll_info($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('GEI001', FALSE, 204,'获取报名信息错误', null );
            http_data(204, $resultArr, $this);
        }
        $img_arr = [];
        $base_url='https://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
        if(array_key_exists('sign_picture',$res[0])){
            array_push($img_arr,array('sign_picture'=>$base_url.'public/enroll/'.$res[0]['sign_image'].'/'.$res[0]['sign_picture']));
        }
        if(array_key_exists('sign_id_card_img',$res[0])){
            array_push($img_arr,array('sign_id_card_img'=>$base_url.'public/enroll/'.$res[0]['sign_image'].'/'.$res[0]['sign_id_card_img']));
        }
        if(array_key_exists('sign_education_certificate',$res[0])){
            array_push($img_arr,array('sign_education_certificate'=>$base_url.'public/enroll/'.$res[0]['sign_image'].'/'.$res[0]['sign_education_certificate']));
        }
        if(array_key_exists('sign_skill_certificate',$res[0])){
            array_push($img_arr,array('sign_skill_certificate'=>$base_url.'public/enroll/'.$res[0]['sign_image'].'/'.$res[0]['sign_skill_certificate']));
        }
        $res_order = $this->userorder->get_order_info($res[0]['sign_order_id']);
        if(!$res_order){
            $resultArr = build_resultArr('GEI002', FALSE, 204,'获取报名信息错误', null );
            http_data(204, $resultArr, $this);
        }
        $resultArr = build_resultArr('GEI000', TRUE, 0,'获取报名信息成功', [$res[0],$res_order[0],$img_arr] );
        http_data(200, $resultArr, $this);
    }
}