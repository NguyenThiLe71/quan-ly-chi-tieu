<?php

namespace App\Helpers;

use App\Models\UserInteraction;

class InteractionHelper
{
    public static function log(
        $userId,
        $module,
        $action,
        $keyword = null,
        $note = null
    ) {
        UserInteraction::create([
            'user_id'    => $userId,
            'module'     => strtolower($module),
            'action'     => strtolower($action),
            'keyword'    => $keyword,
            'note'       => $note,
            'ip'         => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}