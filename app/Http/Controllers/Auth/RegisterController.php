<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Mail\HoraSignup;
use App\Mail\Welcome;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
  protected function profile(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
     public function store(Request $request)
    {
        $this->validator($request->all())->validate();
        $array=array("name"=>$request->input('name'),"email"=>$request->input('email'),"phone"=>$request->input('phone'),"password"=>$request->input('password'),"is_subscriber"=>$request->input('is_member'));
       //$user = User::create($request->all());
        $user = User::create($array);
       $user->password =Hash::make($user->password);
      $user->save();
      $member=$request->input('is_member');

      $user1=0;
       Mail::to($user)->send(new Welcome);
    /*if  ($member == 1)
   {
       $user1 = User::findorfail($user->id);

         $user1->is_member = $member;
        
         $user1->save();
      

        // Ship order...

       
    }*/
       
    

       //send welcome and  verficationn email if i can verify email 
        return response()
        ->json($user, 201);
    }
}
