<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;

return Migration::createTable(
    'invite',
    function (Blueprint $table) {
        $table->increments('id');

        $table->integer('user_id')->comment('发邀人用户ID')->index();
        $table->string('email')->comment('受邀人邮箱')->index();
        $table->string('username')->comment('受邀人用户名')->index();
        $table->string('link')->comment('受邀人个人链接');
        $table->string('link2')->comment('受邀人个人链接2');
        $table->integer('status')->default(0)->comment('审核状态 0-未审核 1-审核通过 2-审核拒绝');
        $table->text('apply_remark')->comment('申请备注');
        $table->integer('confirm_user_id')->comment('审核人员');
        $table->text('confirm_remark')->comment('审核意见');
        $table->dateTime('confirm_time')->comment('审核时间');

        // created_at & updated_at
        $table->timestamps();
    }
);

