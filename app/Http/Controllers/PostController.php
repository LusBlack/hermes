<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{


    public function viewSinglePost(Post $post) {
     $post['body'] =  strip_tags(Str::markdown($post->body), '<p><ul<ol><li><strong><e,><h3><br>');
        return view('single-post', ['post'=> $post]);
    }

 public function storeNewPost(Request $request){
      $incomingFields = $request->validate([
        'title'=> 'required',
        'body'=> 'required'
      ]);
      //sanitizing user input
      $incomingFields['title'] = strip_tags($incomingFields['title']);
      $incomingFields['body'] = strip_tags($incomingFields['body']);
      $incomingFields['user_id'] = auth()->user()->id;


      $newPost= Post::create($incomingFields);

      return redirect("/post/{$newPost->id}")->with("nice","New post created");
     }


    public function showCreatePost() {
        return view('create-post');
    }





}
