<?php

class CourseRenControl extends CI_Controller{
    private $receive_data;
    public function __construct(){
        parent::__construct();
        $this->load->helper('tool');
        $this->load->service('CourseRen');
        $receive = file_get_contents('php://input');
        $this->receive_data = json_decode($receive, true);
    }
    /**
     * @OA\Post(
     *    tags={"hyr"},
     *    path="cr/gs",
     *    summary="接口说明",
     *	@OA\RequestBody(
     *		@OA\MediaType(
     *            mediaType="application/json",
     *			@OA\Schema(
     *				@OA\Property(
     *                    property="members_id",
     *                    type="string",
     *                    description="会员id"
     *                ),
     *                example={
     *                    "members_id":"1"
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
     *                    property="response_key",
     *                    type="response_type",
     *                    description="参数说明"
     *                ),
     *            ),
     *            example={
     *                "example_response_key":"example_response_val"
     *            }
     *        )
     *    ),
     * )
     **/
    public function getSchedule(){
        $result = $this->courseren->getSchedule($this->receive_data);
        if (count($result) > 0) {
            $resultArr = build_resultArr('gs000', true, 0,'获取成功',json_encode($result) );
            http_data(200, $resultArr, $this);
        } else {
            $resultArr = build_resultArr('gs002', false, 0,'获取失败', []);
            http_data(200, $resultArr, $this);
        }
    }
    public function getTeacherSchedule(){
        $result = $this->courseren->getTeacherSchedule($this->receive_data);
        if (count($result) > 0) {
            $resultArr = build_resultArr('gs000', true, 0,'获取成功',json_encode($result) );
            http_data(200, $resultArr, $this);
        } else {
            $resultArr = build_resultArr('gs002', false, 0,'获取失败', []);
            http_data(200, $resultArr, $this);
        }
    }
    /**
     * @OA\Post(
     *    tags={"hyr"},
     *    path="cr/gc",
     *    summary="获取用户所有课程",
     *	@OA\RequestBody(
     *		@OA\MediaType(
     *            mediaType="application/json",
     *			@OA\Schema(
     *				@OA\Property(
     *                    property="members_id",
     *                    type="string",
     *                    description="会员id"
     *                ),
     *                example={
     *                    "members_id":"1"
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
     *                    property="response_key",
     *                    type="response_type",
     *                    description="参数说明"
     *                ),
     *            ),
     *            example={
     *                "example_response_key":"example_response_val"
     *            }
     *        )
     *    ),
     * )
     **/
    public function getCourse(){
        $result = $this->courseren->getCourse($this->receive_data);
        if (count($result) > 0) {
            $resultArr = build_resultArr('gc000', true, 0,'获取成功',json_encode($result) );
            http_data(200, $resultArr, $this);
        } else {
            $resultArr = build_resultArr('gc002', false, 0,'获取失败', []);
            http_data(200, $resultArr, $this);
        }
    }
    public function getOrder(){
        $result = $this->courseren->getOrder($this->receive_data);
        if (count($result) > 0) {
            $resultArr = build_resultArr('go000', true, 0,'获取成功',json_encode($result) );
            http_data(200, $resultArr, $this);
        } else {
            $resultArr = build_resultArr('go002', false, 0,'获取失败', []);
            http_data(200, $resultArr, $this);
        }
    }
    /**
     * @OA\Post(
     *    tags={"hyr"},
     *    path="cr/gc",
     *    summary="获取用户所有课程",
     *	@OA\RequestBody(
     *		@OA\MediaType(
     *            mediaType="application/json",
     *			@OA\Schema(
     *				@OA\Property(
     *                    property="members_id",
     *                    type="string",
     *                    description="会员id"
     *                ),
     *                example={
     *                    "members_id":"1"
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
     *                    property="response_key",
     *                    type="response_type",
     *                    description="参数说明"
     *                ),
     *            ),
     *            example={
     *                "example_response_key":"example_response_val"
     *            }
     *        )
     *    ),
     * )
     **/
    public function getMessage(){
        $result = $this->courseren->getMessage($this->receive_data);
        if (count($result) > 0) {
            $resultArr = build_resultArr('gm000', true, 0,'获取成功',json_encode($result) );
            http_data(200, $resultArr, $this);
        } else {
            $resultArr = build_resultArr('gm002', false, 0,'获取失败', []);
            http_data(200, $resultArr, $this);
        }
    }
    /**
     * @OA\Post(
     *    tags={"hyr"},
     *    path="cr/gc",
     *    summary="获取用户所有课程",
     *	@OA\RequestBody(
     *		@OA\MediaType(
     *            mediaType="application/json",
     *			@OA\Schema(
     *				@OA\Property(
     *                    property="members_id",
     *                    type="string",
     *                    description="会员id"
     *                ),
     *                example={
     *                    "members_id":"1"
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
     *                    property="response_key",
     *                    type="response_type",
     *                    description="参数说明"
     *                ),
     *            ),
     *            example={
     *                "example_response_key":"example_response_val"
     *            }
     *        )
     *    ),
     * )
     **/
    public function updateMessage(){
        $result = $this->courseren->updateMessage($this->receive_data);
        if (count($result) > 0) {
            $resultArr = build_resultArr('um000', true, 0,'获取成功',[] );
            http_data(200, $resultArr, $this);
        } else {
            $resultArr = build_resultArr('um002', false, 0,'获取失败', []);
            http_data(200, $resultArr, $this);
        }
    }

    /**
     * @OA\Post(
     *    tags={"hyr"},
     *    path="cr/gc",
     *    summary="获取用户所有课程",
     *	@OA\RequestBody(
     *		@OA\MediaType(
     *            mediaType="application/json",
     *			@OA\Schema(
     *				@OA\Property(
     *                    property="members_id",
     *                    type="string",
     *                    description="会员id"
     *                ),
     *                example={
     *                    "members_id":"1"
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
     *                    property="response_key",
     *                    type="response_type",
     *                    description="参数说明"
     *                ),
     *            ),
     *            example={
     *                "example_response_key":"example_response_val"
     *            }
     *        )
     *    ),
     * )
     **/
    public function sign(){
        $result = $this->courseren->sign($this->receive_data);
        if (count($result) > 0) {
            $resultArr = build_resultArr('sg000', true, 0,'签到成功',[] );
            http_data(200, $resultArr, $this);
        } else {
            $resultArr = build_resultArr('sg002', false, 0,'签到失败', []);
            http_data(200, $resultArr, $this);
        }
    }
}