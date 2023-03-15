<?php

use Illuminate\Database\Seeder;
use App\Company;
use App\User;
use App\Role;
use App\Permission;
use App\License;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->make_license();
        $this->make_company();
        $this->make_permissions_roles();
        $this->make_users();
    }


    private function make_license()
    {
        License::create([License::LICENSE_ID => 1, License::LICENSE_TITLE => License::LICENSE_COMPANY, License::ACTIVE => 0]);
        License::create([License::LICENSE_ID => 2, License::LICENSE_TITLE => 'Month', License::ACTIVE => 1, License::LICENSE_VALIDITY => '30']);
        License::create([License::LICENSE_ID => 3, License::LICENSE_TITLE => 'Year', License::ACTIVE => 1, License::LICENSE_VALIDITY => '360']);
    }

    private function make_company()
    {
        Company::create([Company::COMPANY_ID => 1, Company::COMPANY_NAME => Company::DEFAULT_COMPANY, Company::COMPANY_LICENSE_ID => 1, Company::ACTIVE => 0]);
        Company::create([Company::COMPANY_ID => 2, Company::COMPANY_NAME => 'test company', Company::COMPANY_LICENSE_ID => 1, Company::COMPANY_LICENSE_START => '2020-05-01']);
    }

    private function make_permissions_roles()
    {
        /* Permissions */
        $PERMISSION_VIEW_USERS_LIST = Permission::create([Permission::PERMISSION_ID => 1, Permission::PERMISSION_NAME => Permission::PERMISSION_VIEW_USERS_LIST]);
        $PERMISSION_CREATE_USER_ACCOUNT = Permission::create([Permission::PERMISSION_ID => 2, Permission::PERMISSION_NAME => Permission::PERMISSION_CREATE_USER_ACCOUNT]);
        $PERMISSION_UPDATE_USER_ACCOUNT = Permission::create([Permission::PERMISSION_ID => 3, Permission::PERMISSION_NAME => Permission::PERMISSION_UPDATE_USER_ACCOUNT]);
        $PERMISSION_DELETE_USER_ACCOUNT = Permission::create([Permission::PERMISSION_ID => 4, Permission::PERMISSION_NAME => Permission::PERMISSION_DELETE_USER_ACCOUNT]);
        $PERMISSION_MANAGE_USER_ACCOUNT_LICENSE = Permission::create([Permission::PERMISSION_ID => 5, Permission::PERMISSION_NAME => Permission::PERMISSION_MANAGE_USER_ACCOUNT_LICENSE]);
        $PERMISSION_VIEW_TRAININGS_LIST = Permission::create([Permission::PERMISSION_ID => 6, Permission::PERMISSION_NAME => Permission::PERMISSION_VIEW_TRAININGS_LIST]);
        $PERMISSION_CREATE_TRAINING = Permission::create([Permission::PERMISSION_ID => 7, Permission::PERMISSION_NAME => Permission::PERMISSION_CREATE_TRAINING]);
        $PERMISSION_UPDATE_TRAINING = Permission::create([Permission::PERMISSION_ID => 8, Permission::PERMISSION_NAME => Permission::PERMISSION_UPDATE_TRAINING]);
        $PERMISSION_DELETE_TRAINING = Permission::create([Permission::PERMISSION_ID => 9, Permission::PERMISSION_NAME => Permission::PERMISSION_DELETE_TRAINING]);
        $PERMISSION_VIEW_TOTAL_STATISTICS = Permission::create([Permission::PERMISSION_ID => 10, Permission::PERMISSION_NAME => Permission::PERMISSION_VIEW_TOTAL_STATISTICS]);
        $PERMISSION_VIEW_SPECIFIC_ACCOUNT_STATISTICS = Permission::create([Permission::PERMISSION_ID => 11, Permission::PERMISSION_NAME => Permission::PERMISSION_VIEW_SPECIFIC_ACCOUNT_STATISTICS]);
        $PERMISSION_CHANGE_PASSWORD = Permission::create([Permission::PERMISSION_ID => 12, Permission::PERMISSION_NAME => Permission::PERMISSION_CHANGE_PASSWORD]);
        $PERMISSION_CHANGE_USER_NAME = Permission::create([Permission::PERMISSION_ID => 13, Permission::PERMISSION_NAME => Permission::PERMISSION_CHANGE_USER_NAME]);
        $PERMISSION_VIEW_LICENSE = Permission::create([Permission::PERMISSION_ID => 14, Permission::PERMISSION_NAME => Permission::PERMISSION_VIEW_LICENSE]);
        $PERMISSION_VIEW_COMPANIES_LIST = Permission::create([Permission::PERMISSION_ID => 15, Permission::PERMISSION_NAME => Permission::PERMISSION_VIEW_COMPANIES_LIST]);
        $PERMISSION_CREATE_COMPANY = Permission::create([Permission::PERMISSION_ID => 16, Permission::PERMISSION_NAME => Permission::PERMISSION_CREATE_COMPANY]);
        $PERMISSION_DELETE_COMPANY = Permission::create([Permission::PERMISSION_ID => 17, Permission::PERMISSION_NAME => Permission::PERMISSION_DELETE_COMPANY]);
        $PERMISSION_UPDATE_COMPANY = Permission::create([Permission::PERMISSION_ID => 18, Permission::PERMISSION_NAME => Permission::PERMISSION_UPDATE_COMPANY]);
        $PERMISSION_VIEW_LICENSES_LIST = Permission::create([Permission::PERMISSION_ID => 19, Permission::PERMISSION_NAME => Permission::PERMISSION_VIEW_LICENSES_LIST]);
        $PERMISSION_CREATE_LICENSE = Permission::create([Permission::PERMISSION_ID => 20, Permission::PERMISSION_NAME => Permission::PERMISSION_CREATE_LICENSE]);
        $PERMISSION_UPDATE_LICENSE = Permission::create([Permission::PERMISSION_ID => 21, Permission::PERMISSION_NAME => Permission::PERMISSION_UPDATE_LICENSE]);
        $PERMISSION_DELETE_LICENSE = Permission::create([Permission::PERMISSION_ID => 22, Permission::PERMISSION_NAME => Permission::PERMISSION_DELETE_LICENSE]);
        $PERMISSION_VIEW_AGREEMENTS = Permission::create([Permission::PERMISSION_ID => 23, Permission::PERMISSION_NAME => Permission::PERMISSION_VIEW_AGREEMENTS]);
        $PERMISSION_UPDATE_AGREEMENTS = Permission::create([Permission::PERMISSION_ID => 24, Permission::PERMISSION_NAME => Permission::PERMISSION_UPDATE_AGREEMENTS]);

        /* Roles */
        $ADMIN = Role::create([Role::USERS_ROLE_ID => 1, Role::USERS_ROLE_NAME => Role::ADMIN]);
        $MANAGER = Role::create([Role::USERS_ROLE_ID => 2, Role::USERS_ROLE_NAME => Role::MANAGER]);
        $INSTRUCTOR = Role::create([Role::USERS_ROLE_ID => 3, Role::USERS_ROLE_NAME => Role::INSTRUCTOR]);
        $TRAINEE = Role::create([Role::USERS_ROLE_ID => 4, Role::USERS_ROLE_NAME => Role::TRAINEE]);

        /* Attach permissions to role */
        $ADMIN->permissions()->attach(
            [
                $PERMISSION_VIEW_USERS_LIST->id, $PERMISSION_CREATE_USER_ACCOUNT->id, /*$PERMISSION_UPDATE_USER_ACCOUNT->id,
                $PERMISSION_DELETE_USER_ACCOUNT->id,*/ $PERMISSION_MANAGE_USER_ACCOUNT_LICENSE->id, /*$PERMISSION_VIEW_TRAININGS_LIST->id,
                $PERMISSION_CREATE_TRAINING->id, $PERMISSION_UPDATE_TRAINING->id, $PERMISSION_DELETE_TRAINING->id,
                $PERMISSION_VIEW_TOTAL_STATISTICS->id, $PERMISSION_VIEW_SPECIFIC_ACCOUNT_STATISTICS->id,*/ $PERMISSION_CHANGE_PASSWORD->id,
                $PERMISSION_CHANGE_USER_NAME->id, $PERMISSION_VIEW_LICENSE->id, $PERMISSION_VIEW_COMPANIES_LIST->id, $PERMISSION_CREATE_COMPANY->id,
                $PERMISSION_DELETE_COMPANY->id, $PERMISSION_UPDATE_COMPANY->id, $PERMISSION_VIEW_LICENSES_LIST->id,
                $PERMISSION_CREATE_LICENSE->id, $PERMISSION_UPDATE_LICENSE->id, $PERMISSION_DELETE_LICENSE->id,
                $PERMISSION_VIEW_AGREEMENTS->id, $PERMISSION_UPDATE_AGREEMENTS->id
            ]
        );

        $MANAGER->permissions()->attach(
            [
                $PERMISSION_VIEW_USERS_LIST->id, $PERMISSION_CREATE_USER_ACCOUNT->id, $PERMISSION_UPDATE_USER_ACCOUNT->id,
                $PERMISSION_DELETE_USER_ACCOUNT->id, $PERMISSION_MANAGE_USER_ACCOUNT_LICENSE->id,
                $PERMISSION_VIEW_TOTAL_STATISTICS->id, $PERMISSION_VIEW_SPECIFIC_ACCOUNT_STATISTICS->id, $PERMISSION_CHANGE_PASSWORD->id,
                $PERMISSION_CHANGE_USER_NAME->id, $PERMISSION_VIEW_LICENSE->id, $PERMISSION_VIEW_AGREEMENTS->id, $PERMISSION_VIEW_TRAININGS_LIST->id,
                $PERMISSION_CREATE_TRAINING->id, $PERMISSION_UPDATE_TRAINING->id, $PERMISSION_DELETE_TRAINING->id
            ]
        );

        $INSTRUCTOR->permissions()->attach(
            [
                $PERMISSION_VIEW_USERS_LIST->id, $PERMISSION_CREATE_USER_ACCOUNT->id, $PERMISSION_VIEW_TOTAL_STATISTICS->id, $PERMISSION_VIEW_SPECIFIC_ACCOUNT_STATISTICS->id, $PERMISSION_CHANGE_PASSWORD->id,
                $PERMISSION_CHANGE_USER_NAME->id, $PERMISSION_VIEW_LICENSE->id, $PERMISSION_VIEW_AGREEMENTS->id, $PERMISSION_VIEW_TRAININGS_LIST->id,
                $PERMISSION_CREATE_TRAINING->id, $PERMISSION_UPDATE_TRAINING->id, $PERMISSION_DELETE_TRAINING->id
            ]
        );

        $TRAINEE->permissions()->attach(
            [
                $PERMISSION_VIEW_TRAININGS_LIST->id, $PERMISSION_VIEW_SPECIFIC_ACCOUNT_STATISTICS->id
            ]
        );
    }

    private function make_users()
    {
        User::create([
            User::ID => 1,
            User::NAME => 'global admin',
            User::EMAIL => 'admin@admin.com',
            User::PASSWORD => bcrypt('12345678'),
            User::ACCESS_LEVEL => 1,
            User::ROLE_ID => 1,
            User::COMPANY_ID => 1,
        ]);

        User::create([
            User::ID => 2,
            User::NAME => Role::ADMIN,
            User::EMAIL => 'admin@ace.com',
            User::PASSWORD => bcrypt('12345678'),
            User::ACCESS_LEVEL => 2,
            User::ROLE_ID => 1,
            User::COMPANY_ID => 1,
        ]);

        User::create([
            User::ID => 3,
            User::NAME => Role::MANAGER,
            User::EMAIL => 'manager@ace.com',
            User::PASSWORD => bcrypt('12345678'),
            User::ACCESS_LEVEL => 3,
            User::ROLE_ID => 2,
            User::COMPANY_ID => 1,
        ]);

        User::create([
            User::ID => 4,
            User::NAME => Role::INSTRUCTOR,
            User::EMAIL => 'instructor@ace.com',
            User::PASSWORD => bcrypt('12345678'),
            User::ACCESS_LEVEL => 3,
            User::ROLE_ID => 3,
            User::COMPANY_ID => 1,
        ]);

        User::create([
            User::ID => 5,
            User::NAME => Role::TRAINEE,
            User::EMAIL => 'trainee@ace.com',
            User::PASSWORD => bcrypt('12345678'),
            User::ACCESS_LEVEL => 3,
            User::ROLE_ID => 4,
            User::COMPANY_ID => 1,
        ]);
    }
}
