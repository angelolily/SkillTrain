<?php


/**
 * Class Menu
 */
class Menu extends HTY_service
{
	private $menuid=[];

	/**
	 * Menu constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sys_Model');
		$this->load->helper('tool');

	}

	/**
	 * Notes:获取菜单层级树
	 * User: angelo
	 * DateTime: 2021/1/11 16:41
	 * @param $pid  父ID
	 * @param $arr  递归数组
	 * @param array $tree
	 * @return array|mixed
	 */
	public function getMenuTree($pid , $arr, &$tree = [])
	{

		foreach ( $arr as $key => $dp ){
			if( $dp['parentid'] == $pid ){
				$c= $this->getMenuTree( $dp['menuid'] ,$arr );
				if( $c ){

					$dp['children'] = $c;
				}
				if(count($this->menuid)>0){
					if(in_array($dp['menuid'],$this->menuid))
					{
						$tree[] = $dp;
					}
				} else {
					$tree[] = $dp;
				}
			}
		}
		return $tree;

	}


	/**
	 * Notes:添加菜单
	 * User: angelo
	 * DateTime: 2021/1/4 15:02
	 * @param $indData 添加数据
	 * @param $by  添加人
	 * @return mixed
	 */
	public function addMenu($indData, $by){

		if($indData['parentid']=='')
		{
			$indData['parentid']=0;
		}
		$indData['CREATED_BY']=$by;
		$indData['CREATED_TIME']=date('Y-m-d H:i');
		$result=$this->Sys_Model->table_addRow("base_menu",$indData,1);
		return $result;

	}

	/**
	 * Notes:单个修改菜单信息
	 * User: angelo
	 * DateTime: 2021/1/4 15:06
	 * @param $MenuId 修改菜单ID
	 * @param $indData 修改数据
	 * @param $by 修改人
	 * @return mixed
	 */
	public function updateMenu($MenuId, $indData, $by)
	{
		if($indData['parentid']=='')
		{
			$indData['parentid']=0;
		}
		$indData['UPDATED_BY']=$by;
		$indData['UPDATED_TIME']=date('Y-m-d H:i');
		$result=$this->Sys_Model->table_updateRow("base_menu",$indData,array('menuid'=>$MenuId));
		return $result;
	}

	/**
	 * Notes:删除菜单项目
	 * User: angelo
	 * DateTime: 2021/1/4 15:04
	 * @param $MenuId 删除菜单ID
	 * @return mixed
	 */
	public function delMenu($MenuId)
	{
		$restulNum=0;
		//先将所以一级子节点父级ID变成顶级ID
		$upArr=array('parentid'=>0);
		$upNum=$this->Sys_Model->table_updateRow('base_menu',$upArr,array('parentid'=>$MenuId));
		if($upNum>=0)
		{
			$restulNum=$this->Sys_Model->table_del('base_menu',array('menuid'=>$MenuId));
		}


		return $restulNum;

	}

	/**
	 * Notes:获取菜单数据
	 * User: angelo
	 * DateTime: 2021/1/4 15:15
	 */
	public function getMenu($menuid){

		if($menuid!=""){
			$this->menuid=explode(",",$menuid);
		}
 		$resultMenu=[];
		$menuArr=$this->Sys_Model->table_seleRow('component,title,menuid,menuname,parentid,icon,path,remark,role',"base_menu",array(),array());
		if(count($menuArr)>0)
		{
			$resultMenu=$this->getMenuTree("0",$menuArr);


		}

		return $resultMenu;


	}


	/**
	 * Notes: 批量更新菜单层级
	 * User: angelo
	 * DateTime: 2021/1/11 16:41
	 * @param $indData
	 * @return mixed
	 */
	public function changeLevel($indData)
	{
		$restulNum=0;

		$restulNum=$this->Sys_Model->table_updateBatchRow("base_menu",$indData,"menuid");


		return $restulNum;




	}





}
