<?php


/**
 * Class wProductStore
 * 产品商城操作类
 */
class wProductStore extends HTY_service
{
	/**
	 * Dept constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Custome_Model');
        $this->load->helper('url');
	}



    //通用添加记录
    public function addGeneral($table,$Datas){

        return $this->Custome_Model->table_addRow($table,$Datas,1);

    }

    //通用修改记录
    public function updateGeneral($table,$Datas,$where){

        return $this->Custome_Model->table_updateRow($table,$Datas,$where);

    }

    //通用添加记录
    public function delGeneral($table,$where){

        return $this->Custome_Model->table_del($table,$where);

    }

    //通用查询不带where,分页
    public function getAllGeneral($table,$fields,$wheres=[])
    {

        $mywhere=$wheres;
        $alladvert=$this->Custome_Model->table_seleRow($fields,$table,$mywhere,[]);
        $ss=$this->db->last_query();
        if(count($alladvert)>0)
        {

            $appdata['Data']['data']=$alladvert;
            $appdata["ErrorCode"]="";
            $appdata["ErrorMessage"]="数据获取成功";
            $appdata["Success"]=true;
            $appdata["Status_Code"]="G200";
        }
        else
        {
            $appdata['Data']=[];
            $appdata["ErrorCode"]="";
            $appdata["ErrorMessage"]="无数据";
            $appdata["Success"]=false;
            $appdata["Status_Code"]="G201";
        }



        return $appdata;

    }



    //根据广告名称获取报名信息

    public function geAdvert($searchWhere = []){
        $pages = $searchWhere['pages'];
        $rows = $searchWhere['rows'];

        $offset=($pages-1)*$rows;//计算偏移量


        $like=[];

        if($searchWhere['advertTitle']!="" )
        {
            $like=['advertTitle'=>$searchWhere['advertTitle']];

        }

        $alladvert=$this->Custome_Model->table_seleRow("advertId","advert",[],$like);
        $allad_list=$this->Custome_Model->table_seleRow_limit("*","advert",
            [],$like,$rows,$offset,"advertId","DESC");


        if(count($allad_list)>0)
        {
            $appdata['Data']['total']=count($alladvert);
            $appdata['Data']['data']=$allad_list;
            $appdata["ErrorCode"]="";
            $appdata["ErrorMessage"]="广告表获取成功";
            $appdata["Success"]=true;
            $appdata["Status_Code"]="ADVERT200";
        }
        else
        {
            $appdata['Data']=[];
            $appdata["ErrorCode"]="";
            $appdata["ErrorMessage"]="无广告表数据";
            $appdata["Success"]=false;
            $appdata["Status_Code"]="ADVERT201";
        }



        return $appdata;

    }



    

    





}







