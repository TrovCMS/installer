<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function show(Post $post)
    {
        abort_unless($post->status == 'published' || auth()->user(), 404);

        return view('post', [
            'post' => $post,
        ]);
    }
}
