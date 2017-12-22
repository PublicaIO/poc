<?php

namespace App\Http\Controllers\Auth;

use App\Book;
use App\Http\Controllers\Controller;
use App\Role;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * AuthorLoginController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @param string $role
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm($role = 'author')
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

        // Used to edirect to book view after login
        $book = \request('book', '');

        return view('auth.login', compact('role', 'book'));
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $isAuthor = $this->guard()->user()->hasRole(Role::ROLE_AUTHOR);
        $this->guard()->logout();

        $request->session()->invalidate();

        if ($isAuthor) {
            return redirect('/login/author');
        }

        return redirect('/login/reader');
    }

    /**
     * Will return user to the book view if any.
     *
     * @param Request $request
     * @param $user
     *
     * @return bool|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function authenticated(Request $request, $user)
    {
        $bookId = $request->get('book', false);

        if ($bookId) {
            $book = Book::find($bookId);
            return redirect('/' . $book->url);
        }

        return false;
    }
}
