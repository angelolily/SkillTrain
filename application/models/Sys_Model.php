<?php


/**
 * Class Sys_Model
 */
class Sys_Model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	//插入记录
	public function table_addRow($taname,$values,$type=1){

		if($type==1)
		{
			$this->db->insert($taname,$values);
		}
		else
		{
			$this->db->insert_batch($taname,$values);
		}
		$result = $this->db->affected_rows();
		$this->db->cache_delete_all();
		return $result;
	}

	//查询记录
	public function table_seleRow($field,$taname,$wheredata=array(),$likedata=array(),$wherein=array(),$whereinfield=""){

		$this->db->select($field);
		if(count($wheredata)>0){
			$this->db->where($wheredata);//判断需不需where要查询
		}
		if(count($likedata)>0){
			$this->db->like($likedata);//判断需不需要like查询
		}
		if(count($wherein)>0){
			$this->db->where_in($whereinfield,$wherein);//判断需不需要ow where in
		}
		$query = $this->db->get($taname);

		$ss=$this->db->last_query();

		$rows_arr=$query->result_array();

		return $rows_arr;

	}

	//修改记录
	public function table_updateRow($taname,$values,$wheredata){

		$this->db->where($wheredata);
		$this->db->update($taname,$values);
		$result = $this->db->affected_rows();
		$this->db->cache_delete_all();
		return $result;

	}

	//删除记录
	public function table_del($taname,$wheredata){

		$this->db->where($wheredata);
		$this->db->delete($taname);
		$result = $this->db->affected_rows();
		$this->db->cache_delete_all();

		return $result;
	}

	//事物处理
	public function table_trans($sql_array)
	{

		if(count($sql_array)>0)
		{

			try {
				$this->db->trans_begin();
				foreach ($sql_array as $sql)
				{
					$this->db->query($sql);
				}
				if (($this->db->trans_status() === FALSE))
				{
					$this->db->trans_rollback();
					return false;
				}
				else {
					$this->db->trans_commit();
					$this->db->cache_delete_all();
					return true;
				}
			}
			catch (Exception $ex)
			{
				$this->db->trans_rollback();
				return false;
			}

		}





	}


	/**
	 * @param $sql 要执行的sql语句
	 * @param $type 判断是不是DML语句，默认1：不是，2：是
	 * @return array
	 */
	public function execute_sql($sql, $type=1)
	{

		$query = $this->db->query($sql);
		if($query){
			if($type==1)
			{
				return $query->result_array();
			}
			else{
				return $this->db->affected_rows();
			}

		}
		$ss=$this->db->last_query();
		return array();

	}

	//批量修改记录
	public function table_updateBatchRow($taname, $values, $wherekey)
	{

		$result = $this->db->update_batch($taname, $values, $wherekey);
		$ss = $this->db->last_query();
		$this->db->cache_delete_all();
		return $result;

	}


	/**
	 * @param $field
	 * @param $taname
	 * @param array $wheredata
	 * @param array $likedata
	 * @param int $beging
	 * @param int $offset
	 * @param null $order
	 * @param null $order_type
	 * @return mixed
	 */
	public function table_seleRow_limit($field, $taname, $wheredata=array(), $likedata=array(), $begin=10, $offset=0, $order=null, $order_type=null,$wherein=array(),$whereinfield="")
	{
		$this->db->select($field);
		if(count($wheredata)>0){
			$this->db->where($wheredata);//判断需不需where要查询
		}
		if(count($likedata)>0){
			$this->db->like($likedata);//判断需不需要like查询
		}
		if(count($wherein)>0){
			$this->db->where_in($whereinfield,$wherein);//判断需不需要ow where in
		}
		$this->db->limit($begin,$offset);
		if(!(is_null($order))){
			$this->db->order_by($order,$order_type);
		}
		$query = $this->db->get($taname);
		$ss=$this->db->last_query();
		$rows_arr=$query->result_array();
		return $rows_arr;
	}

    //查询记录，带join
    public function table_joinSeleRow_limit($field, $taname, $wheredata=array(), $likedata=array(), $begin=10, $offset=0, $order=null, $order_type=null,$wherein=array(),$whereinfield="",$jointype="left",$jointable="",$joinwhere="")
    {
        $this->db->select($field);
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需where要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需要like查询
        }
        if(count($wherein)>0){
            $this->db->where_in($whereinfield,$wherein);//判断需不需要ow where in
        }
        $this->db->limit($begin,$offset);
        if(!(is_null($order))){
            $this->db->order_by($order,$order_type);
        }
        $this->db->join($jointable, $joinwhere,$jointype);
        $query = $this->db->get($taname);
        $ss=$this->db->last_query();
        $rows_arr=$query->result_array();
        return $rows_arr;
    }

    public function table_joinSeleRow($field, $taname, $wheredata=array(), $likedata=array(), $order=null, $order_type=null,$wherein=array(),$whereinfield="",$jointype="left",$jointable="",$joinwhere="")
    {
        $this->db->select($field);
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需where要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需要like查询
        }
        if(count($wherein)>0){
            $this->db->where_in($whereinfield,$wherein);//判断需不需要ow where in
        }
