<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\UserDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersApiController extends Controller
{
    public function __construct()
    {
        parent::__construct(User::TABLE_NAME);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $user = $this->getAuthUser();
        $role_name = $user->role->name;
        $params = array(User::ACTIVE => 1, User::ACCESS_LEVEL => 3);
        if ($role_name !== Role::ADMIN) {
            $params[User::COMPANY_ID] = $user->company_id;
            switch ($role_name) {
                case Role::MANAGER:
                    $exclude = array(1, 2);
                    break;

                case Role::INSTRUCTOR:
                    $exclude = array(1, 2, 3);
                    break;

                case Role::TRAINEE:
                default:
                    $exclude = array(1, 2, 3, 4);
                    break;
            }
            $users = User::where($params)
                ->whereNotIn(User::ID, [$user->id])
                ->whereNotIn(User::ROLE_ID, $exclude)
                ->with(['role', 'company', 'detail'])
                ->get();
        } else {
            $users = User::where($params)->whereNotIn(User::ROLE_ID, [1, 3, 4])->with(['role', 'company', 'detail'])->get();
        }

        if ($users) {
            return response()->json((new CommonHelper)->decryptUsersDetails($users));
        }

        return response()->json(['error' => 'Error'], 401);
    }

    /**
     * @return JsonResponse
     */
    public function getRoles()
    {
        $user = $this->getAuthUser();

        $role_name = $user->role->name;
        switch ($role_name) {
            case Role::ADMIN:
                $exclude = array(Role::INSTRUCTOR, Role::TRAINEE, Role::ADMIN);
                break;

            case Role::MANAGER:
                $exclude = array(Role::MANAGER, Role::ADMIN);
                break;

            case Role::INSTRUCTOR:
                $exclude = array(Role::INSTRUCTOR, Role::MANAGER, Role::ADMIN);
                break;

            case Role::TRAINEE:
            default:
                $exclude = array(Role::INSTRUCTOR, Role::TRAINEE, Role::ADMIN, Role::MANAGER);
                break;
        }

        return Role::where(Role::ACTIVE, 1)->whereNotIn(Role::USERS_ROLE_NAME, $exclude)->get();
    }

    /**
     * @return JsonResponse
     */
    public function getPermissions()
    {
        $user = $this->getAuthUser();

        return $user->role->permissions()->get();
    }

    /**
     * @return Event[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getEvents()
    {
        $user = $this->getAuthUser();
        if ($user->role->name === Role::ADMIN) {
            return Event::where([])->with('user')->get();
        } else {
            return Event::whereHas('user', function ($query) use ($user) {
                return $query->where(User::TABLE_NAME.'.'.User::COMPANY_ID, '=', $user->company->id);
            })->get();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            User::NAME => 'required|max:100',
            User::PASSWORD => 'required|max:50',
            User::ROLE_ID => 'required', User::COMPANY_ID => 'required',
        ]);

        $fields = [
            User::NAME, User::PASSWORD, User::ROLE_ID, User::COMPANY_ID,
            UserDetail::FIRST_NAME, UserDetail::LAST_NAME, UserDetail::TITLE,
        ];
        $data = $request->only($fields);
        $data[User::PASSWORD] = bcrypt($data[User::PASSWORD]);

        $checkUser = User::where([User::NAME => $data[User::NAME]])->first();
        if ($checkUser) {
            return response()->json(['error' => 'panel.errors.current_account_name_is_exist'], 400);
        }

        $user = User::create($data);
        if ($user && isset($user->id)) {
            if (isset($data[UserDetail::FIRST_NAME]) || isset($data[UserDetail::LAST_NAME]) || isset($data[UserDetail::TITLE])) {
                $detailData = array(
                    UserDetail::USER_ID => $user->id,
                    UserDetail::FIRST_NAME => isset($data[UserDetail::FIRST_NAME]) ? encrypt($data[UserDetail::FIRST_NAME]) : '',
                    UserDetail::LAST_NAME => isset($data[UserDetail::LAST_NAME]) ? encrypt($data[UserDetail::LAST_NAME]) : '',
                    UserDetail::TITLE => isset($data[UserDetail::TITLE]) ? encrypt($data[UserDetail::TITLE]) : '',
                );
                UserDetail::create($detailData);
            }

            parent::createEvent(Event::EVENT_CREATE_USER, $user->name, $user->id);

            return response()->json($this->getUser($user->id));
        }

        return response()->json(false);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $userAuth = $this->getAuthUser();
        $user = User::findOrFail($id);
        if ($userAuth->company_id !== $user->company_id) {
            return response()->json(['error' => 'Wrong company'], 400);
        }

        $fields = [
            User::COMPANY_ID, UserDetail::FIRST_NAME, UserDetail::LAST_NAME, UserDetail::TITLE,
        ];
        $data = $request->only($fields);
        $user->update($data);

        if (isset($data[UserDetail::FIRST_NAME]) || isset($data[UserDetail::LAST_NAME]) || isset($data[UserDetail::TITLE])) {
            $userDetail = UserDetail::where([UserDetail::USER_ID => $user->id])->first();
            $detailData = array(
                UserDetail::USER_ID => $user->id,
                UserDetail::FIRST_NAME => isset($data[UserDetail::FIRST_NAME]) ? encrypt($data[UserDetail::FIRST_NAME]) : '',
                UserDetail::LAST_NAME => isset($data[UserDetail::LAST_NAME]) ? encrypt($data[UserDetail::LAST_NAME]) : '',
                UserDetail::TITLE => isset($data[UserDetail::TITLE]) ? encrypt($data[UserDetail::TITLE]) : '',
            );
            if (!$userDetail) {
                UserDetail::create($detailData);
            } else {
                $userDetail->update($detailData);
            }
        }

        parent::createEvent(Event::EVENT_UPDATE_USER, $user->name, $user->id);

        return response()->json($this->getUser($user->id));
    }

    /**
     * @param Request $request
     * @param $id
     * @return bool|JsonResponse
     */
    public function changePassword(Request $request, $id)
    {
        $data = $request->all();
        $userById = User::findOrFail($id);
        $current_password = $userById->password;
        if (!isset($data['profile'])) {
            $user = $this->getAuthUser();
            $current_password = $user->password;
        }
        if (!isset($data['current_password']) || !isset($data['new_password'])) {
            return false;
        }

        if (!Hash::check($data['current_password'], $current_password)) {
            return response()->json(['error' => 'panel.errors.wrong_current_password'], 400);
        }

        $userById->update(['password' => bcrypt($data['new_password'])]);

        parent::createEvent(Event::EVENT_CHANGE_PASSWORD, $userById->name, $userById->id);

        return response()->json(['success' => 'panel.success.password_changed'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function delete($id)
    {
        $userAuth = $this->getAuthUser();
        $userDetail = UserDetail::where([UserDetail::USER_ID => $id]);
        $userDetail->delete();
        $user = User::findOrFail($id);
        if ($userAuth->company_id !== $user->company_id) {
            return response()->json(['error' => 'Wrong company'], 400);
        } else {
            if ($user->role->name === Role::ADMIN) {
                return response()->json(['error' => 'error'], 400);
            }
            $user->delete();
            parent::createEvent(Event::EVENT_DELETE_USER, $user->name, $user->id);

            return 204;
        }
    }
}
