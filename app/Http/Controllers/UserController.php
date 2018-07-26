<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(User $user)
    {
        return view("profiles.show", compact("user"));
    }

    public function edit(User $user)
    {
        return view("profiles.edit", compact("user"));
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name'          => 'required|string',
            'last_name'     => 'required|string',
            'email'         => "required|email|unique:users,email,{$user->id}",
            'password'      => 'confirmed',
            'job'           => 'required|string'
        ]);

        $user->name             = $request->name;
        $user->last_name        = $request->last_name;
        $user->email            = $request->email;
        $user->password         = ( !empty($request->password) ) ? Hash::make($request->password) : $user->password;
        $user->job              = $request->job;

        $user->save();

        return redirect()->route("profile", $user);

    }
}
