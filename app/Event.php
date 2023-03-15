<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
Use Carbon\Carbon;

class Event extends Model
{
    use Notifiable;

    CONST TABLE_NAME = 'events';
    CONST ID = 'id';
    CONST USER_ID = 'user_id';
    CONST TITLE = 'title';
    CONST DESCRIPTION = 'description';
    CONST CREATED_AT = 'created_at';
    CONST UPDATED_AT = 'updated_at';

    /* Trainings events */
    CONST EVENT_CREATE_TRAINING = 'create training';
    CONST EVENT_UPDATE_TRAINING = 'update training';
    CONST EVENT_DELETE_TRAINING = 'delete training';
    CONST EVENT_DOWNLOAD_TRAINING = 'download training';
    /* Users events */
    CONST EVENT_CREATE_USER = 'create user';
    CONST EVENT_UPDATE_USER = 'update user';
    CONST EVENT_DELETE_USER = 'delete user';
    CONST EVENT_UPDATE_PROFILE = 'update profile';
    CONST EVENT_LOGIN_USER = 'login';
    /* Companies events */
    CONST EVENT_CREATE_COMPANY = 'create company';
    CONST EVENT_UPDATE_COMPANY = 'create training';
    CONST EVENT_DELETE_COMPANY = 'create training';
    /* License events */
    CONST EVENT_CREATE_LICENSE = 'create license';
    CONST EVENT_UPDATE_LICENSE = 'update license';
    CONST EVENT_DELETE_LICENSE = 'delete license';
    /* Change password */
    CONST EVENT_CHANGE_PASSWORD = 'change users password';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::USER_ID, self::TITLE, self::CREATED_AT, self::UPDATED_AT, self::DESCRIPTION,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        self::UPDATED_AT,
    ];

    public function getCreatedAtAttribute($timestamp) {
        return Carbon::parse($timestamp)->format('H:i d M, Y');
    }

    /**
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, User::ID, self::USER_ID);
    }
}
