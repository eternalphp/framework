<?php

/**
 * -------------------------------------------------------------------
 * 项目说明
 * -------------------------------------------------------------------
 * Author: yuanzhongyi <564165682@qq.com>
 * -------------------------------------------------------------------
 * Date: 2022-03-11
 * -------------------------------------------------------------------
 * Copyright (c) 2022~2025 http://www.homepage.com All rights reserved.
 * -------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * -------------------------------------------------------------------
 */


namespace App\{%path_name%}\Controllers;
use framework\Controller\Controller;

class {%controller_name%} extends Controller {
	
    function __construct() {
        parent::__construct();
		
		//列表显示字段
		$this->viewData->displayFields = [];
		
		//详情显示字段
		$this->viewData->viewFields = [];
		
		//表单字段
		$this->viewData->formFields = [];
    }
	
    /**
     * 显示资源列表
     *
     * @return view
     */
	function index(){
		$this->view('Layout.list');
	}
	
    /**
     * 显示创建资源表单页
     *
     * @return view
     */
	function add(){
		$this->view('Layout.add');
	}
	
    /**
     * 显示编辑资源表单页
     *
     * @return view
     */
	function edit(){
		$this->view('Layout.edit');
	}
	
    /**
     * 显示单条资源详情页
     *
     * @return view
     */
	function detail(){
		$this->view('Layout.detail');
	}
	
    /**
     * 创建或编辑数据存储
     *
     * @return view | json | js
     */
	function save(){
		
	}
	
    /**
     * 删除指定资源
     *
     * @return json
     */
	function remove(){
		
	}
}
?>