<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;

return Migration::createTable(
    'invite_history_models',
    function (Blueprint $table) {
        $table->increments('id');

        // created_at & updated_at
        $table->timestamps();
    }
);

