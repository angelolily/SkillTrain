<?php
class CourseInfoControl extends CI_Controller{
    private $receive_data;

    public function __construct(){
        parent::__construct();
        $this->load->helper('tool');
        $this->load->service('CourseInfo');
        $receive = file_get_contents('php://input');
        $this->receive_data = json_decode($receive, true);
    }

    /**
     * @OA\Post(
     *    tags={"yjl"},
     *    path="ci/get_info",
     *    summary="获取课程信息",
     *	@OA\RequestBody(
     *		@OA\MediaType(
     *            mediaType="application/json",
     *			@OA\Schema(
     *				@OA\Property(
     *                    property="type",
     *                    type="string",
     *                    description="获取的课程信息类型(详情,列表,首页列表)"
     *                ),
     *              @OA\Property(
     *                  property="course_id",
     *                  type="string",
     *                  description="type为详情时的目标课程id"
     *              ),
     *                example={
     *                    "type":"index",
     *                    "course_id":""
     *                }
     *            )
     *        )
     *    ),
     *	@OA\Response(
     *        response=200,
     *        description="正确返回",
     *		@OA\JsonContent(
     *			@OA\Schema(
     *				@OA\Property(
     *                    property="Data",
     *                    type="arrary/object",
     *                    description="返回课程信息"
     *                )
     *            ),
     *            example={
     *                "course_cover":"https://192.168.2.2/SkillTrain/public/coursecover/163238380093.jpg"
     *                "course_describe":"测试课 收费"
     *                "course_id":"794AD463-B085-90D9-AE8A-57AD8CB254E2"
     *                "course_name":"测试课"
     *                "course_signBegin":"2021-09-23"
     *                "course_signEnd":"2021-09-25"
     *                "course_type":"公益"
     *            }
     *        )
     *    ),
     * )
     **/
    public function get_course_info(){
        $type = $this->receive_data['type'];
        $field = "course_id,course_name,course_describe,course_type,course_cover,course_signBegin,course_signEnd";
        if($type === 'index'){
            $where = array('course_ishome'=>1);
            $rk = 'index_info';
        }else if($type === 'list'){
            $where = [];
            $rk = 'c_info_list';
        }else if($type === 'detail'){
            $field = "*";
            $where = array('course_id'=>$this->receive_data['course_id']);
            $rk = 'c_detail_info';
        }else{
            $where = [];
            $rk = '';
        }
        $res = $this->courseinfo->get_course_info($field,$where,$rk);
        if(!$res){
            $resultArr = build_resultArr('GCI001', FALSE, 204,'获取课程信息错误', null );
            http_data(204, $resultArr, $this);
        }
        $final_res = $res;
        $base_url='http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
        for($i=0; $i<count($final_res); $i++){
            if(array_key_exists('course_cover',$final_res[$i])){
                $final_res[$i]['course_cover'] = $base_url.'public/coursecover/'.$final_res[$i]['course_cover'];
            }
            if(array_key_exists('course_graphic',$final_res[$i])){
                $final_res[$i]['course_graphic'] = $base_url.'public/coursegraphic/'.$final_res[$i]['course_graphic'];
            }
        }
        if($type === 'detail'){
            $final_res = $final_res[0];
        }
        $resultArr = build_resultArr('GCI000', TRUE, 0,'获取课程信息成功', $final_res );
        http_data(200, $resultArr, $this);
    }
    public function test(){

    }
}