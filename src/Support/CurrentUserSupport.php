<?php namespace WebEd\Base\Users\Support;

use WebEd\Base\Users\Models\Contracts\UserModelContract;
use WebEd\Base\Users\Models\User;

class CurrentUserSupport
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @param UserModelContract $user
     * @return $this
     */
    public function setUser(UserModelContract $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }
}
