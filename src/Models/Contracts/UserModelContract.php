<?php namespace WebEd\Base\Users\Models\Contracts;

interface UserModelContract
{
    /**
     * Get user avatar
     * @return mixed|string
     */
    public function getAvatarAttribute($value);

    /**
     * Hash the password before save to database
     * @param $value
     */
    public function setPasswordAttribute($value);
}
