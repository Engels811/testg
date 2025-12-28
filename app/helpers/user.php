<?php

function user_avatar(array $user): string
{
    if (!empty($user['avatar']) && file_exists(BASE_PATH . '/public/uploads/avatars/' . $user['avatar'])) {
        return '/uploads/avatars/' . $user['avatar'];
    }

    return '/assets/img/avatar-default.png';
}
