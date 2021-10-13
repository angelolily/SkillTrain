<?php


/**
 * Class Post ’岗位类
 */
class Cert extends HTY_service
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
//新增证书
	public function addData($indData = [], $by)
	{
		$indData['create_by'] = $by;
		$indData['create_time'] = date('Y-m-d H:i');
		$postname=$this->Sys_Model->table_seleRow('cert_id',"cert_tb",array('cert_name'=>$indData['cert_name'],'members_phone'=>$indData['members_phone']), $like=array());
		if ($postname){
			$results = [];
		    return $results;
	}else{
            $result=$this->Sys_Model->table_addRow("cert_tb", $indData, 1);
            return $result;
		}
	}

//获取搜索证书
    public function getcert($searchWhere = []) //查询到课程表
    {
            $where = "";
            if (count($searchWhere) > 0) {
                if ($searchWhere['cert_name'] != '') {
                    $where = $where . " and cert_name like '%{$searchWhere['cert_name']}%')";
                }
                if ($searchWhere['members_name'] != '') {
                    $where = $where . " and members_name like '%{$searchWhere['members_name']}%')";
                }

                $pages = $searchWhere['pages'];
                $rows = $searchWhere['rows'];
                $deptTmpArr=$this->get_certdata($pages, $rows,$where);
            }
        return $deptTmpArr;
    }

//搜索活动信息页面 分页
    public function get_certdata($pages,$rows,$wheredata){
        //Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
        $offset=($pages-1)*$rows;//计算偏移量
        $sql_query="Select * from cert_tb  where  1=1  ";
        $sql_query_where=$sql_query.$wheredata;
        if($wheredata!="")
        {
            $sql_query=$sql_query_where;
        }
        $sql_query_total=$sql_query;
        $sql_query=$sql_query." order by members_name  limit ".$offset.",".$rows;
        $query = $this->db->query($sql_query);
        $ss=$this->db->last_query();
        $r_total=$this->db->query($sql_query_total)->result_array();
        $row_arr=$query->result_array();
        $result['total']=count($r_total);//获取总行数
        $result["data"] = $row_arr;
        $result["alldata"] = $r_total;
        return $result;
    }

//修改
	public function modifycert($values,$by)
    {
        $values['update_by'] = $by;
        $values['update_time'] = date('Y-m-d H:i');
        $postname = $this->Sys_Model->table_seleRow('cert_id', "cert_tb", array('cert_name' => $values['cert_name']), $like = array());
        $resluts=[];
        if ($postname) {
            if ($postname[0]['cert_id'] == $values['cert_id']) {
                $resluts=$this->Sys_Model->table_updateRow('cert_tb', $values, array('cert_id' => $values['cert_id']));
            }
            return $resluts;
        }
        $resluts=$this->Sys_Model->table_updateRow('cert_tb', $values, array('cert_id' => $values['cert_id']));
        return $resluts;
    }

    //删除
    public function delcert($values)
    {
        $restulNum = $this->Sys_Model->table_del("cert_tb", $values);
        return $restulNum;
    }


    public function manyimageupload()//多图片上传
    {
        $resultvalue = array();
        $dddir=time().rand(19,99);

        $dir = './public/'.$dddir;
        $pptfiles=[];
        if (is_dir($dir) or mkdir($dir)) {
            $files=$_FILES;
            foreach ($files as $file)
            {
                $filename=time(). '.jpg';
                sleep(1);
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
            $resultvalue['process_data']='/public/'.$dddir;
            return $resultvalue;
        }
    }


    public function readmaneypic($values)//读取目录下多图片
    {
        $res_url = [];
        $base_url='http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
        $dir_name = $values['process_data'];
        $dir = '.'.$dir_name;
        if(file_exists($dir)){
            $handler = opendir($dir);
            if ($handler) {
                while (($filename = readdir($handler)) !== false) {
                    if ($filename != "." && $filename != "..") {
                        array_push($res_url, $filename);
//                        array_push($res_url,$base_url . $dir_name . '/' . $filename);
                    }
                }
            }
            closedir($handler);
        }
        asort($res_url);//排序
        $res_urls=[];
        foreach ($res_url as $item){//加路径
          array_push($res_urls,$base_url . $dir_name . '/' . $item);
        }
        return $res_urls;
    }


    //选择人员下拉
    public function membersdata()
    {
        $result = $this->Sys_Model->table_seleRow('members_card,members_id,members_name,members_phone', "members", array(), $like = array());
        return $result;
    }


}







