<?php

namespace App;

use App\Notifications\EmailVerificationNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;

    CONST TABLE_NAME = 'users';
    CONST ID = 'id';
    CONST NAME = 'name';
    CONST EMAIL = 'email';
    CONST PASSWORD = 'password';
    CONST REMEMBER_TOKEN = 'remember_token';
    CONST EMAIL_VERIFIED_AT = 'email_verified_at';
    CONST ACCESS_LEVEL = 'access_level';
    CONST COMPANY_ID = 'company_id';
    CONST ROLE_ID = 'role_id';
    CONST ACTIVE = 'active';
    CONST CREATED_AT = 'created_at';
    CONST UPDATED_AT = 'updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::NAME, self::EMAIL, self::PASSWORD, self::ACTIVE, self::CREATED_AT, self::UPDATED_AT, self::ROLE_ID,
        self::COMPANY_ID, self::EMAIL_VERIFIED_AT, self::PASSWORD,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        self::PASSWORD, self::REMEMBER_TOKEN, self::CREATED_AT, self::UPDATED_AT, self::ACTIVE, self::ACCESS_LEVEL
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        self::EMAIL_VERIFIED_AT => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo
     */
    public function detail()
    {
        return $this->belongsTo(UserDetail::class, User::ID, UserDetail::USER_ID);
    }

    /**
     * @return HasMany
     */
    public function events()
    {
        return $this->hasMany(Event::class, User::ID, Event::USER_ID);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification());
    }
}
