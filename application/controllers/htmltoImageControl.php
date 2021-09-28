<?php

/**
 * @OA\Info(
 *  title="职技通",
 *  version="1.0.0"
 * )
 *
 */
class htmltoImageControl extends CI_Controller
{


	private $dataArr = [];//操作数据

	function __construct()
	{
		parent::__construct();
		$this->load->helper('tool');
	}
    /**
     * @OA\Post(
     *		tags={"用户管理"},
     *		path="路由路径",
     *		summary="接口说明",
     *		@OA\RequestBody(
     *			@OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *					@OA\Property(
     *						property="reques_key",
     *						type="reques_type",
     *						description="参数说明"
     *					),
     *					example={
     *						"example_reques_key":"example_reques_val"
     *					}
     *				)
     *			)
     *		),
     *		@OA\Response(
     *			response=200,
     *			description="正确返回",
     *			@OA\JsonContent(
     *				@OA\Schema(
     *					@OA\Property(
     *						property="response_key",
     *						type="response_type",
     *						description="参数说明"
     *					)
     *				),
     *				example={
     *					"example_response_key":"example_response_val"
     *				}
     *			)
     *
     *		),
     *)
     **/





}
