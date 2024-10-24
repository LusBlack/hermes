<?php

namespace App\Http\Controllers;

use Response;
use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use App\Events\ExampleEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
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

    public function changeUsername(Request $request) {
      $request->validate(['username' => ['required','min:3',
        'max:20', Rule::unique('users', 'username')]]);
     $user= Auth::user();
     if($user->username !== $request->username) {
        $user->username = $request->username;

        if($user->save()) {
            return redirect("/profile/{$user->username}")->with('success', 'username changed');
        } else {
            //diagnosing for save failure
            \Log::error('Failed to update username for user ID: ' . $user->id);
        }
    } else {
        return redirect()->back()->with('error', 'New username is the same as the current one.');
    }
     return redirect()->back()->with('error', 'failed to update username');
}


    public function ShowChangeUsername() {
        $user= Auth::user();
        return view('changeUsername', ['user' => $user]);
    }


    private function getSharedData($user) {
        $currentlyFollowing = 0;

        if(auth()->check()) {
            $currentlyFollowing = Follow::where(([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]]))->count();
        }

        View::share('sharedData', [
        'currentlyFollowing' => $currentlyFollowing,
        'avatar' => $user->avatar,
         'username' => $user->username,
         'postCount' => $user->posts()->count(),
         'followerCount' => $user->followers()->count(),
         'followingCount' => $user->following()->count()
        ]);
    }

    public function profileFollowing(User $user) {
        $this->getSharedData($user);
        return view('profile-following',
        ['following' => $user->following()->latest()->get()]);
    }

    public function profileFollowers(User $user) {
        $this->getSharedData($user);
        return view('profile-followers',
        [ 'followers' => $user->followers()->latest()->get()]);
    }

     public function profile(User $user) {
        $this->getSharedData($user);
        return view('profile-post',
        ['posts' => $user->posts()->latest()->get()]);
    }

    public function profileFollowingRaw(User $user)  {
         return response()->json(['theHTML' => view('profile-following-only', ['following'=>$user->following()->latest()->get()])->render(), 'docTitle'=> $user->username . "follows these users"]);
    }

    public function profileFollowersRaw(User $user) {
       return response()->json(['theHTML' => view('profile-followers-only', ['followers'=>$user->followers()->latest()->get()])->render(), 'docTitle'=> $user->username . "'s followers"]);
    }
    public function profileRaw(User $user) {
        return response()->json(['theHTML' => view('profile-posts-only', ['posts'=>$user->posts()->latest()->get()])->render(), 'docTitle'=> $user->username]);
    }

    public function logout() {
        auth()->logout();
        return redirect('/')->with('nice', "piss-off");
     }


    public function showCorrectHomePage() {
        if (auth()->check()) {
            return view('homepage-feed', ['posts'=> auth()->user()->feedPosts()->latest()->paginate(4)]);
        } else {
            $postCount = Cache::remember('postCount', 20, function(){
                return Post::count();
            });
            return view('homepage', ['postCount'=> $postCount]);
        }

    }

    public function loginAPI(Request $request) {
        $incomingFields = $request->validate([
            'username' => 'required',
            'password' => 'required'
            ]);

            if(auth()->attempt($incomingFields)) {
                $user = User::where('username', $incomingFields['username'])->first();
                $token =$user->createToken('HermesToken')->plainTextToken;
                return $token;
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
