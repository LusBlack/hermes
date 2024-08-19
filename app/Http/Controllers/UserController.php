<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function storeAvatar(Request $request) {
        $request->file('avatar')->store('public/avatars');
        return ''
    }

    public function showAvatarForm() {
        return view('avatar-form');
    }

    public function profile(User $user) {

        return view('profile-post',
        ['username' => $user->username,
         'posts' => $user->posts()->latest()->get(),
         'postCount' => $user->posts()->count()
    ]);

    }

    public function logout() {
        auth()->logout();
        return redirect('/')->with('nice', "piss-off");
     }
    public function showCorrectHomePage() {
        if (auth()->check()) {
            return view('homepage-feed');
        } else {
            return view('homepage');
        }

    }

    public function login(Request $request) {
        $incomingFields = $request->validate([
          'loginusername' => 'required',
          'loginpassword' => 'required'
          ]);

          //login method
          if (auth()->attempt(['username'=> $incomingFields['loginusername'],'password'=> $incomingFields['loginpassword']])) {
            $request->session()->regenerate();
            return redirect('/')->with('nice', "you're in.");

          } else {
            return redirect('/')->with('failure', 'Invalid login.');
          }

    }
    public function register(Request $request) {
        $incomingFields = $request->validate([
            'username' => ['required','min:3',
            'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password'=> ['required', 'min:8', 'confirmed']

        ]);
        $incomingFields['password'] = bcrypt($incomingFields['password']);
       $user = User::create($incomingFields) ;
       //we log the new user in instead of redirecting to home
        auth()->login($user);
        return redirect('/')->with('nice', 'Happy Hunting');
    }
}
