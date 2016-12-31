<?php namespace WebEd\Base\Users\Repositories\Contracts;

use WebEd\Base\Users\Models\User;

interface UserRepositoryContract
{
    /**
     * @param array $data
     * @return mixed
     */
    public function createUser(array $data);

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function updateUser($id, array $data);

    /**
     * @param mixed $model
     * @param \Illuminate\Database\Eloquent\Collection|array $data
     */
    public function syncRoles($model, $data);

    /**
     * @param $user
     * @return mixed
     */
    public function getRoles($user);

    /**
     * @param User|int $id
     * @param array ...$permissions
     * @return bool
     */
    public function hasPermission($id, ...$permissions);

    /**
     * @param User|int $id
     * @param array ...$roles
     * @return bool
     */
    public function hasRole($id, ...$roles);
}
