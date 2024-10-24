<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Mail\NewPostEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\sendNewPostEmail;
use Illuminate\Support\Facades\Mail;

class PostController extends Controller
{

    public function search($term) {
       $posts = Post::Search($term)->get();
        $posts->load('user:id,username,avatar');
            return $posts;
        }


    public function actuallyUpdate(Post $post, Request $request) {

        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        if( auth()->user()->cannot('update', $post)) {
            return back()->with('failure', 'You cannot update this post');
        }

        $post->update($incomingFields);

        return back()->with('success', 'Post successfully updated');
    }

    public function showEditForm(Post $post) {
        return view('edit-post', ['post' => $post]);
    }

    public function delete(Post $post) {
        $post->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success', 'Post successfully deleted.');
    }

    public function deletePostAPI(Post $post) {
        $post->delete();
        return 'true';
     }



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

        dispatch(new sendNewPostEmail(['sendTo'=>auth()->user()->email, 'name' => auth()->user()->username, 'title'=>$newPost->title]));

        return redirect("/post/{$newPost->id}")->with("nice","New post created");
    }

    public function storePostAPI(Request $request){
        $incomingFields = $request->validate([
            'title'=> 'required',
            'body'=> 'required'
        ]);
        //sanitizing user input
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = auth()->user()->id;


        $newPost= Post::create($incomingFields);

        dispatch(new sendNewPostEmail(['sendTo'=>auth()->user()->email, 'name' => auth()->user()->username, 'title'=>$newPost->title]));

        return $newPost->id;
    }

    public function showCreatePost() {
        return view('create-post');
    }

}
