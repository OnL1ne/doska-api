<?php

namespace App\Http\Controllers;

use App\Event;
use App\License;
use App\Permission;
use App\Training;
use App\Company;
use App\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct($controller = null)
    {
        $this->middleware('auth', ['except' => ['login', 'registration', 'forgotPassword', 'resetPassword', 'verify']]);
        $this->checkPermissions($controller);
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|\Illuminate\Http\JsonResponse|null
     */
    public function getAuthUser()
    {
        $user = auth()->user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);
        if (!$user->role) return response()->json(['error' => 'User without role'], 400);
        if (!$user->company_id) return response()->json(['error' => 'User without company'], 400);
        return $user;
    }

    /**
     * @param $controller
     * method for checking controller permissions
     */
    private function checkPermissions($controller)
    {
        if ($controller) {
            switch ($controller) {
                /* Manage training */
                case Training::TABLE_NAME:
                    $this->trainings();
                    break;
                /* Manage companies */
                case Company::TABLE_NAME:
                    $this->companies();
                    break;
                /* Manage users */
                case User::TABLE_NAME:
                    $this->users();
                    break;
                /* Manage licenses */
                case License::TABLE_NAME:
                    $this->licenses();
                    break;
            }
        }
    }

    /**
     * Check companies controller permissions
     */
    private function companies()
    {
        $this->middleware('permission:' . Permission::PERMISSION_VIEW_COMPANIES_LIST)->only(['index', 'show']);
        $this->middleware('permission:' . Permission::PERMISSION_CREATE_COMPANY)->only(['create']);
        $this->middleware('permission:' . Permission::PERMISSION_UPDATE_COMPANY)->only(['update']);
        $this->middleware('permission:' . Permission::PERMISSION_DELETE_COMPANY)->only(['delete']);
    }

    /**
     * Check trainings controller permissions
     */
    private function trainings()
    {
        $this->middleware('permission:' . Permission::PERMISSION_VIEW_TRAININGS_LIST)->only(['index', 'show']);
        $this->middleware('permission:' . Permission::PERMISSION_CREATE_TRAINING)->only(['create']);
        $this->middleware('permission:' . Permission::PERMISSION_UPDATE_TRAINING)->only(['update']);
        $this->middleware('permission:' . Permission::PERMISSION_DELETE_TRAINING)->only(['delete']);
    }

    /**
     * Check companies controller permissions
     */
    private function users()
    {
        $this->middleware('permission:' . Permission::PERMISSION_VIEW_USERS_LIST)->only(['index', 'getUser']);
        $this->middleware('permission:' . Permission::PERMISSION_CREATE_USER_ACCOUNT)->only(['create']);
        $this->middleware('permission:' . Permission::PERMISSION_UPDATE_USER_ACCOUNT)->only(['update']);
        $this->middleware('permission:' . Permission::PERMISSION_DELETE_USER_ACCOUNT)->only(['delete']);
        //$this->middleware('permission:' . Permission::PERMISSION_CHANGE_PASSWORD)->only(['changePassword']);
    }

    /**
     * Check licenses controller permissions
     */
    private function licenses()
    {
        $this->middleware('permission:' . Permission::PERMISSION_VIEW_LICENSES_LIST)->only(['index', 'show']);
        $this->middleware('permission:' . Permission::PERMISSION_CREATE_LICENSE)->only(['create']);
        $this->middleware('permission:' . Permission::PERMISSION_UPDATE_LICENSE)->only(['update']);
        $this->middleware('permission:' . Permission::PERMISSION_DELETE_LICENSE)->only(['delete']);
    }

    /**
     * Audit function
     * creating events list in database and log file - audit.log
     *
     * @param $eventTitle
     * @param null $nodeTitle
     * @param null $nodeId
     * @return bool
     */
    public function createEvent($eventTitle, $nodeTitle=null, $nodeId=null) {
        if (!$eventTitle) return false;
        $user = self::getAuthUser();
        $data = [
            Event::USER_ID => $user->id,
            Event::TITLE => $eventTitle,
            Event::DESCRIPTION => $eventTitle.' - '.$nodeTitle.' (id: '.$nodeId.')'
        ];
        Event::create($data);

        Log::channel('customlog')->info($data);
    }

    /**
     * @param $id
     * @return bool
     */
    public function getUser($id) {
        if (!$id) return false;
        $getUser = User::where([User::ID => $id])->with(['role', 'company', 'detail'])->first();
        if ($getUser->detail) {
            $getUser->detail->first_name = decrypt($getUser->detail->first_name);
            $getUser->detail->last_name = decrypt($getUser->detail->last_name);
            $getUser->detail->title = decrypt($getUser->detail->title);
        }

        return $getUser;
    }
}
