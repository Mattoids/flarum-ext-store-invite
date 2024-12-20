<?php

namespace Mattoid\StoreInvite\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Locale\Translator;

class InviteSerializer extends AbstractSerializer
{

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    protected function getDefaultAttributes($data) {

        $confirmUserName = $data->confirmUser ? $data->confirmUser->username : '';
        $confirmAvatarUrl = $data->confirmUser ? $data->confirmUser->avatar_url : '';

        $attributes = [
            'id' => $data->id,
            'user' => $data->user->username,
            'userImg' => $data->user->avatar_url,
            'email' => $data->email,
            'username' => $data->username,
            'link' => $data->link,
            'link2' => $data->link2,
            'status' => $data->status,
            'confirmUser' => $confirmUserName,
            'confirmUserImg' => $confirmAvatarUrl,
            'applyRemark' => $data->apply_remark,
            'confirmTime' => $data->confirm_time,
            'confirmRemark' => $data->confirm_remark,
            'createdAt' => $data->created_at,
            'updatedAt' => $data->updated_at,
            'totalNum'  => $data->totalNum,
            'passTotalNum' => $data->passTotalNum,
            'inviteCode' => '',
            'notes' => $data->notes,
            'postNum' => $data->postNum,
            'userCreateTime' => $data->userCreateTime,
        ];

        if ($data -> status == 1) {
            $attributes['inviteCode'] = $data->invite_code;
        }

        if (!$this->actor->can('mattoid-store-invite.group-admin-view')) {
            $attributes["confirmUser"] = "";
            $attributes["confirmUserImg"] = "";
            $attributes["notes"] = 0;
        }

        return $attributes;
    }

}
