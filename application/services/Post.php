<?php


/**
 * Class Post ’岗位类
 */
class Post extends HTY_service
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
	 * Notes:新增岗位数据
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param array $indData 岗位信息
	 * @param $by /添加人员
	 * @return mixed
	 */
	public function addData($indData = [], $by)
	{

		$indData['PostId'] = uniqid("HTY", 4);//生成唯一部门ID
		$indData['CREATED_BY'] = $by;
		$indData['CREATED_TIME'] = date('Y-m-d H:i');
		$postname=$this->Sys_Model->table_seleRow('PostId',"base_post",array('PostName'=>$indData['PostName']), $like=array());
		if ($postname){
			$result = [];
		return $result;
	}else{

		$result = $this->Sys_Model->table_addRow("base_post", $indData, 1);
		return $result;
		}


	}

	/**
	 * Notes: 获取岗位信息
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param array $searchWhere ‘查询条件
	 * @return array|mixed
	 */
	public function getPost($searchWhere = [])
	{
		$where = [];
		$like = [];
		$deptArr= [];
		if (count($searchWhere) > 0) {

			if($searchWhere['PostName'] != '')
			{
				$like['PostName'] = $searchWhere['PostName'];
			}
			if($searchWhere['Status'] != ''){
				$where['Status'] = $searchWhere['Status'];
			}
			$begin=$searchWhere['rows'];
			$offset=($searchWhere['pages']-1)*$searchWhere['rows'];
			$totalArr=$this->Sys_Model->table_seleRow('PostId',"base_post", $where, $like);
			if($totalArr && count($totalArr)>0)
			{
				$deptTmpArr = $this->Sys_Model->table_seleRow_limit('PostId,PostCode,PostName,PostSort,Status,Remark', "base_post", $where, $like,$begin,$offset,$order="PostSort");
				$deptArr['total']=count($totalArr);
				$deptArr['data']=$deptTmpArr;
			}
			else
			{
				$deptArr['total']=0;
				$deptArr['data']=[];

			}

		}
		return $deptArr;

	}


	/**
	 * Notes: 删除岗位数据
	 * User: ljx
	 * DateTime: 2020/12/31 16：50
	 * @param array $postId  岗位ID
	 * @return mixed
	 */
	public function delPost($postId = [])
	{
		if(count($postId)>=1){
			$postid = explode(',',$postId['PostId']);
			$uname="'".$postid[0]."'";
			for($i=1;$i<count($postid);$i++){
				$uname=$uname.",'".$postid[$i]."'";
			}
			$the_uname ="PostId in(".$uname.")";
			$del_sql = "delete from base_post where ".$the_uname;
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
	 * DateTime: 2020/12/25 17:00
	 * @param array $values
	 * @return mixed
	 */
	public function modifyPost($values,$by)
	{
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
		$postname=$this->Sys_Model->table_seleRow('PostId',"base_post",array('PostName'=>$values['PostName']), $like=array());

		if ($postname){
			$restulNum = [];
			if($postname[0]['PostId']==$values['PostId']){
				$restulNum = $this->Sys_Model->table_updateRow('base_post', $values, array('PostId' => $values['PostId']));
				return $restulNum;
			}
			return $restulNum;
		}else{

			$restulNum = $this->Sys_Model->table_updateRow('base_post', $values, array('PostId' => $values['PostId']));
			return $restulNum;
		}

	}
}







