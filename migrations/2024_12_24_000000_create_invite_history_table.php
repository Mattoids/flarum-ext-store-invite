<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;

return Migration::createTable(
    'invite_history',
    function (Blueprint $table) {
        $table->increments('id');

        $table->integer('user_id')->nullable();
        $table->char("year", 4)->nullable()->comment('年份');
        $table->integer("apply")->default(0)->comment("申请数量");
        $table->integer("pass")->default(0)->comment("通过数量");

        $table->timestamp('created_at')->useCurrent();

        $table->unique(["user_id", "year"]);
    }
);

