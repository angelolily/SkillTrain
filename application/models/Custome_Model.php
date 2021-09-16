<?php


/**
 * Class Sys_Model
 */
class Custome_Model extends CI_Model
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



}
