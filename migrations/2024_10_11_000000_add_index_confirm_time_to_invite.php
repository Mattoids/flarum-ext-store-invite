<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->table('invite', function (Blueprint $table) {
            $table->index('confirm_time');
        });
    },

    'down' => function (Builder $schema) {
    },
];
