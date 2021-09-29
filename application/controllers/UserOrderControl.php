<?php
/*
 * @Author: your name
 * @Date: 2021-09-26 15:06:09
 * @LastEditTime: 2021-09-29 17:33:36
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: \SkillTrain\application\controllers\UserOrderControl.php
 */
class UserOrderControl  extends CI_Controller{
    private $receive_data;
    public function __construct(){
        parent::__construct();
        $this->load->helper('tool');
        $this->load->service('UserOrder');
        $receive = file_get_contents('php://input');
        $this->receive_data = json_decode($receive, true);
    }
    public function set_enroll_form(){
//        $sign_id = create_guid();
//        $this->receive_data['enroll_form']['sign_id'] = $sign_id;
        $this->receive_data['order_form']['order_datetime'] = date('Y-m-d H:i:s');
        $res = $this->userorder->set_enroll_form($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('SEF001', FALSE, 204,'报名信息存储错误', null );
            http_data(204, $resultArr, $this);
        }
        $resultArr = build_resultArr('SEF000', TRUE, 0,'报名信息存储成功', $res );
        http_data(200, $resultArr, $this);
    }
    
}