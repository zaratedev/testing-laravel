<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class RegistrationConfirmController extends Controller
{
    public function index(Request $request)
    {
        try {
            User::where('confirmation_token', request('token'))
                ->firstOrFail()
                ->confirm();
        }
        catch(\Exception $e)
        {
            return redirect('/')->with('success', 'Token Invalid');
        }

        return redirect('/')->with('success', 'Your account is now activated!');
    }
}
