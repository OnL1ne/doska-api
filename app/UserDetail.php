<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    CONST TABLE_NAME = 'user_details';
    CONST ID = 'id';
    CONST USER_ID = 'user_id';
    CONST FIRST_NAME = 'first_name';
    CONST LAST_NAME = 'last_name';
    CONST TITLE = 'title';
    CONST CREATED_AT = 'created_at';
    CONST UPDATED_AT = 'updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::FIRST_NAME, self::LAST_NAME, self::TITLE, self::CREATED_AT, self::UPDATED_AT, self::USER_ID,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        self::CREATED_AT, self::UPDATED_AT, self::ID,
    ];
}
