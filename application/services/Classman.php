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
        $postname = $this->Sys_Model->table_seleRow('class_id', "class_group", array('class_name' => $indData['a']['class_name']));//班级名称重复判断
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
                if($item['members_id']==$indData['c'][0]['members_id']){
                    break;
                }
                $sql2 = $sql2 . "," . $item['members_id'];
            }
            $sql = "update sign_up set sign_class = '1' where sign_competition_id=  '" . $indData['b'][0]['course_id'] . "' and members_id in (" . $sql2 . ")";
            $this->Sys_Model->execute_sql($sql,2);

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



    //选择人员加入班级排课
    public function members_data($indData)
    {
        $pages = $indData['pages'];
        $rows = $indData['rows'];
        $offset=($pages-1)*$rows;//计算偏移量

        //新增班级与修改班级使用同一接口，所以需要判断是新增调用还是修改调用
        //ID：true 新增获取，false，修改获取
        if($indData['ID']=="true"){

            $arrTotal=$this->Sys_Model->table_seleRow('sign_id', "sign_up", array('sign_competition_id' => $indData['course_id'], "sign_class !=" => "1"));
            if(count($arrTotal)>0){
                $result = $this->Sys_Model->table_seleRow_limit('members_id,sign_name as members_name,sign_phone as members_phone,members_openid', "sign_up", array('sign_competition_id' => $indData['course_id'], "sign_class !=" => "1"),[],$rows,$offset);
                $results['total']=count($arrTotal);
                $results['data']=$result;
            }
            else{
                $results['total']=0;
                $results['data']=[];
            }


        }
        else{


            $arrTotal=$this->Sys_Model->table_seleRow('sign_id', "sign_up", array('sign_competition_id' => $indData['course_id']));
            if(count($arrTotal)>0){
                //获取报名表中全部报该课程的学员
                $resultAll = $this->Sys_Model->table_seleRow_limit('members_id,sign_name as members_name,sign_phone as members_phone,members_openid', "sign_up", array('sign_competition_id' => $indData['course_id']),[],$rows,$offset);
                //获取已经排课的学员
                $getsql="select distinct members_id,members_name,members_phone,members_openid from schedule where course_id='".$indData['course_id']."' limit ".$offset.",".$rows;
                $resultData = $this->Sys_Model->execute_sql($getsql);;
                $results['total']=count($arrTotal);
                $results['data']=$resultData;
                $results['alldata']=$resultAll;
                
            }
            else{
                $results['total']=0;
                $results['data']=[];
                $results['alldata']=[];
            }


        }

       

        return $results;

    }


    // //选择人员加入班级排课的下拉
    // public function membersdata($indData)
    // {
    //     if($indData['ID']=="true"){
    //     $result = $this->Sys_Model->table_seleRow('sign_id,members_id', "sign_up", array('sign_competition_id' => $indData['course_id'], "sign_statue" => "成功报名"), $like = array());
    //     if($result){
    //         $sql = "" . $result[0]['members_id'];
    //         foreach ($result as $item) {
    //             $sql = $sql . "," . $item['members_id'];
    //         }
    //         $pages = $indData['pages'];
    //         $rows = $indData['rows'];
    //         $offset=($pages-1)*$rows;//计算偏移量
    //         $sql1 = "select members_id,members_nickname,members_openid,members_name,members_phone from members where members_id in (" . $sql . ")  limit ".$offset.",".$rows;
    //         $results['data'] = $this->Sys_Model->execute_sql($sql1);
    //         $sql2 = "select members_id,members_nickname,members_openid,members_name,members_phone from members where members_id in (" . $sql . ") ";
    //         $resul = $this->Sys_Model->execute_sql($sql2);
    //         $results['total']=count($resul);
    //         return $results;
    //     }else{
    //         return $result;
    //     }
    // }else{
    //         $result = $this->Sys_Model->table_seleRow('sign_id,members_id', "sign_up", array('sign_competition_id' => $indData['course_id'], "sign_statue" => "成功报名", "sign_class" => "1"), $like = array());
    //         if($result) {
    //             $sql = "" . $result[0]['members_id'];
    //             foreach ($result as $item) {
    //                 $sql = $sql . "," . $item['members_id'];
    //             }
    //             $pages = $indData['pages'];
    //             $rows = $indData['rows'];
    //             $offset = ($pages - 1) * $rows;//计算偏移量
    //             $sql1 = "select members_id,members_nickname,members_openid,members_name,members_phone from members where members_id in (" . $sql . ")  limit " . $offset . "," . $rows;
    //             $results['data'] = $this->Sys_Model->execute_sql($sql1);
    //             $result1 = $this->Sys_Model->table_seleRow('sign_id,members_id', "sign_up", array('sign_competition_id' => $indData['course_id'], "sign_statue" => "成功报名"), $like = array());

    //             $sqll = "" . $result1[0]['members_id'];
    //             foreach ($result1 as $item) {
    //                 $sqll= $sqll . "," . $item['members_id'];
    //             }
    //             $pages = $indData['pages'];
    //             $rows = $indData['rows'];
    //             $offset = ($pages - 1) * $rows;//计算偏移量
    //             $sql2 = "select members_id,members_nickname,members_openid,members_name,members_phone from members where members_id in (" . $sqll . ")  limit ".$offset.",".$rows;
    //             $results['alldata'] = $this->Sys_Model->execute_sql($sql2);
    //             $sql3 = "select members_id,members_nickname,members_openid,members_name,members_phone from members where members_id in (" . $sqll . ") ";
    //             $resul = $this->Sys_Model->execute_sql($sql3);
    //             $results['total']=count($resul);
    //             return  $results;
    //         }else{
    //             $result1 = $this->Sys_Model->table_seleRow('sign_id,members_id', "sign_up", array('sign_competition_id' => $indData['course_id'], "sign_statue" => "成功报名"), $like = array());
    //             if($result1){
    //                 $sql = "" . $result1[0]['members_id'];
    //                 foreach ($result1 as $item) {
    //                     $sql = $sql . "," . $item['members_id'];
    //                 }
    //                 $pages = $indData['pages'];
    //                 $rows = $indData['rows'];
    //                 $offset = ($pages - 1) * $rows;//计算偏移量
    //                 $sql2 = "select members_id,members_nickname,members_openid,members_name,members_phone from members where members_id in (" . $sql . ")  limit ".$offset.",".$rows;
    //                 $results['alldata'] = $this->Sys_Model->execute_sql($sql2);
    //                 $sql3 = "select members_id,members_nickname,members_openid,members_name,members_phone from members where members_id in (" . $sql . ") ";
    //                 $resul = $this->Sys_Model->execute_sql($sql3);
    //                 $results['total']=count($resul);
    //                 $results['data']=[];
    //                 return $results;
    //             }
    //             else{
    //                 return $result1;
    //             }

    //         }
    //     }
    // }

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
            $deptTmpArr = $this->get_scheduledata($like);
        }
        return $deptTmpArr;
    }

