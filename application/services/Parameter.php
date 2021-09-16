<?php


/**
 * Class Post ’参数类
 */
class Parameter extends HTY_service
{
	/**
	 * Dept constructor.
	 */
	public function __construct()
	{
		$this->load->model('Sys_Model');
		$this->load->helper('tool');

	}


	/**
	 * Notes:新增参数数据
	 * User: junxiong
	 * DateTime: 2020/1/5 10:04
	 * @param array $indData 参数信息
	 * @param $by /添加人员
	 * @return mixed
	 */
	public function addData($indData = [], $by)
	{
		$indData['ParameterId'] = uniqid("HTY", 4);//生成唯一部门ID
		$indData['CREATED_BY'] = $by;
		$indData['CREATED_TIME'] = date('Y-m-d H:i');
		$parametername=$this->Sys_Model->table_seleRow('ParameterId',"base_parameter",array('ParameterTitle'=>$indData['ParameterTitle'],'ParameterKey'=>$indData['ParameterKey']), $like=array());
		if (count($parametername)>0){
			$result = [];
			return $result;
		}else{
			$result = $this->Sys_Model->table_addRow("base_parameter", $indData, 1);
			return $result;
		}
	}

	/**
	 * Notes: 获取参数信息或者刷新
	 * User: junxiong
	 * DateTime: 2020/1/5 10:04
	 * @param array $searchWhere ‘查询条件
	 * @return array|mixed
	 */
	public function getParameter($searchWhere = [])
	{
		$where="where 1=1 ";
		$like="";
		$curr=$searchWhere['pages'];
		$limit=$searchWhere['rows'];
		if($searchWhere['ParameterTitle']!="")
		{
			$like=" and ParameterTitle like '%{$searchWhere['ParameterTitle']}%'";
		}
		if($searchWhere['ParameterKey']!="")
		{
			$where=$where." and ParameterKey='{$searchWhere['ParameterKey']}'";
		}
		if($searchWhere['begin'] != '' and $searchWhere['end'] != ''){

			$where=$where." and CREATED_TIME between '".$searchWhere['begin']."' and '".$searchWhere['end']."'";
		}
		$items=$this->get_searchdata($curr,$limit,$where,$like);
		return $items;
	}
	/**
	 * Notes: 删除参数数据
	 * User: ljx
	 * DateTime: 2020/12/31 16：50
	 * @param array $ParameterId  参数ID
	 * @return mixed
	 */
	public function delParameter($ParameterId = [])
	{
		if(count($ParameterId)>=1){
			$disParameterId = explode(',',$ParameterId['ParameterId']);
			$uname="'".$disParameterId[0]."'";
			for($i=1;$i<count($disParameterId);$i++){
				$uname=$uname.",'".$disParameterId[$i]."'";
			}
			$the_uname ="ParameterId in(".$uname.")";
			$del_sql = "delete from base_parameter where ".$the_uname;
		}
		else{
			$restulNum=[];
			return $restulNum;
		}
		$restulNum = $this->Sys_Model->execute_sql($del_sql, 2);
		return $restulNum;
	}

	/**
	 * * Notes: 修改部门数据
	 * User: junxiong
	 * DateTime: 2020/1/5 10:10
	 * @param array $values
	 * @return mixed
	 */
	public function modifyParameter($values,$by)
	{
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
		$parametername=$this->Sys_Model->table_seleRow('ParameterId',"base_parameter",array('ParameterTitle'=>$values['ParameterTitle'],'ParameterKey'=>$values['ParameterKey']), $like=array());
		if ($parametername ){
			$restulNum = [];
			if($parametername[0]['ParameterId']==$values['ParameterId']){
					$restulNum = $this->Sys_Model->table_updateRow('base_parameter', $values, array('ParameterId' => $values['ParameterId']));
					return $restulNum;

			}else{
				return $restulNum;
			}
		}else{
			$restulNum = $this->Sys_Model->table_updateRow('base_parameter', $values, array('ParameterId' => $values['ParameterId']));
			return $restulNum;
		}

	}
	/**
	 * * * Notes: 参数键名下拉
	 * User: junxiong
	 * DateTime: 2020/1/5 10：00
	 * @return mixed
	 */
	public function showKey()
	{
		$deptArr = $this->Sys_Model->table_seleRow('ParameterKey', "base_parameter", array(), $like = array());
		return $deptArr;
	}
    /**
     * * * Notes: 标题和值
     * User: junxiong
     * DateTime: 2021
     * @return mixed
     */
    public function showTitle($ParameterKey)
    {
        $deptArr = $this->Sys_Model->table_seleRow('ParameterTitle,Parametervalue', "base_parameter",$ParameterKey, $like = array());
        return $deptArr;
    }

//搜索参数页面 分页
	public function get_searchdata($pages,$rows,$wheredata,$likedata){

		$offset=($pages-1)*$rows;//计算偏移量
		$field='SQL_CALC_FOUND_ROWS ParameterId,ParameterTitle,ParameterKey,Parametervalue,ParameterRem,CREATED_TIME';
		$sql_query="Select ".$field." from base_parameter  ";
		if($wheredata!=""){
			$sql_query=$sql_query.$wheredata;
		}
		if($likedata!=""){
			$sql_query=$sql_query." ".$likedata;
		}
		$sql_query_total=$sql_query;
		$sql_query=$sql_query."  order by CREATED_TIME desc limit ".$offset.",".$rows;
		$query = $this->db->query($sql_query);
		$ss=$this->db->last_query();
		$r_total=$this->db->query($sql_query_total)->result_array();
		$row_arr=$query->result_array();
		$result['total']=count($r_total);//获取总行数
		$result["data"] = $row_arr;
		return $result;
	}


}







