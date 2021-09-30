<?php


/**
 * Class Post ’岗位类
 */
class Classman extends HTY_service
{
    /**
     * Dept constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Sys_Model');
        $this->load->helper('tool');
        $this->load->helper('qrcode');

    }

    //新增班级与排课表
    public function addData($indData = [], $by)
    {
        $indData['a']['create_by'] = $by;
        $indData['a']['create_time'] = date('Y-m-d H:i');
        $indData['a']['class_id'] = uniqid();//生成唯一ID
        $postname = $this->Sys_Model->table_seleRow('class_id', "class_group", array('class_name' => $indData['a']['class_name']), $like = array());
        if ($postname) {
            $result = [];
            return $result;
        } else {
            $returnInfo = true;
            $this->db->trans_begin();
            $this->Sys_Model->table_addRow("class_group", $indData['a'], 1);
//            $competition=$this->db->insert_id();
            $resluts = [];
            foreach ($indData['b'] as $row) {
                $row = bykey_reitem($row, 'id');
                foreach ($indData['c'] as $item) {
                    $re = $row;
                    $re['create_by'] = $by;
                    $re['class_id'] = $indData['a']['class_id'];
                    $re['members_id'] = $item['members_id'];
                    $re['members_name'] = $item['members_name'];
                    $re['members_phone'] = $item['members_phone'];
                    $re['members_openid'] = $item['members_openid'];
                    $re['create_time'] = date('Y-m-d H:i');
                    array_push($resluts, $re);
                }
            }
            $this->Sys_Model->table_addRow("schedule", $resluts, 2);
            $sql2 = "" . $indData['c'][0]['members_id'];
            foreach ($indData['c'] as $item) {
                $sql2 = $sql2 . "," . $item['members_id'];
            }
            $sql = "update sign_up set sign_class = '1' where course_id=" . $indData['c'][0]['course_id'] . " and members_id in (" . $sql2 . ")";
            $this->Sys_Model->execute_sql($sql);
            $row = $this->db->affected_rows();
            if (($this->db->trans_status() === FALSE) && $row <= 0) {
                $this->db->trans_rollback();
                $returnInfo = false;
            } else {
                $this->db->trans_commit();
            }
            return $returnInfo;

        }
    }

    //选择人员加入班级排课的下拉
    public function membersdata($indData)
    {
        $result = $this->Sys_Model->table_seleRow('sign_id,members_id', "sign_up", array('sign_competition_id' => $indData['course_id'], "sign_statue" => "成功报名", "sign_class" => "0"), $like = array());
        $sql = "" . $result[0]['members_id'];
        foreach ($result as $item) {
            $sql = $sql . "," . $item['members_id'];
        }
        $pages = $indData['pages'];
        $rows = $indData['rows'];
        $offset=($pages-1)*$rows;//计算偏移量
        $sql1 = "select members_id,members_nickname,members_openid,members_name,members_phone from members where members_id in (" . $sql . ")  limit ".$offset.",".$rows;
        $results['data'] = $this->Sys_Model->execute_sql($sql1);
        $sql = "select members_id,members_nickname,members_openid,members_name,members_phone from members where members_id in (" . $sql . ") ";
        $resul = $this->Sys_Model->execute_sql($sql);
        $results['total']=count($resul);
        return $results;
    }

    //获取班级表
    public function getclass($searchWhere = []) //查询到赛事表
    {
        $like = "";
        if (count($searchWhere) > 0) {
            if ($searchWhere['class_name'] != '') {//班级名称
                $like = $like . " and class_name like  '%{$searchWhere['class_name']}%' ";
            }
            if ($searchWhere['course_name'] != '') {//课程名称
                $like = $like . " and course_name like  '%{$searchWhere['course_name']}%' ";
            }
            if ($searchWhere['begin'] != '' and $searchWhere['end'] != '') {
                $like = $like . " and begin_time between '" . $searchWhere['begin'] . "' and '" . $searchWhere['end'] . "'";
            }
            $pages = $searchWhere['pages'];
            $rows = $searchWhere['rows'];
            $deptTmpArr = $this->get_class_groupdata($pages, $rows, $like);
        }
        return $deptTmpArr;
    }

//搜索班级表
    public function get_class_groupdata($pages, $rows, $wheredata)
    {
        //Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
        $offset = ($pages - 1) * $rows;//计算偏移量
        $sql_query = "Select * from class_group  where  1=1  ";
        $sql_query_where = $sql_query . $wheredata;
        if ($wheredata != "") {
            $sql_query = $sql_query_where;
        }
        $sql_query_total = $sql_query;
        $sql_query = $sql_query . " order by create_time desc limit " . $offset . "," . $rows;
        $query = $this->db->query($sql_query);
        $ss = $this->db->last_query();
        $r_total = $this->db->query($sql_query_total)->result_array();
        $row_arr = $query->result_array();
        $result['total'] = count($r_total);//获取总行数
        $result["data"] = $row_arr;
        $result["alldata"] = $r_total;
        return $result;
    }

//获取班级表
    public function getschedule($searchWhere = []) //查询到赛事表
    {
        $like = "";
        if (count($searchWhere) > 0) {
            $like = $like . " and  class_id =  '" . $searchWhere['class_id'] . "'";
            $pages = $searchWhere['pages'];
            $rows = $searchWhere['rows'];
            $deptTmpArr = $this->get_scheduledata($pages, $rows, $like);
        }
        return $deptTmpArr;
    }

//搜索排课表
    public function get_scheduledata($pages, $rows, $wheredata)
    {
        //Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
        $offset = ($pages - 1) * $rows;//计算偏移量
        $sql_query = "Select DISTINCT class_id,course_name,class_num,school_time,home_time,class_romm,teacher,rate,members_name,members_id,members_openid,members_phone from schedule  where  1=1  ";
        $sql_query_where = $sql_query . $wheredata;
        if ($wheredata != "") {
            $sql_query = $sql_query_where;
        }
        $sql_query_total = $sql_query;
        $sql_query = $sql_query . " order by class_num desc limit " . $offset . "," . $rows;
        $query = $this->db->query($sql_query);
        $ss = $this->db->last_query();
        $r_total = $this->db->query($sql_query_total)->result_array();
        $row_arr = $query->result_array();
        $result['total'] = count($r_total);//获取总行数
        $result["data"] = $row_arr;
        $result["alldata"] = $r_total;
        return $result;
    }

//删除
    public function delclass_group($postId = [])
    {
        $returnInfo = true;
        $this->db->trans_begin();
        $this->Sys_Model->table_del("class_group", $postId);
        $this->Sys_Model->table_del("schedule", $postId);
        $row = $this->db->affected_rows();
        if (($this->db->trans_status() === FALSE) && $row <= 0) {
            $this->db->trans_rollback();
            $returnInfo = false;
        } else {
            $this->db->trans_commit();
        }
        return $returnInfo;
    }

//修改
    public function modifyclass_group($values, $by)
    {
        $values['a']['update_by'] = $by;
        $values['a']['update_time'] = date('Y-m-d H:i');
        $postname = $this->Sys_Model->table_seleRow('class_id', "class_group", array('class_name' => $values['a']['class_name']), $like = array());//比对班级名称是否修改
        if ($postname) {
            if ($postname[0]['class_id'] == $values['a']['class_id']) {
                if ($values['a']['ID'] == "true") {
                    $returnInfo = true;
                    $this->db->trans_begin();
                    $values['a'] = bykey_reitem($values['a'], 'ID');
                    $this->Sys_Model->table_updateRow('class_group', $values['a'], array('class_id' => $values['a']['class_id']));//修改班级信息
                    $sign_class = $this->Sys_Model->table_seleRow('members_id', "schedule", array('class_id' => $values['a']['class_id']), $like = array());//查出所有会员id（并没有去重）
                    $items_class = [];
                    foreach ($sign_class as $ite) {
                        $ite['sign_class'] = "0";
                        array_push($items_class, $ite);
                    }
                    $this->Sys_Model->table_updateBatchRow("members", $items_class, "members_id");
//将人员与班级解绑
                    $this->Sys_Model->table_del("schedule", array('class_id' => $values['a']['class_id']));
//删除排课表所有相关排课
                    $resluts = [];
                    foreach ($values['b'] as $row) {
                        $row = bykey_reitem($row, 'id');
                        foreach ($values['c'] as $item) {
                            $re = $row;
                            $re['create_by'] = $by;
                            $re['class_id'] = $values['a']['class_id'];
                            $re['members_id'] = $item['members_id'];
                            $re['members_name'] = $item['members_name'];
                            $re['members_phone'] = $item['members_phone'];
                            $re['members_openid'] = $item['members_openid'];
                            $re['create_time'] = date('Y-m-d H:i');
                            array_push($resluts, $re);
                        }
                    }
                    //重新排课
                    $this->Sys_Model->table_addRow("schedule", $resluts, 2);
                    $sql2 = "" . $values['c'][0]['members_id'];
                    foreach ($values['c'] as $item) {
                        $sql2 = $sql2 . "," . $item['members_id'];
                    }
                    $sql = "update sign_up set sign_class = '1' where course_id=" . $values['c'][0]['course_id'] . " and members_id in (" . $sql2 . ")";
                    $this->Sys_Model->execute_sql($sql);
                    //重新树立绑定标识
                    $row = $this->db->affected_rows();
                    if (($this->db->trans_status() === FALSE) && $row <= 0) {
                        $this->db->trans_rollback();
                        $returnInfo = false;
                    } else {
                        $this->db->trans_commit();
                    }
                    return $returnInfo;
                } else {
                    $values['a'] = bykey_reitem($values['a'], 'ID');
                    $reslut = $this->Sys_Model->table_updateRow('class_group', $values['a'], array('class_id' => $values['a']['class_id']));//修改班级信息
                    return $reslut;
                }
            }
        } else {
            if ($values['a']['ID'] == "true") {
                $returnInfo = true;
                $this->db->trans_begin();
                $values['a'] = bykey_reitem($values['a'], 'ID');
                $this->Sys_Model->table_updateRow('class_group', $values['a'], array('class_id' => $values['a']['class_id']));//修改班级信息
                $sign_class = $this->Sys_Model->table_seleRow('members_id', "schedule", array('class_id' => $values['a']['class_id']), $like = array());//查出所有会员id（并没有去重）
                $items_class = [];
                foreach ($sign_class as $ite) {
                    $ite['sign_class'] = "0";
                    array_push($items_class, $ite);
                }
                $this->Sys_Model->table_updateBatchRow("members", $items_class, "members_id");
//将人员与班级解绑
                $this->Sys_Model->table_del("schedule", array('class_id' => $values['a']['class_id']));
//删除排课表所有相关排课
                $resluts = [];
                foreach ($values['b'] as $row) {
                    $row = bykey_reitem($row, 'id');
                    foreach ($values['c'] as $item) {
                        $re = $row;
                        $re['create_by'] = $by;
                        $re['class_id'] = $values['a']['class_id'];
                        $re['members_id'] = $item['members_id'];
                        $re['members_name'] = $item['members_name'];
                        $re['members_phone'] = $item['members_phone'];
                        $re['members_openid'] = $item['members_openid'];
                        $re['create_time'] = date('Y-m-d H:i');
                        array_push($resluts, $re);
                    }
                }
                //重新排课
                $this->Sys_Model->table_addRow("schedule", $resluts, 2);
                $sql2 = "" . $values['c'][0]['members_id'];
                foreach ($values['c'] as $item) {
                    $sql2 = $sql2 . "," . $item['members_id'];
                }
                $sql = "update sign_up set sign_class = '1' where course_id=" . $values['c'][0]['course_id'] . " and members_id in (" . $sql2 . ")";
                $this->Sys_Model->execute_sql($sql);
                //重新树立绑定标识
                $row = $this->db->affected_rows();
                if (($this->db->trans_status() === FALSE) && $row <= 0) {
                    $this->db->trans_rollback();
                    $returnInfo = false;
                } else {
                    $this->db->trans_commit();
                }
                return $returnInfo;
            } else {
                $values['a'] = bykey_reitem($values['a'], 'ID');
                $reslut = $this->Sys_Model->table_updateRow('class_group', $values['a'], array('class_id' => $values['a']['class_id']));//修改班级信息
                return $reslut;
            }
        }
    }


}







