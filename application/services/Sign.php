<?php


/**
 * Class Post ’报名
 */
class Sign extends HTY_service
{
	/**
	 * Dept constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sys_Model');
		$this->load->helper('tool');

	}

	/**
	 * Notes: 获取报名人员信息
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param array $searchWhere ‘查询条件
	 * @return array|mixed
	 */
	public function getSign($searchWhere = [])
	{
        if($searchWhere['DataScope']) {
            $where = "";


            if (count($searchWhere) > 0) {
                if ($searchWhere['sign_competition_id'] != '') {//课程ID  下拉
                    $where = $where . " and sign_competition_id in('{$searchWhere['sign_competition_id']}')";
                }
                $pages = $searchWhere['pages'];
                $rows = $searchWhere['rows'];
                $deptTmpArr=$this->get_Signdata($pages, $rows,$where);
            }
        }
        return $deptTmpArr;

	}


//搜索报名信息页面 分页
public function get_Signdata($pages,$rows,$wheredata){
    //Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
    $offset=($pages-1)*$rows;//计算偏移量
    $sql_query="Select sign_up.*,course.*	  from sign_up,course where course.course_id=sign_up.sign_competition_id   ";
    $sql_query_where=$sql_query.$wheredata;
    if($wheredata!="")
    {
        $sql_query=$sql_query_where;
    }
    $sql_query_total=$sql_query;
    $sql_query=$sql_query." order by sign_created_time desc limit ".$offset.",".$rows;

    $query = $this->db->query($sql_query);
    $ss=$this->db->last_query();
    $r_total=$this->db->query($sql_query_total)->result_array();
    $row_arr=$query->result_array();
    $result['total']=count($r_total);//获取总行数
    $result["data"] = $row_arr;
    $result["alldata"] = $r_total;
    return $result;
}

public function modifysign($values,$by)
{
    $values['sign_updated_by'] = $by;
    $values['sign_updated_time'] = date('Y-m-d H:i');
    $resluts=$this->Sys_Model->table_updateRow('sign_up', $values, array('sign_id' => $values['sign_id']));
    return $resluts;

}

//导入excel
    public function intoexcel(){
        $DbInsResult=array();//最终插入数据库结构
        $dist=["报名状态"=>"sign_statue","姓名"=>"sign_name","年龄"=>"sign_age","性别"=>"sign_sex",
            "民族"=>"sign_national","出生日期"=>"sign_birthday","身份证号"=>"sign_card_num","联系方式	"=>"sign_phone",
            "身高"=>"sign_height","体重"=>"sign_weight","鞋码"=>"sign_shoes_size","监护人姓名"=>"sign_guardian_name","监护人联系方式"=>"sign_guardian_phone",
            "居住地"=>"sign_live","自我介绍（50字内）"=>"sign_introduce","2寸红底证件照2"=>"sign_picture","照片1"=>"sign_image",
            "照片2"=>"sign_image","照片3"=>"sign_image"];//匹配字典
        $file = $_FILES['file']; // 获取上传的文件
        $temp = explode(".", $file["name"]);
        $extension = end($temp);
        $file_name=date('Ymdhis').rand(111,999);//文件名去中文
        $file_path = ".\\uploads\\" .$file_name . "." . $extension;
        $file_tmp = $file['tmp_name'];
        $move_result = move_uploaded_file($file_tmp, $file_path);
        $pinyin=[];
        if ($move_result) {

            $excel_inData = batch_import_excel($file_path);
            $excel_num = count($excel_inData);//记录Excel中记录总数
            foreach ($excel_inData as $row) {
                if($row[0]=="汉服少儿模特大赛报名表"){
                    continue;
                }
                if($row[0]=="姓名"){
                    array_push($pinyin,$dist[$row[0]]);
                    array_push($pinyin,$dist[$row[1]]);
                    array_push($pinyin,$dist[$row[2]]);
                    array_push($pinyin,$dist[$row[3]]);
                    array_push($pinyin,$dist[$row[4]]);
                    array_push($pinyin,$dist[$row[5]]);
                    array_push($pinyin,$dist[$row[6]]);
                    array_push($pinyin,$dist[$row[7]]);
                    array_push($pinyin,$dist[$row[8]]);
                    array_push($pinyin,$dist[$row[9]]);
                    array_push($pinyin,$dist[$row[10]]);
                    array_push($pinyin,$dist[$row[11]]);
                    array_push($pinyin,$dist[$row[12]]);
                    array_push($pinyin,$dist[$row[13]]);
                    array_push($pinyin,$dist[$row[14]]);
                    array_push($pinyin,$dist[$row[15]]);
                    array_push($pinyin,$dist[$row[16]]);
                    array_push($pinyin,$dist[$row[17]]);
                    array_push($pinyin,$dist[$row[18]]);
                    continue;
                }
                $temp_row = [];
                //按照excel列顺序赋值字段名称
                $temp_row[$pinyin[0]] = $row[0];
                $temp_row[$pinyin[1]] = $row[1];
                $temp_row[$pinyin[2]] = $row[2];
                $temp_row[$pinyin[3]] = $row[3];
                $temp_row[$pinyin[4]] = $row[4];
                $temp_row[$pinyin[5]] = $row[5];
                $temp_row[$pinyin[6]] = $row[6];
                $temp_row[$pinyin[7]] = $row[7];
                $temp_row[$pinyin[8]] = $row[8];
                $temp_row[$pinyin[9]] = $row[9];
                $temp_row[$pinyin[10]] = $row[10];
                $temp_row[$pinyin[11]] = $row[11];
                $temp_row[$pinyin[12]] = $row[12];
                $temp_row[$pinyin[13]] = $row[13];
                $temp_row[$pinyin[14]] = $row[14];
                $temp_row[$pinyin[15]] = $row[15];
                $temp_row[$pinyin[16]] = $row[16];
                $temp_row[$pinyin[17]] = $row[17].",".$row[18].",".$row[19];//照片
                array_push($DbInsResult, $temp_row);
            }
        }
	}
}







