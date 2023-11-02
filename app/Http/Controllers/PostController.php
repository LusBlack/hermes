<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function showCreatePost() {
        return view('create-post');
    }
    public function storeNewPost(){
        return 'hwfar';
    }


}
