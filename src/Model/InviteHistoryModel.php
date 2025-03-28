<?php

namespace Mattoid\StoreInvite\Model;

use Flarum\Database\AbstractModel;
use Flarum\Formatter\Formatter;
use Flarum\User\User;

class InviteHistoryModel extends AbstractModel
{
    // See https://docs.flarum.org/2.x/extend/models.html#backend-models for more information.

    protected $table = 'invite_history';

    /**
     * The text formatter instance.
     *
     * @var \Flarum\Formatter\Formatter
     */
    protected static $formatter;

    /**
     * Get the text formatter instance.
     *
     * @return \Flarum\Formatter\Formatter
     */
    public static function getFormatter()
    {
        return static::$formatter;
    }

    /**
     * Set the text formatter instance.
     *
     * @param \Flarum\Formatter\Formatter $formatter
     */
    public static function setFormatter(Formatter $formatter)
    {
        static::$formatter = $formatter;
    }

    public function User(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
