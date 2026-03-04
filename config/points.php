<?php

return [
    // 1年
    'default_expire_days' => 365,
    'actions' => [
        'post.created'    => '投稿',
        'comment.created' => 'コメント/返信',
        // 将来：order.paid / order.refunded などを追加していける
    ],
];