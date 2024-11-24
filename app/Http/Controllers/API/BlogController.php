<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Http\Resources\BlogsResource;
use App\Models\Blog;
use App\Services\BlogsService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use AuthorizesRequests;
    protected $blogsService;

    public function __construct(BlogsService $blogsService)
    {
        $this->blogsService = $blogsService;
    }

    public function index()
    {
        $blogs = $this->blogsService->listAllBlogs();
        return BlogsResource::collection($blogs);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request)
    {

        $blog = $this->blogsService->createBlogs($request->validated());
        return new BlogsResource($blog);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $token = request()->bearerToken();

        $user = $token ? PersonalAccessToken::findToken($token)?->tokenable : null;

        $blog = $this->blogsService->getById($id);

        return new BlogsResource($blog);

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogRequest $request, $id)
    {
        Log::info('Request data:', $request->all());
        Log::info('Validated data:', $request->validated());

        $blog = Blog::findOrFail($id);
        $this->authorize('update', $blog);

        $validatedData = $request->validated();

        // Handle the image upload
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($blog->image && Storage::exists($blog->image)) {
                Storage::delete($blog->image);
            }

            // Store the new image in the 'public/blogs' directory
            $imagePath = $request->file('image')->store('blogs', 'public');
            $validatedData['image'] = $imagePath;
        }

        $updatedBlog = $this->blogsService->updateBlogs($blog, $validatedData);
        return new BlogsResource($updatedBlog);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return response()->json(['error' => 'Invalid request. Please provide an array of IDs.'], 400);
        }

        $blogs = Blog::whereIn('id', $ids)->get();

        foreach ($blogs as $blog) {
            $this->authorize('delete', $blog);
        }

        $response = $this->blogsService->deleteBlogs($blogs);

        return response()->json($response);    }
}