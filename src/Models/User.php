<?php namespace WebEd\Base\Users\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use WebEd\Base\Users\Models\Contracts\UserModelContract;
use WebEd\Base\Core\Models\EloquentBase as BaseModel;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use \WebEd\Base\ACL\Models\Traits\UserAuthorizable;

class User extends BaseModel implements UserModelContract, AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    use UserAuthorizable;

    use SoftDeletes;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $fillable = [
        'username', 'email', 'password',
        'first_name', 'last_name', 'display_name',
        'sex', 'status', 'phone', 'mobile_phone', 'avatar',
        'birthday', 'description', 'disabled_until',
    ];

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

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = str_slug($value, '_');
    }
}
