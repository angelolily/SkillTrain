<?php


/**
 * Class Usermanage ’用户管理类
 */
class Order extends HTY_service
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



	//搜索订单页面 分页
	public function get_orderdata($pages,$rows,$wheredata,$likedata){
		//Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
		$offset=($pages-1)*$rows;//计算偏移量
		$field='Select * ';
		$sql_query=$field." from `order` where 1=1 ".$wheredata;
		if($likedata!=""){//like不为空
		    $sql_query=$sql_query." ".$likedata;
		}
		$sql_query_total=$sql_query;
		$sql_query=$sql_query." order by created_time desc limit ".$offset.",".$rows;
 		$query = $this->db->query($sql_query);
		$ss=$this->db->last_query();
		$r_total=$this->db->query($sql_query_total)->result_array();
		$row_arr=$query->result_array();
		$result['total']=count($r_total);//获取总行数
		$result["data"] = $row_arr;
		return $result;
	}

	/**
	 * Notes: 获取用户信息或者刷新
	 * User: junxiong
	 * DateTime: 2021/1/11 15:04
	 * @param array $searchWhere ‘查询条件
	 * @return array|mixed
	 */
	public function getOrder($searchWhere = [])
	{
		if($searchWhere['DataScope']) {
            $where = "";
            $like = "";
            $curr = $searchWhere['pages'];
            $limit = $searchWhere['rows'];
            if ($searchWhere['order_customer_name'] != "") {
                $like = " and order_customer_name like '%{$searchWhere['order_customer_name']}%'";
            }
            if ($searchWhere['order_capid'] != "") {
                $where = $where . " and  order_capid in('{$searchWhere['order_capid']}')";
            }
            if ($searchWhere['order_statue'] != "") {
                $where = $where . " and  order_statue in('{$searchWhere['order_statue']}')";
            }

            if ($searchWhere['DataScope']==1){
                if ($searchWhere['order_deptid'] != "") {
                    $where = $where . " and  order_deptid in('{$searchWhere['order_deptid']}')";
                }
            }
            if ($searchWhere['DataScope']==3 or $searchWhere['DataScope']==4){
                $all=explode(',',$searchWhere['powerdept']);
                $DeptId = "'" . $all[0] . "'";
                for ($i = 1; $i < count($all); $i++) {
                    $DeptId = $DeptId . ",'" . $all[$i] . "'";
                }
                $where = $where . " and ( order_deptid in({$DeptId})) ";
            }
            $items = $this->get_orderdata($curr, $limit, $where, $like);
        }
		return $items;

	}//获取订单

    /**
     * Notes: 获取用户信息或者刷新
     * User: junxiong
     * DateTime: 2021/1/11 15:04
     * @param array $searchWhere ‘查询条件
     * @return array|mixed
     */
    public function getallOrder($searchWhere = [])
    {
        if($searchWhere['DataScope']) {
            $where = "";
            $like = "";
            $curr = $searchWhere['pages'];
            $limit = $searchWhere['rows'];
            if ($searchWhere['order_id'] != "") {
                $like = " and order_id like '%{$searchWhere['order_id']}%'";
            }
            if ($searchWhere['order_product'] != "") {
                $like = " and order_product like '%{$searchWhere['order_product']}%'";
            }
            if ($searchWhere['order_statue'] != "") {
                $where = $where . " and  order_statue in('{$searchWhere['order_statue']}')";
            }
            if ($searchWhere['order_customer_phone'] != "") {
                $like = " and order_customer_phone like '%{$searchWhere['order_customer_phone']}%'";
            }
            if ($searchWhere['order_refund_flag'] != "") {
                $where = $where . " and  order_refund_flag in('{$searchWhere['order_refund_flag']}')";
            }
            $where = $where . " and  order_type in('商品')";
            $items = $this->get_orderdata($curr, $limit, $where, $like);
        }
        return $items;

    }//商品订单


    /**
     * Notes: 获取用户信息或者刷新
     * User: junxiong
     * DateTime: 2021/1/11 15:04
     * @param array $searchWhere ‘查询条件
     * @return array|mixed
     */
    public function getactivityOrder($searchWhere = [])
    {
        if($searchWhere['DataScope']) {
            $where = "";
            $like = "";
            $curr = $searchWhere['pages'];
            $limit = $searchWhere['rows'];
            if ($searchWhere['order_id'] != "") {
                $like = " and order_id like '%{$searchWhere['order_id']}%'";
            }
            if ($searchWhere['order_product'] != "") {
                $like = " and order_product like '%{$searchWhere['order_product']}%'";
            }
            if ($searchWhere['order_statue'] != "") {
                $where = $where . " and  order_statue in('{$searchWhere['order_statue']}')";
            }
            $where = $where . " and  order_type  in('活动')";
            $items = $this->get_orderdata($curr, $limit, $where, $like);
        }
        return $items;

    }//活动订单


    /**
     * Notes: 获取用户信息或者刷新
     * User: junxiong
     * DateTime: 2021/1/11 15:04
     * @param array $searchWhere ‘查询条件
     * @return array|mixed
     */
    public function getcourseOrder($searchWhere = [])
    {
        if($searchWhere['DataScope']) {
            $where = "";
            $like = "";
            $curr = $searchWhere['pages'];
            $limit = $searchWhere['rows'];
            if ($searchWhere['order_id'] != "") {
                $like = " and order_id like '%{$searchWhere['order_id']}%'";
            }
            if ($searchWhere['order_product'] != "") {
                $like = " and order_product like '%{$searchWhere['order_product']}%'";
            }
            if ($searchWhere['order_statue'] != "") {
                $where = $where . " and  order_statue in('{$searchWhere['order_statue']}')";
            }
            $where = $where . " and  order_type  in('培训')";
            $items = $this->get_orderdata($curr, $limit, $where, $like);
        }
        return $items;

    }//培训订单

    public function getdetialOrder($searchWhere = [])
    {
        $offset=($searchWhere['pages']-1)*$searchWhere['rows'];//计算偏移量
        $field='Select * ';
        $sql_query=$field." from orderitem where 1=1 "."and order_id= '".$searchWhere['order_id']."'";
        $sql_query_total=$sql_query;
        $sql_query=$sql_query." order by orderitem_created_time desc limit ".$offset.",".$searchWhere['rows'];
        $query = $this->db->query($sql_query);
        $ss=$this->db->last_query();
        $r_total=$this->db->query($sql_query_total)->result_array();
        $row_arr=$query->result_array();
        $result['total']=count($r_total);//获取总行数
        $result["data"] = $row_arr;
        return $result;

    }//订单详情

	public function modifyyesorno($values,$by)
	{
	    $where=[];
        $wheres=[];
	    if($values['ID']==true){
            $returnInfo = true;
            $this->db->trans_begin();
            $where['updated_by'] = $by;
            $where['order_statue'] = "已退款";
            $where['updated_time'] = date('Y-m-d H:i');
            $where['order_refund_flag'] = 1;
            $where['order_refund_datetime'] = $values['order_refund_datetime'];
            $this->Sys_Model->table_updateRow('order', $where, array('order_id' => $values['order_id']));
            $item=[];
            $wheres['sign_competition_id']=$values['order_capid'];
            $wheres['DeptId']=$values['order_deptid'];
            $wheres['members_id']=$values['members_id'];
            $wheres['sign_name']=$values['order_customer_name'];
            $wheres['sign_phone']=$values['order_customer_phone'];
            $item['sign_statue']="未付款";
            $this->Sys_Model->table_updateRow('sign_up', $item, $wheres);
            $row=$this->db->affected_rows();
            if (($this->db->trans_status() === FALSE) && $row<=0){
                $this->db->trans_rollback();
                $returnInfo = false;
            }else{
                $this->db->trans_commit();
            }
            return $returnInfo;
        }else{
            $where['updated_by'] = $by;
            $where['order_statue'] = "进行中";
            $where['updated_time'] = date('Y-m-d H:i');
            $where['order_refund_rate']=$values['order_refund_rate'];
            $where['order_refund_flag'] = 0;
            $result=$this->Sys_Model->table_updateRow('order', $where, array('order_id' => $values['order_id']));
            return $result;
        }

	}//同意，不同意退款


	public function modifyprice($values,$by)
	{
	    $where=[];
        $where['updated_by'] = $by;
        $where['updated_time'] = date('Y-m-d H:i');
        $where['order_price'] = $values['order_price'];
        $returnInfo=$this->Sys_Model->table_updateRow('order', $where, array('order_id' => $values['order_id']));
        return $returnInfo;
	}//修改订单价格



    public function modifystatu($values,$by)
    {
        $values['updated_by'] = $by;
        $values['updated_time'] = date('Y-m-d H:i');
        $returnInfo=$this->Sys_Model->table_updateRow('order', $values, array('order_id' => $values['order_id']));
        return $returnInfo;
    }//修改订单支付状态

	public function payment($values,$by)//支付
	{
	    if($values['ID']==true){//成功
            $indData['a']['created_by'] = $by;
            $indData['a']['created_time'] = date('Y-m-d H:i');
            $indData['a']['order_statue'] = "买家已付款";
            $indData['a']['order_type'] = "商品";
            $indData['a']['order_integral'] = $this->config->item('Back_integral')*$indData['a']['order_price'];;
            $returnInfo = true;
            $this->db->trans_begin();
            $this->Sys_Model->table_addRow("order", $indData['a'], 1);
            $competition=$this->db->insert_id();

            $resluts=[];
            foreach ($indData['b'] as $row){
                $row['orderitem_created_by'] = $by;
                $row['order_id'] = $competition;
                $row['orderitem_created_time'] = date('Y-m-d H:i');
                $wheres['commodity_id']=$row['commodity_id'];
                $wheres['commodity_spec_name']=$row['commodity_spec_name'];
                $wheres['commodity_spec_id']=$row['commodity_spec_id'];
                array_push($resluts,$row);
                $re=$this->Sys_Model->table_seleRow('commodity_id,amount',"commodity_spec",$wheres, $like=array());
                $where['amount']=$re[0]['amount']-$row['buy_num'];
                $this->Sys_Model->table_updateRow('commodity_spec', $where, $wheres);

                $wheres['members_id']=$row['members_id'];
                $this->Sys_Model->table_del("shop_cart",$wheres);

            }
            $this->Sys_Model->table_addRow("orderitem", $resluts, 2);
            $row=$this->db->affected_rows();
            if (($this->db->trans_status() === FALSE) && $row<=0){
                $this->db->trans_rollback();
                $returnInfo = false;
            }else{
                $this->db->trans_commit();
            }
            return $returnInfo;
        }else{//失败
            $indData['a']['created_by'] = $by;
            $indData['a']['created_time'] = date('Y-m-d H:i');
            $indData['a']['order_statue'] = "等待买家付款";
            $indData['a']['order_type'] = "商品";
            $returnInfo = true;
            $this->db->trans_begin();
            $this->Sys_Model->table_addRow("order", $indData['a'], 1);
            $competition=$this->db->insert_id();

            $resluts=[];
            foreach ($indData['b'] as $row){
                $row['orderitem_created_by'] = $by;
                $row['order_id'] = $competition;
                $row['orderitem_created_time'] = date('Y-m-d H:i');
                $wheres['commodity_id']=$row['commodity_id'];
                $wheres['commodity_spec_name']=$row['commodity_spec_name'];
                $wheres['commodity_spec_id']=$row['commodity_spec_id'];
                array_push($resluts,$row);
                $re=$this->Sys_Model->table_seleRow('commodity_id,amount',"commodity_spec",$wheres, $like=array());
                $where['amount']=$re[0]['amount']-$row['buy_num'];
                $this->Sys_Model->table_updateRow('commodity_spec', $where, $wheres);

                $wheres['members_id']=$row['members_id'];
                $this->Sys_Model->table_del("shop_cart",$wheres);

            }
            $this->Sys_Model->table_addRow("orderitem", $resluts, 2);
            $row=$this->db->affected_rows();
            if (($this->db->trans_status() === FALSE) && $row<=0){
                $this->db->trans_rollback();
                $returnInfo = false;
            }else{
                $this->db->trans_commit();
            }
            return $returnInfo;
        }

	}

	public function  shipped($values,$by)//卖家已发货
	{
        $values['updated_by'] = $by;
        $values['updated_time'] = date('Y-m-d H:i');
        $values['order_statue'] = "卖家已发货";
        $restulNum = $this->Sys_Model->table_updateRow('order', $values, array('order_id' => $values['order_id']));
        return $restulNum;
	}

    public function  receivedgood($values,$by)//确认收货
    {
        $values['updated_by'] = $by;
        $values['updated_time'] = date('Y-m-d H:i');
        $values['order_statue'] = "已完成";
        $returnInfo = true;
        $this->db->trans_begin();
        $this->Sys_Model->table_updateRow('order', $values, array('order_id' => $values['order_id']));//更新订单状态
        $money=$this->Sys_Model->table_seleRow('order_points,order_user,members_id,order_integral',"orderitem",['order_id'=>$values['order_id']], $like=array());
        foreach ($money as $rows) {
            if ($rows['order_user'] != "") {
                $where = [];
                $value = [];
                $where['members_id'] = $rows['order_user'];
                $ownintegral = $this->Sys_Model->table_seleRow('members_integral', "members", $where);
                $value['members_integral'] = $rows['order_points'] + $ownintegral[0]['members_integral'];
//            $this->config->item('Back_integral')*$money[0]['order_price'];
                $this->Sys_Model->table_updateRow('members', $value, $where);//更新推荐人积分
            }
            if($rows['order_integral']!=0) {
                $integral = $this->Sys_Model->table_seleRow('members_integral', "members", ['members_id' => $rows['members_id']]);
                $ownvalue['members_integral'] = $integral[0]['members_integral'] - $rows['order_integral'];
                $this->Sys_Model->table_updateRow('members', $ownvalue, ['members_id' => $rows['members_id']]);//更新购买人会员积分
                $row=$this->db->affected_rows();
            }
        }
        if (($this->db->trans_status() === FALSE) && $row<=0){
            $this->db->trans_rollback();
            $returnInfo = false;
        }else{
            $this->db->trans_commit();
        }


        return $returnInfo;
    }

	public function aftersales($values,$by)//申请售后
	{
        $values['orderitem_updated_by'] = $by;
        $values['orderitem_updated_time'] = date('Y-m-d H:i');
        $values['orderitem_status'] = 1;
        $returnInfo = true;
        $this->db->trans_begin();
        $this->Sys_Model->table_updateRow('orderitem', $values, array('orderitem_id' => $values['orderitem_id']));
        $where=[];
        $where['order_refund_flag']=3;
        $this->Sys_Model->table_updateRow('order', $where, array('order_id' => $values['order_id']));
        $row=$this->db->affected_rows();
        if (($this->db->trans_status() === FALSE) && $row<=0){
            $this->db->trans_rollback();
            $returnInfo = false;
        }else{
            $this->db->trans_commit();
        }
        return $returnInfo;
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
            $resultvalue['orderitem_image_refused']='/public/'.$dddir;
            return $resultvalue;
        }
    }

    public function readmaneypic($values)//读取目录下多图片
    {
        $res_url = [];
        $base_url='https://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
        $dir_name = $values['orderitem_image_refused'];
        $dir = '.'.$dir_name;
        if(file_exists($dir)){
            $handler = opendir($dir);
            if ($handler) {
                while (($filename = readdir($handler)) !== false) {
                    if ($filename != "." && $filename != "..") {
                        array_push($res_url,$base_url . $dir_name . '/' . $filename);
                    }
                }
            }
            closedir($handler);
        }
        return $res_url;
    }

    public function  selleragree($values,$by)//卖家是否同意
    {
        $values['orderitem_updated_by'] = $by;
        $values['orderitem_updated_time'] = date('Y-m-d H:i');
        $where=[];
        $where['order_refund_flag']=$values['order_refund_flag'];
        $values= bykey_reitem($values, 'order_refund_flag');
        $returnInfo = true;
        $this->db->trans_begin();
        $this->Sys_Model->table_updateRow('orderitem', $values, array('orderitem_id' => $values['orderitem_id']));//更新订单详情
        $this->Sys_Model->table_updateRow('order', $where, array('order_id' => $values['order_id']));//更新订单
        $row=$this->db->affected_rows();
        if (($this->db->trans_status() === FALSE) && $row<=0){
            $this->db->trans_rollback();
            $returnInfo = false;
        }else{
            $this->db->trans_commit();
        }
        return $returnInfo;
    }



    public function  aftersalessuccess($values,$by)//售后结束，只有状态4可以点
    {
        $where=[];
        $wheres=[];
        $where['created_by'] = $by;
        $where['created_time'] = date('Y-m-d H:i');
        $where['order_statue']="售后成功";
        $where['order_refund_flag']=$values['order_refund_flag'];
        $returnInfo = true;
        $this->db->trans_begin();
        $this->Sys_Model->table_updateRow('order', $where, array('order_id' => $values['order_id']));//更新订单
        $wheres['orderitem_status']=4;
        $this->Sys_Model->table_updateRow('orderitem', $wheres, array('orderitem_id' => $values['orderitem_id']));//更新订单详情
        $row=$this->db->affected_rows();
        if (($this->db->trans_status() === FALSE) && $row<=0){
            $this->db->trans_rollback();
            $returnInfo = false;
        }else{
            $this->db->trans_commit();
        }
        return $returnInfo;
    }

}







