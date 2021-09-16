<?php


/**
 * Class Post ’参数类
 */
class Personal extends HTY_service
{
	/**
	 * Dept constructor.
	 */
	public function __construct()
	{
		$this->load->model('Sys_Model');
		$this->load->helper('tool');
		$this->load->library('encryption');


	}


		//搜索用户页面 分页
	public function get_owndata($wheredata){
		$field='SQL_CALC_FOUND_ROWS Userid,UserName,UserRole,base_dept.DeptName,base_user.Mobile,Birthday,UserStatus,UserEmail,Sex,UserDept,base_user.Remark,IsAdmin,UserPost,base_user.CREATED_TIME,base_post.PostName,base_dept.DeptName,Avatar';
		$sql_query="Select ".$field." from base_user,base_dept,base_post where base_user.UserDept = base_dept.DeptId AND base_user.UserPost=base_post.PostId";
		if($wheredata!=""){
			$sql_query=$sql_query.$wheredata;
		}
		$query = $this->db->query($sql_query);
		$ss=$this->db->last_query();
		$row_arr=$query->result_array();
		$result["data"] = $row_arr;
		return $result;
	}
	/**
	 * Notes: 获取用户个人信息
	 * User: junxiong
	 * DateTime: 2020/1/7 16:31
	 * @param array $searchWhere ‘查询条件
	 * @return array|mixed
	 */
	public function getPersonal($searchWhere = [],$by)
	{
		$wheredata="  and base_user.Mobile='{$by}'";
		$restulNum = $this->get_owndata($wheredata);
		$roleid=explode(',',$restulNum['data'][0]['UserRole']);
		$role="'".$roleid[0]."'";
		for($i=1;$i<count($roleid);$i++) {
			$role = $role . ",'" . $roleid[$i] . "'";
		}
		$sql="select Name from base_role where RoleId in ( {$role} ) ";
		$a=$this->Sys_Model->execute_sql($sql, 1);
		$a = array_column($a, 'Name');
		$str=implode(',',$a);
		$item['Name']=$str;
		$restulNum['data'][0]['Name']=$item['Name'];
		return $restulNum;
	}


	/**
	 * * Notes: 修改密码
	 * User: junxiong
	 * DateTime: 2020/1/7 16:30
	 * @param array $values
	 * @return mixed
	 */
	public function modifyPersonal($values,$by)
	{
		$restul= $this->Sys_Model->table_seleRow("UserPassword","base_user",$wheredata=array('Mobile'=>$by),$likedata=array());
		$enrestul=$this->encryption->decrypt($restul[0]['UserPassword']);//解密
		if($values['UserPassword']==$enrestul){
			$values['UserPassword']=$this->encryption->encrypt($values['newPassword']);//加密
			$restulover=$this->Sys_Model->table_updateRow("base_user",array('UserPassword'=>$values['UserPassword']),$wheredata=array('Mobile'=>$values['Mobile']));
			return $restulover;
		}else
		{
			$restulover=[];
			return $restulover;
		}
		$restulover=[];
		return $restulover;

	}

	public function headPortrait($base64_image_content,$by)
	{
		//匹配出文件的格式
		if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content['image'], $result)) {
			$type = $result[2];
			$new_folder = ".\public";
			$name = date('Ymdhis') . rand(111, 999);
			if (is_dir($new_folder) or mkdir($new_folder)) {
				$new_file = "https://hftx.fzz.cn/public/". $name . ".{$type}";//上线后改路径
				$new_folder=$new_folder. "\\".$name . ".{$type}";
				if (file_put_contents($new_folder, base64_decode(str_replace($result[1], '', $base64_image_content['image'])))) {
					$this->Sys_Model->table_updateRow("base_user",array('Avatar'=>$new_file),array('Mobile'=>$by));
					return $new_file;
				} else {
					return "";
				}
			}
		}
		else {
			return "";
		}
	}
}







