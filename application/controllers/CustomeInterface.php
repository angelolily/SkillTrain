<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Content-type: text/html; charset=utf-8');

/**
 * Class CustomeInterface
 * 客户端接口类
 *
 */
class CustomeInterface extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->service('wProductStore');
        $this->load->helper('tool');
    }


    /**
     * 8、上传图片
     *
     */
    public function AdvertImageUpload()
    {
        $resultvalue = array();

        $dir = './public/advert';
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
            $pptfiles=join(',',$pptfiles);
            $resultvalue['Advert_image']=$this->config->item('localpath')."/public/advert/".$pptfiles;
            http_data(200, $resultvalue, $this);
        }
    }

    /**
     * 9、获取广告数据
     *
     */

    public function getAdvertData()
    {
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        if (array_key_exists("advertTitle", $info) && array_key_exists("pages", $info) && array_key_exists("rows", $info)) {

            $requestData = $this->wproductstore->geAdvert($info);

            http_data(200, $requestData, $this);


        } else {
            $requestData['Data'] = '';
            $requestData["ErrorCode"] = "parameter-error";
            $requestData["ErrorMessage"] = "参数接收错误";
            $requestData["Success"] = false;
            $requestData["Status_Code"] = "OSS203";

        }


    }


    /**
     * 10、新增广告
     *
     */
    public function newAdvert()
    {


        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo,true);

        if(count($info)>0)
        {
            $info=bykey_reitem($info, 'phone');
            $info=bykey_reitem($info, 'timestamp');
            $info=bykey_reitem($info, 'signature');
            $info['created_time']=date("Y-m-d H:i");
            $resultNum = $this->wproductstore->addGeneral("advert", $info);
            if ($resultNum> 0) {
                $resulArr = build_resulArr('AD000', true, '插入成功', []);
                http_data(200, $resulArr, $this);
            } else {
                $resulArr = build_resulArr('AD002', false, '插入失败', []);
                http_data(200, $resulArr, $this);
            }
        }
        else{
            $resulArr = build_resulArr('AD001', false, '参数接收失败', []);
            http_data(200, $resulArr, $this);
        }




    }


    /**
     * 11、修改广告
     * User:
     *
     */

    public function updateAdvert()
    {

        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo,true);

        if(count($info)>0)
        {
            $info=bykey_reitem($info, 'phone');
            $info=bykey_reitem($info, 'timestamp');
            $info=bykey_reitem($info, 'signature');
            $info['updated_time']=date("Y-m-d H:i");
            $where['advertId']=$info['advertId'];
            $resultNum = $this->wproductstore->updateGeneral("advert",$info,$where);
            if ($resultNum> 0) {
                $resulArr = build_resulArr('ADU00', true, '修改成功', []);
                http_data(200, $resulArr, $this);
            } else {
                $resulArr = build_resulArr('ADU02', false, '修改失败', []);
                http_data(200, $resulArr, $this);
            }
        }
        else{
            $resulArr = build_resulArr('ADU01', false, '参数接收失败', []);
            http_data(200, $resulArr, $this);
        }

    }

<<<<<<< HEAD
    //导出Excel
    public function ControlExcel()
    {
        $agentinfo = file_get_contents('php://input');
        $receiveData = json_decode($agentinfo, true);
        $table=$receiveData['table'];
        $where=$receiveData['where'][0];
        $like=$receiveData['like'][0];


        $requestData = $this->wproductstore->outExcel($table,$where,$like);


        http_data(200, $requestData, $this);

    }

=======
>>>>>>> 62b93cfcc187df8541e5e5e57709818b36c80848

    /**
     * 12、删除广告
     * User:
     *
     */

    public function delAdvert()
    {

        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo,true);

        if(count($info)>0)
        {
            $info=bykey_reitem($info, 'phone');
            $info=bykey_reitem($info, 'timestamp');
            $info=bykey_reitem($info, 'signature');

            $resultNum = $this->wproductstore->delGeneral("advert", $info);
            if ($resultNum>0) {
                $resulArr = build_resulArr('ADU00', true, '删除成功', []);
                http_data(200, $resulArr, $this);
            } else {
                $resulArr = build_resulArr('ADU02', false, '删除失败', []);
                http_data(200, $resulArr, $this);
            }
        }
        else{
            $resulArr = build_resulArr('ADU01', false, '参数接收失败', []);
            http_data(200, $resulArr, $this);
        }

    }















}