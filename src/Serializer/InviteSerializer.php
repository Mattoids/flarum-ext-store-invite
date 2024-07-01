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
        return [
            'id' => $data->id,
            'user' => $data->user->username,
            'userImg' => $data->user->avatar_url,
            'email' => $data->email,
            'username' => $data->username,
            'link' => $data->link,
            'link2' => $data->link2,
            'status' => $data->status,
            'confirmUser' => $data->confirmUser->username,
            'confirmUserImg' => $data->confirmUser->avatar_url,
            'applyRemark' => $data->apply_remark,
            'confirmTime' => $data->confirm_time,
            'confirmRemark' => $data->confirm_remark,
            'createdAt' => $data->created_at,
            'updatedAt' => $data->updated_at,
        ];
    }

}
