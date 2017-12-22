<?php

namespace App\Http\Controllers\Auth;

use App\Role;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

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
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * RegisterController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display registration form
     *
     * @param $role
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm($role = 'reader')
    {
        switch ($role) {
            case 'reader':
            default:
                $role = 'reader';
                break;

            case 'author':
                $role = 'author';
                break;
        }

        return view('auth.register', compact('role'));
    }

    /**
     * Attempt user registration
     * @param $role
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function register($role, Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($role, $request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    /**
     * Validate request params
     *
     * @param array $data
     * @return mixed
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|string|max:255',
            'surname'  => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * create new user and assign role
     *
     * @param $role
     * @param array $data
     * @return mixed
     */
    protected function create($role, array $data)
    {
        $role = Role::where('name', $role)->firstorFail();
        $user = User::create([
            'name'            => $data['name'],
            'surname'         => $data['surname'],
            'email'           => $data['email'],
            'password'        => bcrypt($data['password']),
            'wallet_password' => str_random(16)
        ]);

        $user->attachRole($role->id);
        $user->wallet_password = str_random(16);
        $user->save();

        return $user;
    }

    /**
     * Redirect to home after successful registration
     *
     * @param Request $request
     * @param $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function registered(Request $request, $user)
    {
        return redirect('/');
    }
}
