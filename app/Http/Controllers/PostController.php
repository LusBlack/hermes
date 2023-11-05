<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function showCreatePost() {
        return view('create-post');
    }
    public function storeNewPost(Request $request){
      $incomingFields = $request->validate([
        'title'=> 'required',
        'body'=> 'required'
      ]);

      $incomingFields['title'] = strip_tags($incomingFields['title']);
      $incomingFields['body'] = strip_tags($incomingFields['body']);
      $incomingFields['user_id'] = auth()->user()->id;




    }


}