//        $this->db->limit($begin,$offset);
        if(!(is_null($order))){
            $this->db->order_by($order,$order_type);
        }
        $this->db->join($jointable, $joinwhere,$jointype);
        $query = $this->db->get($taname);
        $ss=$this->db->last_query();
        $rows_arr=$query->result_array();
        return $rows_arr;
    }

    //$sum  要求和的字段名  $name变成的字段名  $taname表名
    public function table_sumRow($taname,$wheredata=array(),$sum="",$name=""){

        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需where要查询
        }
        if($sum!=""){
            $this->db->select_sum($sum,$name);
        }
        $query = $this->db->get($taname);
        $ss=$this->db->last_query();
        $rows_arr=$query->result_array();
        return $rows_arr;
    }
//搜索参数页面 分页
//	public function get_searchdata($pages,$rows,$wheredata,$likedata){
//
//		$offset=($pages-1)*$rows;//计算偏移量
//		$field='SQL_CALC_FOUND_ROWS ParameterId,ParameterTitle,ParameterKey,Parametervalue,ParameterRem,CREATED_TIME';
//		$sql_query="Select ".$field." from base_parameter ";
//		if($wheredata!=""){
//			$sql_query=$sql_query.$wheredata;
//		}
//		if($likedata!=""){
//			$sql_query=$sql_query." ".$likedata;
//		}
//		$sql_query_total=$sql_query;
//		$sql_query=$sql_query." limit ".$offset.",".$rows;
//		$query = $this->db->query($sql_query);
//		$ss=$this->db->last_query();
//		$r_total=$this->db->query($sql_query_total)->result_array();
//		$row_arr=$query->result_array();
//		$result['total']=count($r_total);//获取总行数
//		$result["data"] = $row_arr;
//		return $result;
//	}
	//搜索角色页面 分页
//	public function get_roledata($pages,$rows,$wheredata,$likedata){
//
//		$offset=($pages-1)*$rows;//计算偏移量
//		$field='SQL_CALC_FOUND_ROWS RoleId,Status,Name,DataScope,Remark,CREATED_TIME,showmenu,showdata';
//		$sql_query="Select ".$field." from base_role ";
//		if($wheredata!=""){
//			$sql_query=$sql_query.$wheredata;
//		}
//		if($likedata!=""){
//			$sql_query=$sql_query." ".$likedata;
//		}
//		$sql_query_total=$sql_query;
//		$sql_query=$sql_query." limit ".$offset.",".$rows;
//		$query = $this->db->query($sql_query);
//		$ss=$this->db->last_query();
//		$r_total=$this->db->query($sql_query_total)->result_array();
//		$row_arr=$query->result_array();
//		$result['total']=count($r_total);//获取总行数
//		$result["data"] = $row_arr;
//		return $result;
//	}
//	//搜索用户页面 分页
//	public function get_userdata($pages,$rows,$wheredata,$likedata){
//		//Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
//		$offset=($pages-1)*$rows;//计算偏移量
//		$field='SQL_CALC_FOUND_ROWS Userid,UserName,UserRole,base_dept.DeptName,base_user.Mobile,Birthday,UserStatus,UserEmail,Sex,UserDept,base_user.Remark,IsAdmin,UserPost,base_user.CREATED_TIME,base_post.PostName';
//		$sql_query="Select ".$field." from base_user,base_dept,base_post where base_user.UserDept = base_dept.DeptId AND base_user.UserPost=base_post.PostId ";
//		if($wheredata!=""){
//			$sql_query=$sql_query.$wheredata;
//		}
//		if($likedata!=""){
//			$sql_query=$sql_query." ".$likedata;
//		}
//		$sql_query_total=$sql_query;
//		$sql_query=$sql_query." limit ".$offset.",".$rows;
//		$query = $this->db->query($sql_query);
//		$ss=$this->db->last_query();
//		$r_total=$this->db->query($sql_query_total)->result_array();
//		$row_arr=$query->result_array();
//		$result['total']=count($r_total);//获取总行数
//		$result["data"] = $row_arr;
//		return $result;
//	}

//	/**
//	 * @param $field
//	 * @param $taname
//	 * @param array $wheredata
//	 * @param array $likedata
//	 * @return mixed 总的查询到的数
//	 */
//	public function table_seleRow_count($field, $taname, $wheredata=array(), $likedata=array()){
//
//		$this->db->select($field);
//		if(count($wheredata)>0){
//			$this->db->where($wheredata);//判断需不需where要查询
//		}
//		if(count($likedata)>0){
//			$this->db->like($likedata);//判断需不需要like查询
//		}
//		$query = $this->db->get($taname);
//
//		$ss=$this->db->last_query();
//
//		$rows_arr=$query->result_array();
//		$total=count($rows_arr);
//		return $total;
//
//	}

}
