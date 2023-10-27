<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function homepage() {
        //return '<h1>Homepage</h1><a href="/about">view the about page</a>';
         return view("homepage");
    }
    public function post() {    
       return view('single-post');
    
    }
    //
}
