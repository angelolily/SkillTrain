<?php


/**
 * Class Post ’岗位类
 */
class Course extends HTY_service
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
//新增课程
	public function addData($indData = [], $by)
	{
		$indData['course_created_by'] = $by;
		$indData['course_created_time'] = date('Y-m-d H:i');
        $indData['course_status'] = "报名中";
        $indData['course_id'] =create_guid();
		$postname=$this->Sys_Model->table_seleRow('course_id',"course",array('course_name'=>$indData['course_name']), $like=array());
		if ($postname){
			$results = [];
		    return $results;
	}else{
            $result=$this->Sys_Model->table_addRow("course", $indData, 1);

            return $result;
		}
	}
    //图片详情上传
    public function imageuploaddetail()
    {
        $resultvalue = array();

        $dir = './public/coursegraphic';
        $pptfiles=[];
        if (is_dir($dir) or mkdir($dir)) {
            $files=$_FILES;
            foreach ($files as $file)
            {
                $filename=time().rand(19,99). '.jpg';
                $file_tmp = $file['tmp_name'];
                $savePath=$dir."/".$filename;
                $move_result = move_uploaded_file($file_tmp, $savePath);//上传文件
                if ($move_result) {//上传成功
                    array_push($pptfiles,$filename);
                } else {
                    //上传失败
                    $resultvalue=[];
                    return $resultvalue;
                }
            }
            $pptfiles=join(',',$pptfiles);
            $resultvalue['course_graphic']=$pptfiles;
            return $resultvalue;
        }
    }
    //获取图片详情
    public function getimagedetail($pic){
        $resultvalue=array();
        $dir_original='./public/coursegraphic';
        $handler = opendir($dir_original);
        if($handler){
            $dir_original=str_replace('.','',$dir_original);
            $arrdirfiles=array();
            $dirfilename = "http://124.70.77.122/SkillTrain" . $dir_original .'/'. $pic['course_graphic'] ;
            //5、关闭目录
            closedir($handler);
            $resultvalue['name']=$pic['course_graphic'];
            $resultvalue['url']=$dirfilename;
            $resultvalue['raw']['type']="image/jpg";
            return $resultvalue;
        }
    }
    //图片封面上传
    public function imageuploadcover()
    {
        $resultvalue = array();

        $dir = './public/coursecover';
        $pptfiles=[];
        if (is_dir($dir) or mkdir($dir)) {
            $files=$_FILES;

            foreach ($files as $file)
            {
                $filename=time().rand(19,99). '.jpg';
                $file_tmp = $file['tmp_name'];
                $savePath=$dir."/".$filename;
                $move_result = move_uploaded_file($file_tmp, $savePath);//上传文件
                if ($move_result) {//上传成功
                    array_push($pptfiles,$filename);
                } else {
                    //上传失败
                    $resultvalue=[];
                    return $resultvalue;
                }
            }
            $pptfiles=join(',',$pptfiles);
            $resultvalue['course_cover']=$pptfiles;
            return $resultvalue;
        }
    }
    //获取图片封面
    public function getimagecover($pic){
        $resultvalue=array();
        $dir_original='./public/coursecover';
        //2、循环的读取目录下的所有文件
        //其中$filename = readdir($handler)是每次循环的时候将读取的文件名赋值给$filename，为了不陷于死循环，所以还要让$filename !== false。一定要用!==，因为如果某个文件名如果叫’0′，或者某些被系统认为是代表false，用!=就会停止循环*/
        $handler = opendir($dir_original);
        if($handler){
            $dir_original=str_replace('.','',$dir_original);
            $dirfilename = "http://124.70.77.122/SkillTrain" . $dir_original .'/'. $pic['course_cover'];
            //5、关闭目录
            closedir($handler);
            $resultvalue['name']=$pic['course_cover'];
            $resultvalue['url']=$dirfilename;
            $resultvalue['raw']['type']="image/jpg";
            return $resultvalue;
        }
    }
//获取
    public function getcourse($searchWhere = []) //查询到课程表
    {
            $where = "";
            if (count($searchWhere) > 0) {
                if ($searchWhere['course_id'] != '') {//课程表id  下拉
                    $where = $where . " and course_id in('{$searchWhere['course_id']}')";
                }
                $pages = $searchWhere['pages'];
                $rows = $searchWhere['rows'];
                $deptTmpArr=$this->get_coursedata($pages, $rows,$where);
            }
        return $deptTmpArr;
    }

//搜索活动信息页面 分页
    public function get_coursedata($pages,$rows,$wheredata){
        //Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
        $offset=($pages-1)*$rows;//计算偏移量
        $sql_query="Select * from course  where  1=1  ";
        $sql_query_where=$sql_query.$wheredata;
        if($wheredata!="")
        {
            $sql_query=$sql_query_where;
        }
        $sql_query_total=$sql_query;
        $sql_query=$sql_query." order by course_created_time desc limit ".$offset.",".$rows;
        $query = $this->db->query($sql_query);
        $ss=$this->db->last_query();
        $r_total=$this->db->query($sql_query_total)->result_array();
        $row_arr=$query->result_array();
        $result['total']=count($r_total);//获取总行数
        $result["data"] = $row_arr;
        $result["alldata"] = $r_total;
        return $result;
    }
//删除
	public function delcourse($postId = [])
	{
        $postname=$this->Sys_Model->table_seleRow('members_id',"sign_up",array('sign_competition_id'=>$postId['course_id'],"sign_statue"=>"成功报名"), $like=array());
        if($postname){
            $result=[];
            return $result;
        }else{
            $result=$this->Sys_Model->table_del("course",$postId);
            return $result;
        }
	}
////发布
//    public function publishaa($postId = [])
//    {
//        $where['course_signQRcode']=getCode("pages/sign/sign","course_id",$postId['course_id']);
//        $where['course_status']="已发布";
//        $result=$this->Sys_Model->table_updateRow('course', $where, array('course_id' => $postId['course_id']));
//        return $result;
//    }
//修改
	public function modifycourse($values,$by)
    {
        $values['course_updated_by'] = $by;
        $values['course_updated_time'] = date('Y-m-d H:i');
        $postname = $this->Sys_Model->table_seleRow('course_id', "course", array('course_name' => $values['course_name']), $like = array());
        $resluts=[];
        if ($postname) {
            if ($postname[0]['course_id'] == $values['course_id']) {
                $resluts=$this->Sys_Model->table_updateRow('course', $values, array('course_id' => $values['course_id']));
            }
            return $resluts;
        }
        $resluts=$this->Sys_Model->table_updateRow('course', $values, array('course_id' => $values['course_id']));
        return $resluts;
    }
//下拉
    public function showcourse()
    {
        $reslut = $this->Sys_Model->table_seleRow('course_id,course_name', "course", $where=array(), $like = array());
        return $reslut;
    }

//结束
    public function finallycourse($postId = [])
    {
        $where['course_status']="已结束";
        $result=$this->Sys_Model->table_updateRow('course', $where, array('course_id' => $postId['course_id']));
        return $result;
    }

////下架
//    public function finallycommodity($postId = [])
//    {
//        $where['course_status']="未发布";
//        $result=$this->Sys_Model->table_updateRow('course', $where, array('course_id' => $postId['course_id']));
//        return $result;
//    }

}







