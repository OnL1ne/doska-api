<?php namespace App\Http\Controllers;

use App\Event;
use App\Mail\ResetPasswordMail;
use App\User;
use App\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @param JWTAuth $JWTAuth
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request, JWTAuth $JWTAuth)
    {
        $field = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $request->merge([$field => $request->input('login')]);
        $credentials = $request->only([$field, 'password']);
        auth()->factory()->setTTL(4080);
        if (!auth()->attempt($credentials)) {
            return response()->json(['error' => 'panel.errors.unauthorized_login'], 401);
        }
        $user = auth()->user();
        $customClaims = $user->toArray();
        $customClaims['role_name'] = $user->role ? $user->role->name : '';
        $token = $JWTAuth->customClaims($customClaims)->fromUser($user);
        parent::createEvent(Event::EVENT_LOGIN_USER, $user->name, $user->id);

        return $this->respondWithToken($token);
    }

    /**
     * User registration
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registration(Request $request)
    {
        $request->validate([
            User::NAME => 'required|max:100',
            User::EMAIL => 'required|max:100',
            User::PASSWORD => 'required|max:50',
        ]);

        $fields = [User::NAME, User::EMAIL, User::PASSWORD];
        $credentials = $request->only($fields);
        $checkUser = User::where([User::NAME => $credentials['name']])->first();
        if ($checkUser) {
            return response()->json(['error' => 'panel.errors.current_account_name_is_exist'], 400);
        }
        $checkUser = User::where([User::EMAIL => $credentials['email']])->first();
        if ($checkUser) {
            return response()->json(['error' => 'panel.errors.current_account_email_is_exist'], 400);
        }

        $user = User::create([
            User::NAME => $credentials['name'],
            User::EMAIL => $credentials['email'],
            User::PASSWORD => bcrypt($credentials['password']),
        ]);

        if (!$user) {
            return response()->json(['error' => 'Error!']);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return response()->json(['success' => 'panel.success.user_registered']);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $user = parent::getAuthUser();
        $user->role_name = $user->role->name;
        if ($user->detail) {
            $user->detail->first_name = decrypt($user->detail->first_name);
            $user->detail->last_name = decrypt($user->detail->last_name);
            $user->detail->title = decrypt($user->detail->title);
        }
        return response()->json($user);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $email = $request->email;
        if (!$email) return response()->json(['error' => 'panel.errors.email_not_exist'], 400);

        $checkUser = User::where([User::EMAIL => $email])->first();
        if ($checkUser) {
            $token = $this->saveToken(Str::random(60), $email);
            Mail::to($email)->send(new ResetPasswordMail($token));
            return response()->json(['success' => 'panel.success.email_has_been_sent'], 200);
        } else {
            return response()->json(['error' => 'panel.errors.reset_email_is_not_exist'], 400);
        }
    }

    private function saveToken($token, $email)
    {
        if (!$token || !$email) return false;

        $checkToken = DB::table('password_resets')->where(['email' => $email])->first();
        if ($checkToken) return $checkToken->token;

        $row = DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
        return $row->token;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required',
            'repeat_password' => 'required',
            'email' => 'required',
            'token' => 'required',
        ]);

        $fields = ['new_password', 'repeat_password', 'email', 'token'];
        $credentials = $request->only($fields);

        if ($credentials['new_password'] !== $credentials['repeat_password']) {
            return response()->json(['error' => 'panel.errors.password_confirm_pattern'], 400);
        }

        $checkToken = DB::table('password_resets')->where(
            ['email' => $credentials['email'], 'token' => $credentials['token']]
        )->first();
        if (!$checkToken) {
            return response()->json(['error' => 'panel.errors.wrong_email'], 400);
        }

        $user = User::where([User::EMAIL => $credentials['email']])->first();
        if (!$user) {
            return response()->json(['error' => 'panel.errors.user_not_exist'], 400);
        }

        $user->update([User::PASSWORD => bcrypt($credentials['new_password'])]);
        DB::table('password_resets')->where(
            ['email' => $credentials['email'], 'token' => $credentials['token']]
        )->delete();

        return response()->json(['success' => 'panel.success.password_changed'], 200);
    }

    public function updateProfile(Request $request)
    {
        $fields = [
            User::NAME, User::EMAIL, UserDetail::FIRST_NAME, UserDetail::LAST_NAME, UserDetail::TITLE,
        ];
        $data = $request->only($fields);
        unset($data['password']);

        $userAuth = $this->getAuthUser();
        $user = User::findOrFail($userAuth->id);
        if (isset($data[User::NAME])) {
            $checkUser = User::where([User::NAME => $data[User::NAME]])->whereNotIn(User::ID, [$user->id])->first();
            if ($checkUser) {
                return response()->json(['error' => 'panel.errors.current_account_name_is_exist'], 400);
            }
            if ($user->name) unset($data[User::NAME]);
        }
        if (isset($data[User::EMAIL])) {
            $checkUser = User::where([User::EMAIL => $data[User::EMAIL]])->whereNotIn(User::ID, [$user->id])->first();
            if ($checkUser) {
                return response()->json(['error' => 'panel.errors.current_account_email_is_exist'], 400);
            }
            if ($user->email) unset($data[User::EMAIL]);
        }

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

        parent::createEvent(Event::EVENT_UPDATE_PROFILE, $user->name, $user->id);

        return response()->json($this->getUser($user->id));
    }
}
