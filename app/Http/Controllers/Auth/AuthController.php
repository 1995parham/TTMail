<?php

namespace TMail\Http\Controllers\Auth;

use TMail\User;
use Validator;
use TMail\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/inbox';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data user input
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'g-recaptcha-response' => 'required|recaptcha',
            'avatar' => 'image',
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data validated user data
     *
     * @return User
     */
    protected function create(array $data)
    {
        $u = User::create(
            [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            ]
        );

        if (isset($data['avatar'])) {
            $f = $data['avatar'];
            $e = $f->getClientOriginalExtension();
            $f->move(storage_path('app/public/avatars'), md5($data['email']).'.'.$e);
            $avatar = asset('storage/avatars/'.md5($data['email']).'.'.$e);
            $u->avatar = $avatar;
        }

        return $u;
    }
}
