<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use App\Http\Requests;


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
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function login(Request $request)
    {
        $credenticals = [
            'email' => $request->json('email'),
            'password' => $request->json('password'),
        ];


        if (Auth::attempt($credenticals)) 
        {
            $user = Auth::guard($this->getGuard())->user();
            $user->generateToken();

            return response()->json(
                $user->toArray()
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json('login failed', 400);
        }
        else {
            return $this->sendFailedLoginResponse($request);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();
    
        if ($user) {
            $user->api_token = null;
            $user->save();
        }
    
        return response()->json('User logged out.', 200);
    }

    public function register(Request $request)
    {
        $credenticals = [
            'name' => $request->json('name'),
            'email' => $request->json('email'),
            'password' => $request->json('password'),
        ];

        // Here the request is validated. The validator method is located
        // inside the RegisterController, and makes sure the name, email
        // password and password_confirmation fields are required.
        $this->validator($credenticals);

        // A Registered event is created and will trigger any relevant
        // observers, such as sending a confirmation email or any 
        // code that needs to be run as soon as the user is created.

        // $user = $this->create($request->all());
        $user = $this->create($credenticals);
        // event(new Registered($user = $this->create($request->all())));

        // // After the user is created, he's logged in.
        auth()->login($user);

        // And finally this is the hook that we want. If there is no
        // registered() method or it returns null, redirect him to
        // some other URL. In our case, we just need to implement
        // that method to return the correct response.
        return response()->json(
            $user->toArray()
        );
    }

}

