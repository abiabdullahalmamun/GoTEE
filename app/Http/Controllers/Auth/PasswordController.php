<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function create()
    {
        return view('auth.change-pass');
    }


    public function update(Request $request): RedirectResponse
    {
         // dd($request->all());
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);
        // dd($validated) ; 

        if($validated){
             $update= $request->user()->update([
                    'password' => Hash::make($validated['password']),
                ]);

             if($update){
                 return back()->with('success', 'Password updated successfully');
             }
             else{
                return back()->with('error', 'Current password is incorrect');
             }
        }
        else{
             return back()->with('error', 'Current password is incorrect');
        }
       
// OR on failure:


        // return back()->with('status', 'password-updated');
    }
}
