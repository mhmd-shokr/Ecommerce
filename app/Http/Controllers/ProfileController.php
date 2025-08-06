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
        'mobile' => 'required|numeric|min:10',
    ]);
    /**
     * @var /$user/App\Models\User
     */
    $user = Auth::user();
    $user->mobile = $request->mobile;
    $user->save();

    return redirect()->route('home.index')->with('success', 'Profile completed successfully.');
}

}
