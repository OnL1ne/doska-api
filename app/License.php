<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    CONST TABLE_NAME = 'licenses';
    CONST LICENSE_ID = 'id';
    CONST LICENSE_TITLE = 'title';
    CONST LICENSE_DESCRIPTION = 'description';
    CONST LICENSE_VALIDITY = 'validity';
    CONST ACTIVE = 'active';

    CONST LICENSE_COMPANY = 'default license';

    protected $fillable = [
        self::LICENSE_ID, self::LICENSE_TITLE, self::LICENSE_DESCRIPTION,
        self::LICENSE_VALIDITY, self::ACTIVE, self::CREATED_AT, self::UPDATED_AT,
    ];

    protected $hidden = [
        self::CREATED_AT, self::UPDATED_AT,
    ];
}
