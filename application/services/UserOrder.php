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
        $data['enroll_form']['sign_order_id'] = $this->db->insert_id();
        $this->Sys_Model->table_addRow('sign_up',$data['enroll_form']);
        $sign_id = $this->db->insert_id();

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

}