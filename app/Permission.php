<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    CONST TABLE_NAME = 'permissions';
    CONST PERMISSION_ID = 'id';
    CONST PERMISSION_NAME = 'title';
    CONST ACTIVE = 'active';

    /* User management */
    CONST PERMISSION_VIEW_USERS_LIST = 'View the list of users';
    CONST PERMISSION_CREATE_USER_ACCOUNT = 'Create a user account';
    CONST PERMISSION_UPDATE_USER_ACCOUNT = 'Change a user account data';
    CONST PERMISSION_DELETE_USER_ACCOUNT = 'Delete a user account';
    CONST PERMISSION_MANAGE_USER_ACCOUNT_LICENSE = 'Manage user account license';
    /* Manage training */
    CONST PERMISSION_VIEW_TRAININGS_LIST = 'View the list of training';
    CONST PERMISSION_CREATE_TRAINING = 'Create a new training';
    CONST PERMISSION_UPDATE_TRAINING = 'Update a training';
    CONST PERMISSION_DELETE_TRAINING = 'Delete a training';
    /* Dashboard */
    CONST PERMISSION_VIEW_TOTAL_STATISTICS = 'View the total statistics';
    CONST PERMISSION_VIEW_SPECIFIC_ACCOUNT_STATISTICS = 'View a specific account statistics';
    /* Account settings management */
    CONST PERMISSION_CHANGE_PASSWORD = 'Change password';
    CONST PERMISSION_CHANGE_USER_NAME = 'Change user name';
    CONST PERMISSION_VIEW_LICENSE = 'View the license information';
    /* Manage company */
    CONST PERMISSION_VIEW_COMPANIES_LIST = 'View the list of Companies';
    CONST PERMISSION_CREATE_COMPANY = 'Create a new Company';
    CONST PERMISSION_DELETE_COMPANY = 'Delete a Company';
    CONST PERMISSION_UPDATE_COMPANY = 'Change company account data';
    /* Manage licenses */
    CONST PERMISSION_VIEW_LICENSES_LIST = 'View the list of licenses';
    CONST PERMISSION_CREATE_LICENSE = 'Create a new license';
    CONST PERMISSION_UPDATE_LICENSE = 'Change the license';
    CONST PERMISSION_DELETE_LICENSE = 'Delete a license';
    /* Agreements management */
    CONST PERMISSION_VIEW_AGREEMENTS = 'View the agreements';
    CONST PERMISSION_UPDATE_AGREEMENTS = 'Change agreement';


    protected $fillable = [
        self::PERMISSION_ID, self::PERMISSION_NAME, self::ACTIVE, self::CREATED_AT, self::UPDATED_AT,
    ];

    protected $hidden = [
        self::CREATED_AT, self::UPDATED_AT,
    ];
}
