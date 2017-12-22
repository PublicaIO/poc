<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Auth;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Save generated wallet for user
     *
     * @param Request $request
     * @return array
     */
    public function createWallet(Request $request)
    {
        $validatedData = $request->validate([
            'wallet_address' => 'required',
        ]);

        $user = User::findOrFail(Auth::id());

        /**
         * Update user data
         */
        $user->wallet_address = $validatedData['wallet_address'];
        $user->save();

        $response = ['success' => true];

        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required',
            'username' => 'required',
            'email'    => 'required|email',
        ]);

        $user = User::findOrFail(Auth::id());
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->wallet_address = $request->wallet_address;
        $user->save();

        /**
         * If username is changed the redirect
         */
        if ($user->username != Auth::user()->username) {
            $response['redirect'] = url("@{$user->username}");
            return $response;
        }

        return $user;
    }
}
