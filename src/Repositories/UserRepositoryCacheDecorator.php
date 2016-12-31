<?php namespace WebEd\Base\Users\Repositories;

use WebEd\Base\Caching\Repositories\AbstractRepositoryCacheDecorator;
use WebEd\Base\Users\Models\Contracts\UserModelContract;
use WebEd\Base\Users\Models\User;
use WebEd\Base\Users\Repositories\Contracts\UserRepositoryContract;

class UserRepositoryCacheDecorator extends AbstractRepositoryCacheDecorator implements UserRepositoryContract
{
    /**
     * @param \WebEd\Base\Users\Models\User $user
     */
    public function getRoles($user)
    {
        return $this->beforeGet(__FUNCTION__, func_get_args());
    }

    /**
     * @param array $data
     * @param bool $withEvent
     * @return array
     */
    public function createUser(array $data, $withEvent = true)
    {
        return $this->afterUpdate(__FUNCTION__, func_get_args());
    }

    /**
     * @param $id
     * @param array $data
     * @param bool $withEvent
     * @return array
     */
    public function updateUser($id, array $data, $withEvent = true)
    {
        return $this->afterUpdate(__FUNCTION__, func_get_args());
    }

    /**
     * @param \WebEd\Base\Users\Models\User $model
     * @param \Illuminate\Database\Eloquent\Collection|array $data
     */
    public function syncRoles($model, $data)
    {
        return $this->afterUpdate(__FUNCTION__, func_get_args());
    }

    /**
     * @param User|int $id
     * @return bool
     */
    public function isSuperAdmin($id)
    {
        return $this->beforeGet(__FUNCTION__, func_get_args());
    }

    /**
     * @param User|int $id
     * @param array ...$permissions
     * @return bool
     */
    public function hasPermission($id, ...$permissions)
    {
        return $this->beforeGet(__FUNCTION__, func_get_args());
    }

    /**
     * @param User|int $id
     * @param array ...$permissions
     * @return bool
     */
    public function hasRole($id, ...$roles)
    {
        return $this->beforeGet(__FUNCTION__, func_get_args());
    }
}
