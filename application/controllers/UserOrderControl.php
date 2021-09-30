<?php
/*
 * @Author: Anan
 * @Date: 2021-09-26 15:06:09
 * @LastEditTime: 2021-09-28 18:00:54
 * @LastEditors: Please set LastEditors
 * @Description: User order and enroll control
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
}