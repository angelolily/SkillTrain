<?php
class UserOrder extends HTY_service{
    public function __construct(){
        parent::__construct();
        $this->load->helper('tool');
        $this->load->model('Sys_Model');
    }
    public function set_enroll_form($data){
        $returnInfo = true;
        $this->db->trans_begin();

        $this->Sys_Model->table_addRow('order',$data['order_form']);
        $order_auto_id = $this->db->insert_id();
        $data['enroll_form']['sign_order_id'] = $order_auto_id;
        $this->Sys_Model->table_addRow('sign_up',$data['enroll_form']);
        $sign_id = $this->db->insert_id();
        $data_order = array('order_sign_id'=>$order_auto_id);
        $where_order = array('order_autoid'=>$order_auto_id);
        $this->Sys_Model->table_updateRow('order', $data_order, $where_order);

        $row=$this->db->affected_rows();
        if (($this->db->trans_status() === FALSE) && $row<=0){
            $this->db->trans_rollback();
            $returnInfo = false;
        }else{
            $this->db->trans_commit();
        }
        if($returnInfo){
            return $sign_id;
        }else{
            return $returnInfo;
        }
    }
    public function get_enroll_info($data){
        $field = "sign_name,sign_sex,sign_card_num,sign_birthday,sign_phone,sign_image,sign_picture,sign_id_card_img,sign_education_certificate,members_id,competition_name,sign_competition_id,sign_order_id";
        $where = array('sign_id'=>$data['id']);
        return $this->Sys_Model->table_seleRow($field,'sign_up',$where);
    }
    public function get_order_info($data){
        $field = "*";
        $where = array('order_autoid'=>$data);
        return $this->Sys_Model->table_seleRow($field,'order',$where);
    }
    public function set_enroll_form_temp($data){
        return $this->Sys_Model->table_addRow('sign_up',$data['enroll_form']);
    }
}