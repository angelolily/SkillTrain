<?php


/**
 * Class 消息管理
 */
class MsgService extends HTY_service
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
    //获取消息数据
    public function getMessage($searchWhere = [])
    {
        $like = [];
        $where = [];
        $data=[];
        if (count($searchWhere) > 0) {

            if ($searchWhere['class_id'] != '') {//班级id
                $where['class_id']=$searchWhere['class_id'];
            }
            if ($searchWhere['members_name'] != '') {//会员名称名称
                $where['$where']=$searchWhere['members_name'];
            }

            $pages = $searchWhere['pages'];
            $rows = $searchWhere['rows'];
            $offset = ($pages - 1) * $rows;//计算偏移量
            $arr_total=$this->Sys_Model->table_seleRow_limit("*","message",$where,$like);

            if(count($arr_total)>0){
                $deptTmpArr['total'] = count($arr_total);//获取总行数
                $data = $this->Sys_Model->table_seleRow_limit("*","message",$where,$like,$rows,$offset);
                $deptTmpArr['data']=$data;
            }
            else{
                $deptTmpArr['total']=[];
                $deptTmpArr['data']=[];
            }





        }
        return $deptTmpArr;
    }

    //新增消息数据
    public function addMessage($data){

        $rowscount=$this->Sys_Model->table_addRow("message",$data,2);
        if($rowscount==count($data)){
            return 1;
        }elseif ($rowscount>0){
            return 2;//部分插入成功
        }
        else{
            return 3;
        }



    }

}







