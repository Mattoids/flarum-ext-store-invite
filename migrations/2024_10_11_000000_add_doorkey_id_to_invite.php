<?php

use Flarum\Database\Migration;

return Migration::addColumns('invite',
    [
    'doorkey_id' => ['integer', 'default' => 0, 'comment' => '对应 doorkey 表的ID，用于删除超时的验证码'],
    'is_expire' => ['integer', 'default' => 0, 'comment' => '过期标志 0-未过期 1-已过期'],
]);
