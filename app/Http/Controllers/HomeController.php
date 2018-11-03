<?php

namespace App\Http\Controllers;

use App\Category;
use App\Post;
use App\Tag;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Main page action
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $posts = Post::where('status', 0)->paginate(5);
        return view('pages.index', [
            'posts' => $posts
        ]);
    }

    /**
     * Single post action
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($slug)
    {
        $post = Post::where('status', 0)->where('slug', $slug)->firstOrFail();
        return view('pages.show', [
            'post' => $post
        ]);
    }

    public function tag($slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        $posts = $tag->posts()->where('status', 0)->paginate(6);
        return view('pages.list', [
            'posts' => $posts
        ]);
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = $category->posts()->where('status', 0)->paginate(6);
        return view('pages.list', [
            'posts' => $posts
        ]);
    }
}