//搜索排课表
    public function get_scheduledata($wheredata)
    {
        //Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
        $sql_query = "Select DISTINCT class_id,course_name,class_num,school_time,home_time,class_romm,teacher_id,teacher_name,rate,members_name,members_id,members_openid,members_phone from schedule  where  1=1  ";
        $sql_query_where = $sql_query . $wheredata;
        if ($wheredata != "") {
            $sql_query = $sql_query_where;
        }
        $sql_query_total = $sql_query;
        $sql_query = $sql_query . " order by class_num ";
        $query = $this->db->query($sql_query);
        $ss = $this->db->last_query();
        $row_arr = $query->result_array();
        $result= $row_arr;
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
                if ($values['a']['ID'] == "false") {
                    $returnInfo = true;
                    $this->db->trans_begin();
                    $values['a'] = bykey_reitem($values['a'], 'ID');
//                    $values['a'] = bykey_reitem($values['a'], 'class_id');
                    $this->Sys_Model->table_updateRow('class_group', $values['a'], array('class_id' => $values['a']['class_id']));//修改班级信息
                    $sign_class = $this->Sys_Model->table_seleRow('members_id', "schedule", array('class_id' => $values['a']['class_id']), $like = array());//查出所有会员id（并没有去重）
                    $items_class = [];
                    array_unique($sign_class,SORT_REGULAR);
                    if($sign_class){
                        foreach ($sign_class as $ite) {
                            $ite['sign_class'] = "0";
                            array_push($items_class, $ite);
                        }
                        $this->Sys_Model->table_updateBatchRow("sign_up", $items_class, "members_id");
                    }
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
                            $re['course_id'] = $values['a']['course_id'];
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
                        if($item['members_id']==$values['c'][0]['members_id']){
                            continue;
                        }
                        $sql2 = $sql2 . "," . $item['members_id'];
                    }
                    $sql = "update sign_up set sign_class = '1' where sign_competition_id= '" . $values['a']['course_id'] . "' and members_id in (" . $sql2 . ")";
                    $this->Sys_Model->execute_sql($sql,2);
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
            if ($values['a']['ID'] == "false") {
                $returnInfo = true;
                $this->db->trans_begin();
                $values['a'] = bykey_reitem($values['a'], 'ID');
                $this->Sys_Model->table_updateRow('class_group', $values['a'], array('class_id' => $values['a']['class_id']));//修改班级信息
                $sign_class = $this->Sys_Model->table_seleRow('members_id', "schedule", array('class_id' => $values['a']['class_id']), $like = array());//查出所有会员id（并没有去重）
                $items_class = [];
                array_unique($sign_class,SORT_REGULAR);
                if($sign_class){
                    foreach ($sign_class as $ite) {
                        $ite['sign_class'] = "0";
                        array_push($items_class, $ite);
                    }
                    $this->Sys_Model->table_updateBatchRow("sign_up", $items_class, "members_id");
                }
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
                        $re['course_id'] = $values['a']['course_id'];
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
                    if($item['members_id']==$values['c'][0]['members_id']){
                        continue;
                    }
                    $sql2 = $sql2 . "," . $item['members_id'];
                }
                $sql = "update sign_up set sign_class = '1' where sign_competition_id='" . $values['a']['course_id'] . "' and members_id in (" . $sql2 . ")";
                $this->Sys_Model->execute_sql($sql,2);
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







