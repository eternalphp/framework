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


use framework\Database\Schema\Control;
use framework\Database\Eloquent\Model;
use framework\Database\Schema\Table;
use framework\Database\Schema\Schema;
use framework\Database\Migrations\Migration;

class {%migration_name%} extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create("{%table_name%}",function($table){
			$table->integer("id")->unsigned()->increments();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('{%table_name%}');
    }
}
?>