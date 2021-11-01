<?php
class WxPay extends HTY_service{

    public function __construct(){
        parent::__construct();
        $this->load->helper('tool');
        $this->load->model('Sys_Model');
    }
    // 用户授权登陆后获取信息
    public function user_login_with_code($openid) {
        $where = array('members_openid'=>$openid);
        $field = "members_id,members_nickname,members_openid,members_photo,members_name,members_phone,members_sex,members_integral,members_card,members_education,members_isTrue,members_address,members_WorkUnit,members_bankid,members_bankname,";
        return $this->Sys_Model->table_seleRow($field,'members',$where);
    }
    // 教师授权登陆后获取信息
    public function teacher_login_with_code($openid){
        $where = array('teacher_openid'=>$openid);
        return $this->Sys_Model->table_seleRow('*','teacher',$where);
    }
    // 用户使用账号密码登陆
    public function user_login_with_account($data){
        $where = array(
            'members_phone'=>$data['phone'],
            'members_pwd'=>$data['pwd'],
        );
        $field = "members_id,members_nickname,members_openid,members_photo,members_name,members_phone,members_sex,members_integral,members_card,members_education,members_isTrue,members_address,members_WorkUnit,members_bankid,members_bankname,";
        return $this->Sys_Model->table_seleRow($field,'members',$where);
    }
    // 用户登陆
    public function user_login($where){
        $field = "members_id,members_nickname,members_openid,members_photo,members_name,members_phone,members_sex,members_integral,members_card,members_education,members_isTrue,members_address,members_WorkUnit,members_bankid,members_bankname,members_pwd";
        return $this->Sys_Model->table_seleRow($field,'members',$where);
    }
    // 绑定用户openid
    public function update_user_openid($phone,$openid){
        $where = array('members_phone'=>$phone);
        $update = array('members_openid'=>$openid);
        return $this->Sys_Model->table_updateRow("members",$update,$where);
    }
    // 用户注册
    public function user_reg($reg_info,$wx_info){
        $new_date = array(
            'members_nickname'=>$wx_info['nickname'],
            'members_openid'=>$wx_info['openid'],
            'members_photo'=>$wx_info['headimgurl'],
            'members_name'=>$reg_info['members_name'],
            'members_phone'=>$reg_info['members_phone'],
            'members_sex'=>$wx_info['sex'],
            'members_education'=>$reg_info['members_education'],
            'members_WorkUnit'=>$reg_info['members_WorkUnit'],
            'members_bankid'=>$reg_info['members_bankid'],
            'members_bankname'=>$reg_info['members_bankname'],
            'members_pwd'=>$reg_info['members_pwd']
        );
        return $this->Sys_Model->table_addRow("members",$new_date);
    }
    // 更新订单信息
    public function update_order_info($data){
        $returnInfo = true;
        $this->db->trans_begin();

        $where_enroll = array(
            'sign_id'=>$data['sign_id']
        );
        $data_enroll = array(
            'sign_statue'=>'成功报名',
            'sign_updated_by'=>"ZPTSys",
            'sign_updated_time'=>date('Y-m-d H:i:s')
        );
        $res_enroll_update = $this->Sys_Model->table_updateRow('sign_up', $data_enroll, $where_enroll);
        $res_enroll_info = $this->Sys_Model->table_seleRow("*",'sign_up',$where_enroll);
        $enroll_info = $res_enroll_info[0];
        $where_order = array(
            'order_autoid'=>$enroll_info['sign_order_id']
        );
        $data_order = array(
            'order_statue'=>'付款成功',
            'order_mic_id'=>$data['order_mic_id'],
            'updated_by'=>"ZPTSys",
            'updated_time'=>date('Y-m-d H:i:s')
        );
        $res_order_update = $this->Sys_Model->table_updateRow('order', $data_order, $where_order);

        $row=$this->db->affected_rows();
        if (($this->db->trans_status() === FALSE) && $row<=0){
            $this->db->trans_rollback();
            $returnInfo = false;
        }else{
            $this->db->trans_commit();
        }
        return $returnInfo;
//        if($returnInfo){
//            return $sign_id;
//        }else{
//            return $returnInfo;
//        }
    }
}
