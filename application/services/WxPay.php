<?php
    class WxPay extends HTY_service{
        public function __construct(){
            parent::__construct();
            $this->load->helper('tool');
            $this->load->model('Sys_Model');
        }
        public function user_login_with_code($openid) {
            $where = array('members_openid'=>$openid);
            $field = "members_nickname,members_openid,members_photo,members_name,members_phone,members_sex,members_integral,members_card,members_education,members_isTrue,members_address,members_WorkUnit,members_bankid,members_bankname,";
            return $this->Sys_Model->table_seleRow($field,'members',$where);
        }
        public function user_login_with_account($data){
            $where = array(
                'members_phone'=>$data['phone'],
                'members_pwd'=>$data['pwd'],
            );
            $field = "members_nickname,members_openid,members_photo,members_name,members_phone,members_sex,members_integral,members_card,members_education,members_isTrue,members_address,members_WorkUnit,members_bankid,members_bankname,";
            return $this->Sys_Model->table_seleRow($field,'members',$where);
        }
        public function user_login($where){
            $field = "members_nickname,members_openid,members_photo,members_name,members_phone,members_sex,members_integral,members_card,members_education,members_isTrue,members_address,members_WorkUnit,members_bankid,members_bankname,";
            return $this->Sys_Model->table_seleRow($field,'members',$where);
        }
        public function update_user_openid($phone,$openid){
            $where = array('members_phone'=>$phone);
            $update = array('members_openid'=>$openid);
            return $this->Sys_Model->table_updateRow("members",$update,$where);
        }
    }
?>