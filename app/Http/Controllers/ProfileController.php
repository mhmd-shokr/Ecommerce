<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function showCompleteForm()
{
    return view('auth.complete-profile');
}

public function saveCompleteForm(Request $request)
{
    $request->validate([
        'mobile' => 'required|numeric|digits:11|unique:users,mobile',
    ],
    [
        'mobile.required' => 'Mobile number is required.',
        'mobile.numeric' => 'Mobile number must contain only digits.',
        'mobile.digits' => 'Mobile number must be exactly 10 digits.',
        'mobile.unique' => 'This mobile number is already in use. Please choose another one.',
    ]
    );
    /**
     * @var /$user/App\Models\User
     */
    $user = Auth::user();
    $user->mobile = $request->mobile;
    $user->save();

    return redirect()->route('home.index')->with('success', 'Profile completed successfully.');
}

}
