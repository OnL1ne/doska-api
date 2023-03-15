<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    CONST TABLE_NAME = 'roles';
    CONST USERS_ROLE_ID = 'id';
    CONST USERS_ROLE_NAME = 'name';
    CONST ACTIVE = 'active';
    CONST CREATED_AT = 'created_at';
    CONST UPDATED_AT = 'updated_at';

    CONST ADMIN = 'admin';
    CONST MANAGER = 'manager';
    CONST INSTRUCTOR = 'instructor';
    CONST TRAINEE = 'trainee';

    protected $fillable = [
        self::USERS_ROLE_ID, self::USERS_ROLE_NAME, self::ACTIVE, self::CREATED_AT, self::UPDATED_AT,
    ];

    protected $hidden = [
        self::CREATED_AT, self::UPDATED_AT,
    ];

    /**
     * @return BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions', 'role_id', 'permission_id');
    }
}
