<?php

namespace App\Http\Controllers;

use App\AuthorInfo;
use App\Book;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorsController extends Controller
{
    /**
     * Show author profile page
     *
     * @param int
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($id)
    {
        $user = User::findOrFail($id);

        //do not display readers profiles publicly, only author profile is public
        if (Auth::id() != $user->id && !$user->hasRole(Role::ROLE_AUTHOR)) {
            return abort('404');
        }

        return view('authors/view', [
            'author' => $user,
            'books' => Book::where('user_id', $id)->whereNotIn('status', [Book::STATUS_CANCELED])->orderBy('status', 'ASC')->get(),
            'purchasedBooks' => $user->purchasedBooks,
            'submitUrl' => route('authors.save'),
            'photo' => $user->avatar,
        ]);
    }

    /**
     * Save authors profile info
     * @param Request $request
     *
     * @return array
     */
    public function save(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|max:255|email|unique:users,email,' . Auth::id(),
            'username' => 'required|max:255|unique:users,username,' . Auth::id(),
            'about' => 'required',
            'kindle_store_link' => 'required',
        ]);

        $author = User::findOrFail(Auth::id());
        $authorInfo = AuthorInfo::where('user_id', Auth::id())->first();

        /**
         * Update user data
         */
        $author->email = $validatedData['email'];
        $author->username = $validatedData['username'];
        $author->save();

        /**
         * Update author info
         */
        if (!$authorInfo) {
            $authorInfo = new AuthorInfo();
            $authorInfo->user_id = Auth::id();
        }
        $authorInfo->about = $validatedData['about'];
        $authorInfo->kindle_store_link = $validatedData['kindle_store_link'];
        $authorInfo->save();

        $response = ['success' => true];

        /**
         * If username is changed the redirect
         */
        if ($validatedData['username'] != Auth::user()->username) {
            $response['redirect'] = url("@{$validatedData['username']}");
        }

        return $response;
    }

    /**
     * Authors list action
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list()
    {
        $authors = User::withRole(Role::ROLE_AUTHOR)->get();

        return view('authors/list', ['authors' => $authors]);

    }
}
