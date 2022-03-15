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
		Schema::{%method_name%}("{%table_name%}",function($table){
			$table->integer("id")->unsigned()->increments();
			$table->string("name");
			$table->string("title");
			$table->integer("age");
			//$table->dropColumn('name');
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