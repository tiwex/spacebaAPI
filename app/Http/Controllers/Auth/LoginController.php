<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
{
    if ($this->attemptLogin($request)) {
        $user = $this->guard()->user();

        //$user->generateToken();

        return response()->json([
            'data' => $user->toArray(),
        ]);
    }
    else return response()->json(false,200);

   // return $this->sendFailedLoginResponse($request);
}
  public function checkcredential(Request $request)

{
    $email=$request->input('email');
    $user=User::where('email', $email)->first();

    if (empty($user)) $password = false;
    else $password=$user->password;

    $password=Hash::check($request->input('password'),$password);

    if (!empty($user) && $password==true)
    {
        return response()->json(['data' => $user->toArray(),]);
    }

    
    else return response()->json(false,200);

   //return $this->sendFailedLoginResponse($request);
}
}
