<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Blog/Blog', []);
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog): Response
    {
        views($blog)->record();

        return Inertia::render('Blog/Blog', [
            'blog' => $blog->load('user', 'bands'),
            'url' => url()->current(),
        ]);
    }
}
