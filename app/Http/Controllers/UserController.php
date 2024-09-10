<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Laravel\Facades\Image;


class UserController extends Controller
{
    public function storeAvatar(Request $request) {
       $request->validate([
        'avatar' => 'required|image|max:3000'
       ]);

       $user = auth()->user();

       $filename = $user->id . '_' . uniqid() . '.jpg';

     $imgData = Image::read($request->file('avatar'));
     $imgData->resize(120,120);
     $jpegEncoder = new JpegEncoder();
     $encodedImage = $imgData->encode($jpegEncoder);


     Storage::put('public/avatars/' . $filename, (string) $encodedImage);

     $oldAvatar = $user->avatar;


     $user->avatar = $filename;
     $user->save();

     if($oldAvatar != "/fallback-avatar.jpg") {
        Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
     }

     return back()->with('success', 'Congrats, avatar changed');

    }

    //view for changing avatar
    public function showAvatarForm() {
        return view('avatar-form');
    }

    private function getSharedData($user) {
        $currentlyFollowing = 0;

        if(auth()->check()) {
            $currentlyFollowing = Follow::where(([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]]))->count();
        }

        View::share('sharedData', ['currentlyFollowing' => $currentlyFollowing,
        'avatar' => $user->avatar,
         'username' => $user->username,
         'postCount' => $user->posts()->count()]);
    }

    public function profileFollowing(User $user) {
        $this->getSharedData($user);
        return view('profile-following',
        ['posts' => $user->posts()->latest()->get()]);
    }

    public function profileFollowers(User $user) {
        $this->getSharedData($user);
        return view('profile-followers',
        [ 'posts' => $user->posts()->latest()->get()]);
    }

    //user's profile
    public function profile(User $user) {
        $this->getSharedData($user);
        return view('profile-post',
        ['posts' => $user->posts()->latest()->get()]);
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

    //login method
    public function login(Request $request) {
        $incomingFields = $request->validate([
          'loginusername' => 'required',
          'loginpassword' => 'required'
          ]);

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
