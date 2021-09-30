<?php


/**
 * Class Post ’岗位类
 */
class Courseware extends HTY_service
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

//新增课件
    public function addData($indData = [], $by)
    {
        $indData['created_by'] = $by;
        $indData['created_time'] = date('Y-m-d H:i');
        $result = $this->Sys_Model->table_addRow("course_attach", $indData, 1);
        return $result;
    }

    //图片详情上传
    public function imageuploaddetail()
    {
        $resultvalue = array();

        $dir = './public/coursegraphic';
        $pptfiles = [];
        if (is_dir($dir) or mkdir($dir)) {
            $files = $_FILES;
            foreach ($files as $file) {
                $filename = time() . rand(19, 99) . '.jpg';
                $file_tmp = $file['tmp_name'];
                $savePath = $dir . "/" . $filename;
                $move_result = move_uploaded_file($file_tmp, $savePath);//上传文件
                if ($move_result) {//上传成功
                    array_push($pptfiles, $filename);
                } else {
                    //上传失败
                    $resultvalue = [];
                    return $resultvalue;
                }
            }
            $pptfiles = join(',', $pptfiles);
            $resultvalue['course_graphic'] = $pptfiles;
            return $resultvalue;
        }
    }

    //获取图片详情
    public function getimagedetail($pic)
    {
        $resultvalue = array();
        $dir_original = './public/coursegraphic';
        $handler = opendir($dir_original);
        if ($handler) {
            $dir_original = str_replace('.', '', $dir_original);
            $arrdirfiles = array();
            $dirfilename = "http://192.168.2.10/SkillTrain" . $dir_original . '/' . $pic['course_graphic'];
            //5、关闭目录
            closedir($handler);
            $resultvalue['name'] = $pic['course_graphic'];
            $resultvalue['url'] = $dirfilename;
            $resultvalue['raw']['type'] = "image/jpg";
            return $resultvalue;
        }
    }

//获取
    public function getcourseware($searchWhere = []) //查询到课程表
    {
        $where = "";
        if (count($searchWhere) > 0) {
            if ($searchWhere['course_id'] != '') {//课程表id  下拉
                $where = $where . " and course_id in('{$searchWhere['course_id']}')";
            }
            $pages = $searchWhere['pages'];
            $rows = $searchWhere['rows'];
            $deptTmpArr = $this->get_coursewaredata($pages, $rows, $where);
        }
        return $deptTmpArr;
    }

//搜索活动信息页面 分页
    public function get_coursewaredata($pages, $rows, $wheredata)
    {
        //Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
        $offset = ($pages - 1) * $rows;//计算偏移量
        $sql_query = "Select * from course_attach  where  1=1  ";
        $sql_query_where = $sql_query . $wheredata;
        if ($wheredata != "") {
            $sql_query = $sql_query_where;
        }
        $sql_query_total = $sql_query;
        $sql_query = $sql_query . " order by created_time desc limit " . $offset . "," . $rows;
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
    public function delcourseware($postId = [])
    {
        $result = $this->Sys_Model->table_del("course_attach", array('course_id' => $postId['course_id'],'course_num' => $postId['course_num']));
        return $result;
    }

//修改
    public function modifycourseware($values, $by)
    {
        $values['created_by'] = $by;
        $values['created_time'] = date('Y-m-d H:i');
        $resluts = $this->Sys_Model->table_updateRow('course_attach', $values, array('course_id' => $values['course_id'],'course_num' => $values['course_num']));
        return $resluts;
    }

}







