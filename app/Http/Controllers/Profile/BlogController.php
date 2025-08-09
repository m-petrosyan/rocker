<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\BlogCreateRequest;
use App\Http\Requests\Blog\BlogUpdateRequest;
use App\Models\Blog;
use App\Repositories\BandRepository;
use App\Services\BlogService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected BlogService $blogService)
    {
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Profile/Blogs/BlogCreateEdit', [
            'bandsList' => BandRepository::withoutPage(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogCreateRequest $request): RedirectResponse
    {
        $this->blogService->store($request->validated());

        session()->flash('message', 'Thank you, the blog has been created.');

        return redirect()->route('profile.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog): Response
    {
        $this->authorize('update', $blog);

        return Inertia::render('Profile/Blogs/BlogCreateEdit', [
            'blog' => $blog->load('bands', 'user'),
            'bandsList' => BandRepository::withoutPage(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Blog $blog, BlogUpdateRequest $request): RedirectResponse
    {
        $this->authorize('update', $blog);

        $this->blogService->update($blog, $request->validated());

        session()->flash('message', 'The blog has been updated.');

        return redirect()->route('profile.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog): RedirectResponse
    {
        $this->authorize('delete', $blog);

        $this->blogService->destroy($blog);

        session()->flash('message', 'The blog has been deleted.');

        return redirect()->route('profile.index');
    }
}
