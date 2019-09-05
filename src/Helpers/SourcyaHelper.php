<?php

namespace Sourcya\BoilerplateBox\Helpers;

class SourcyaHelper
{
    /**
     * @param string $id
     * @return string
     */
    public static function getUserAvatar($id, $withExt = true)
    {
        $user = auth()->user()->findorFail($id);
        if(!$user->files()->where('type','=','avatar')->exists())
            return false;

        $user = $user->files()->where('type','=','avatar')->get();
        if ($withExt)
            return $user->pluck('path')->implode(' ').'.'.$user->pluck('extension')->implode(' ');
        else
            return $user->pluck('path')->implode(' ');
    }
}
