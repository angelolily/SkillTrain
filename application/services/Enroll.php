<?php
class Enroll extends HTY_service{
    public function __construct(){
        parent::__construct();
        $this->load->helper('tool');
        $this->load->model('Sys_Model');
    }
    public function check_enroll_num($data){
        //判断当前报名人数是否达到限额
        $where = array('spec_id'=>$data['spec_id']);
        return $this->Sys_Model->table_seleRow("amount",'specification',$where);
    }
    public function update_enroll_num($data){
        //可报名的名额减一
        $where = array('spec_id'=>$data['spec_id']);
        $update = array('amount'=>$data['new_num']);
        return $this->Sys_Model->table_updateRow("specification",$update,$where);
    }
    public function set_enroll($data){
        $enroll = $data['form'];
        $enroll['sign_type'] = '比赛';
        $enroll['sign_competition_id'] = $data['competition_id'];
        $enroll['competition_name'] = $data['competition_name'];
        $enroll['DeptId'] = $data['DeptId'];
        $enroll['DeptName'] = $data['DeptName'];
        $enroll['Phone'] = $data['Phone'];
        $enroll['sign_statue'] = $data['order_pay_statue'];
        $enroll['members_id'] = $data['members_openid'];
        $enroll['sign_order_id'] = $data['last_order_id'];
        $enroll['sign_created_by'] = 'HFTX_Sys';
        $enroll['sign_created_time'] = date('Y-m-d H:i:s');
        return $this->Sys_Model->table_addRow("sign_up",$enroll);
    }
    public function set_order($data){
        $order = array(
            'order_id' => get_random_tool(4).time(),
            'order_datetime' => $data['time_stamp'],
            'order_statue' => $data['sign_statue'],
            'order_product' => $data['competition_name'],
            'order_type' => '比赛',
            'order_format' => $data['DeptName'],
            'order_num' => 1,
            'order_price' => $data['unit_price'],
            'order_customer_name' => $data['form']['sign_name'],
            'order_customer_phone' => $data['form']['sign_phone'],
            'members_id' => $data['members_openid'],
            'order_capid' => $data['competition_id'],
            'order_prepay_id' => $data['prepay_id'],
            'order_mic_id' => $data['mic_id'],
            'order_deptid' => $data['DeptId'],
            'order_lg_cover' => $data['competition_cover'],
            'created_by' => 'HFTX_Sys',
            'created_time' => date('Y-m-d H:i:s'),
        );
        $res = $this->Sys_Model->table_addRow("order",$order);
        if(!$res){
            return $res;
        }
        return $this->db->insert_id();
    }
    public function get_sign_info($data){
        $where = array('sign_id'=>$data['sign_id']);
        return $this->Sys_Model->table_seleRow("*",'sign_up',$where);
    }
    public function edit_from_sign($data){
        $where = array('sign_id'=>$data['sign_id']);
        $update = array('sign_from'=>$data['sign_from']);
        return $this->Sys_Model->table_updateRow("sign_up",$update,$where);
    }
    public function edit_from_vote($data){
        $where = array(
            'DeptId'=>$data['DeptId'],
            'vote_name'=>$data['sign_name'],
            'competition_id'=>$data['competition_id']
        );
        $update = array('sign_from'=>$data['sign_from']);
        return $this->Sys_Model->table_updateRow("vote",$update,$where);
    }
    public function update_image_dir($data){
        $where = array('sign_id'=>$data['sign_id']);
        $update = array('sign_image'=>$data['dir_name']);
        return $this->Sys_Model->table_updateRow("sign_up",$update,$where);
    }
    public function update_picture_dir($data){
        $where = array('sign_id'=>$data['sign_id']);
        $update = array('sign_picture'=>$data['dir_name']);
        return $this->Sys_Model->table_updateRow("sign_up",$update,$where);
    }
    public function update_picture_dir_vote($data){
        $where = array(
            'DeptId'=>$data['DeptId'],
            'vote_name'=>$data['sign_name'],
            'competition_id'=>$data['competition_id']
        );
        $update = array('vote_image'=>$data['dir_name']);
        return $this->Sys_Model->table_updateRow("vote",$update,$where);
    }
    public function get_order_price($data){
        $where = array('order_autoid'=>$data['sign_order_id']);
        return $this->Sys_Model->table_seleRow("order_price",'order',$where);
    }
    public function del_sign_info_free($data): bool
    {
        $returnInfo = true;
        $this->db->trans_begin();
        $where_enroll = array(
            'sign_id'=>$data['sign_id']
        );
        $where_order = array(
            'order_autoid'=>$data['sign_order_id']
        );
        $this->Sys_Model->table_del('sign_up',$where_enroll);
        $this->Sys_Model->table_del('order',$where_order);
        $row=$this->db->affected_rows();
        if (($this->db->trans_status() === FALSE) && $row<=0){
            $this->db->trans_rollback();
            $returnInfo = false;
        }else{
            $this->db->trans_commit();
        }
        return $returnInfo;
    }
    public function del_sign_info($data): bool
    {
        $returnInfo = true;
        $this->db->trans_begin();
        $where_enroll = array(
            'sign_id'=>$data['sign_id'],
            'sign_statue'=>'未付款'
        );
        $where_order = array(
            'order_autoid'=>$data['sign_order_id'],
            'order_statue'=>'未付款'
        );
        $this->Sys_Model->table_del('sign_up',$where_enroll);
        $this->Sys_Model->table_del('order',$where_order);
        $row=$this->db->affected_rows();
        if (($this->db->trans_status() === FALSE) && $row<=0){
            $this->db->trans_rollback();
            $returnInfo = false;
        }else{
            $this->db->trans_commit();
        }
        return $returnInfo;
    }
    public function get_key_text($data){

    }
    public function get_sign_model_list($data){
        $where = array('sign_model_type'=>$data['model_type']);
        return $this->Sys_Model->table_seleRow("*",'sign_model',$where);
    }
    public function add_sign_model($data){
        $new_data = array(
            'sign_model_type'=>$data['sign_model_type'],
            'sign_model_name'=>$data['sign_model_name']
        );
        $this->Sys_Model->table_addRow("sign_model",$new_data);
        return $this->db->insert_id();
    }
    public function get_sign_index($data){
        $where = array('sign_relevancy_id'=>$data['model_id']);
//        $this->Sys_Model->table_seleRow("*", 'sign_index', $where);
//        $this->db->select('*');
//        $this->db->where($where);
//        $this->db->order_by('sign_index_index', 'ASC');
//        $query = $this->db->get('sign_index');
//        return $query->result_array();
        return $this->Sys_Model->table_seleRow_limit("*", 'sign_index', $where, [], 999, 0, 'sign_index_index', 'ASC');
    }
    public function save_index_edition($data){
        $where = array('sign_index_id'=>$data['sign_index_id']);
        $update = array(
            'sign_control_title'=>$data['sign_control_title'],
            'sign_control_tip'=>$data['sign_control_tip'],
            'sign_is_require'=>$data['sign_is_require']
        );
        return $this->Sys_Model->table_updateRow("sign_index",$update,$where);
    }
    public function add_index_option($data){
        return $this->Sys_Model->table_addRow("sign_index",$data);
    }
    public function del_index_option($data){
        $where = array('sign_index_id'=>$data['sign_index_id']);
        return $this->Sys_Model->table_del("sign_index",$where);
    }
    public function get_enroll_info($data){
        $where = array('sign_order_id'=>$data['order_id']);
        return $this->Sys_Model->table_seleRow("*",'sign_up',$where);
    }
    public function get_activity_info($data){
        $where = array('activity_id'=>$data['activity_id']);
        return $this->Sys_Model->table_seleRow("*",'activity',$where);
    }
    public function set_activity_info($data): bool
    {
        $returnInfo = true;
        $this->db->trans_begin();
        $this->Sys_Model->table_addRow("order",$data['order_info']);
        $data['form']['sign_order_id'] = $this->db->insert_id();
        $this->Sys_Model->table_addRow("sign_up",$data['form']);
        $row=$this->db->affected_rows();
        if (($this->db->trans_status() === FALSE) && $row<=0){
            $this->db->trans_rollback();
            $returnInfo = false;
        }else{
            $this->db->trans_commit();
        }
        return $returnInfo;
    }
    public function get_course_info($data){
        $where = array('course_id'=>$data['course_id']);
        return $this->Sys_Model->table_seleRow("*",'course',$where);
    }
    public function check_exits_state($data){
        $where = array(
            'sign_name'=>$data['name'],
            'sign_phone'=>$data['phone'],
            'sign_competition_id'=>$data['id'],
            'sign_type'=>$data['type'],
            'sign_statue'=>'成功报名'
        );
        return $this->Sys_Model->table_seleRow("sign_id",'sign_up',$where);
    }
    public function set_order_enroll_data($data){
        $returnInfo = true;
        $this->db->trans_begin();
        $this->Sys_Model->table_addRow("order",$data['order_info']);
        $data['form']['sign_order_id'] = $this->db->insert_id();
        $this->Sys_Model->table_addRow("sign_up",$data['form']);
        $this->Sys_Model->execute_sql($data['sql_code'],2);
        $row=$this->db->affected_rows();
        if (($this->db->trans_status() === FALSE) && $row<=0){
            $this->db->trans_rollback();
            $returnInfo = false;
        }else{
            $this->db->trans_commit();
        }
        $res = $returnInfo;
        if($returnInfo){
            $res = $data['form']['sign_order_id'];
        }
        return $res;
    }
    public function get_user_info($data){
        $where = array('members_openid'=>$data['openid']);
        return $this->Sys_Model->table_seleRow("*",'members',$where);
    }
    public function update_share_user_point($data): bool
    {
        $returnInfo = true;
        $this->db->trans_begin();
        // 更新用户积分
        $sql_user = "UPDATE members SET members_integral = members_integral+{$data['num']} WHERE members_openid = '{$data['openid']}'";
        $this->Sys_Model->execute_sql($sql_user,2);
        // 插入积分表
        $new_date_point = array(
            'point_user_openid'=>$data['openid'],
            'point_num'=>'-'.$data['num'],
            'point_source'=>$data['name'],
            'point_source_order'=>$data['id'],
            'point_creat_time'=>date('Y-m-d H:i:s'),
        );
        $this->Sys_Model->table_addRow("point",$new_date_point);
        // 插入推荐人表
        $new_date_referrer = $data['referrer'];
        $this->Sys_Model->table_addRow("referrer",$new_date_referrer);
        $row=$this->db->affected_rows();
        if (($this->db->trans_status() === FALSE) && $row<=0){
            $this->db->trans_rollback();
            $returnInfo = false;
        }else{
            $this->db->trans_commit();
        }
        return $returnInfo;
    }
    public function update_user_point($data){
        $num = (int)$data['user_point'];
        $openid = $data['order_info']['members_id'];
        $sql =
            "UPDATE members SET members_integral = "
            .$num
            ." WHERE members_openid = '"
            .$openid
            ."';";
        return $this->Sys_Model->execute_sql($sql,2);
        /*
        $where = array(
            'members_openid'=>$data['order_info']['members_id']
        );
        $update = array('members_integral'=>(int)$data['user_point']);
        return $this->Sys_Model->table_updateRow("members",$update,$where);
        */
    }
    public function get_sign_data($data){
        $sql_code = "select s.*,o.order_id from sign_up as s left join `order` o on s.sign_order_id = o.order_autoid where sign_competition_id = {$data['aim_id']} and sign_type = '{$data['type']}'";
        if($data['type'] === '活动'){
            $sql_code = "select s.*,o.order_id,p.point_creat_time from sign_up as s left join `order` o on s.sign_order_id = o.order_autoid left join point as p on o.order_autoid = p.point_source_order COLLATE utf8_general_ci where sign_competition_id = {$data['aim_id']} and sign_type = '{$data['type']}'";
        }
        $sql_code = $sql_code." order by sign_created_time desc limit {$data['offset']},{$data['rows']}";
        return $this->Sys_Model->execute_sql($sql_code);
        //$where = array(
        //    'sign_type'=>$data['type'],
        //    'sign_competition_id'=>$data['aim_id']
        //);
        //return $this->Sys_Model->table_seleRow_limit("*","sign_up",$where,[],$data['rows'],$data['offset'],"sign_created_time,sign_id","DESC");
    }
    public function get_sign_data_num($data){
        $where = array(
            'sign_type'=>$data['type'],
            'sign_competition_id'=>$data['aim_id']
        );
        return $this->Sys_Model->table_seleRow("count(*)","sign_up",$where);
    }
    public function check_vote_data($data){
        $where = array(
            'competition_id'=>$data['competition_id'],
            'DeptId'=>$data['DeptId']
        );
        return $this->Sys_Model->table_seleRow("count(*)","vote",$where);
    }
    public function update_user_info($data){
        $where = array('members_openid'=>$data['user_info']['members_openid']);
        return $this->Sys_Model->table_updateRow("members",$data['user_info'],$where);
    }
}