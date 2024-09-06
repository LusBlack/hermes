<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function createFollow(User $user) {
        // you cannot follow yourself
        if($user->id == auth()->user()->id) {
            return back()->with('failure', 'You cannot follow yourself');
        }

        // cannot follow someone you're already following
        $existCheck =Follow::where([])->count();

        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id;
        $newFollow->followeduser = $user->id;
        $newFollow->save();

    }

    public function removeFollow() {

    }
}
