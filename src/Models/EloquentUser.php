<?php namespace WebEd\Base\Users\Models;

use WebEd\Base\Users\Models\Contracts\UserModelContract;
use WebEd\Base\Core\Models\EloquentBase as BaseModel;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use \WebEd\Base\ACL\Models\Traits\EloquentUserAuthorizable;

class EloquentUser extends BaseModel implements UserModelContract, AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    use EloquentUserAuthorizable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $fillable = ['username', 'email', 'first_name', 'last_name', 'display_name', 'password', 'sex', 'status', 'phone', 'mobile_phone', 'avatar'];

    /**
     * Get user avatar
     * @return mixed|string
     */
    public function getAvatarAttribute($value)
    {
        if (!isset($this->sex)) {
            $defaultAvt = '/admin/images/no-avatar-other.jpg';
        } else {
            $defaultAvt = '/admin/images/no-avatar-' . $this->sex . '.jpg';
        }

        return get_image($value, $defaultAvt);
    }

    /**
     * Hash the password before save to database
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
