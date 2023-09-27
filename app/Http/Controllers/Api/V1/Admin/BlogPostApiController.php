<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Admin\Blog;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Http\Resources\Admin\BlogPostResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // Import the Storage facade
use Illuminate\Support\Facades\URL;
use Illuminate\Http\UploadedFile;

class BlogPostApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('blog_post_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BlogPostResource(Blog::with(['users'])->get());
    }
    public function store(Request $request)
{
    try {
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;

        /** @var \Illuminate\Http\UploadedFile $image */
        $image = $data['image'] ?? null;

        // Check if image was given and save on local file system
        if ($image) {
            $relativePath = $this->saveImage($image);
            $data['image'] = URL::to(Storage::url($relativePath));
            $data['image_mime'] = $image->getClientMimeType();
            $data['image_size'] = $image->getSize();
        }

        Blog::create($data);
        return response()->json(['message' => 'Blog Created successfully'], 201);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to create blog', 'details' => $e->getMessage()], 500);
    }
}
    // public function store(StoreBlogPostRequest $request)
    // {
        
    //     $request->validate([
    //         'title' => 'required',
    //         'image' => 'required',
    //         'description' => 'required',
    //     ]);

    //     $image = $request->file('image');
    //     $ext = $image->getClientOriginalExtension();
    //     $filename = uniqid('blog') . '.' . $ext; // Generate a unique filename
    //     $image->move(public_path('assets/img/blogs/'), $filename);

    //    $blogPost = Blog::create([
    //         'title' => $request->title,
    //         'image' => $filename,
    //         'description' => $request->description,
    //         'user_id' => Auth::user()->id
    //     ]);

    //     return (new BlogPostResource($blogPost))
    //         ->response()
    //         ->setStatusCode(Response::HTTP_CREATED);
    // }

    public function show(Blog $blogPost)
    {
        //abort_if(Gate::denies('blog_post_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BlogPostResource($blogPost->load(['users']));
    }
    public function showDetail(Blog $blogPost)
    {
        //abort_if(Gate::denies('blog_post_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BlogPostResource($blogPost->load(['users']));
    }

    public function update(Request $request, $id)
{
    try {
        $blog = Blog::findOrFail($id);

        $data = $request->all();

        // For APIs, get the user from the API guard.
        //$data['user_id'] = auth('api')->user()->id;
        $data['user_id'] = auth()->user()->id;

        // Check if an image has been uploaded
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete the old image if it exists
            if ($blog->image) {
                Storage::delete('public/' . $blog->image);
            }

            // Save the new image and retrieve its path
            $uploadedImage = $data['image'];
            $relativePath = $this->saveImage($uploadedImage);

            // Update the image data for the blog
            $data['image'] = URL::to(Storage::url($relativePath));
            $data['image_mime'] = $uploadedImage->getClientMimeType();
            $data['image_size'] = $uploadedImage->getSize();
        }

        $blog->update($data);

        // Return a JSON response
        return response()->json([
            'message' => 'Blog Updated.',
            'blog' => $blog
        ], 200);

    } catch (\Exception $e) {
        Log::error("Failed to update blog: " . $e->getMessage());

        return response()->json([
            'message' => "Failed to update blog.",
            'error' => $e->getMessage()
        ], 500);
    }
}


//     public function update(Request $request, $id)
// {
//     // Validation
//     $request->validate([
//         'title' => 'required',
//         'description' => 'required',
//     ]);

//     // Logging
//     Log::info('Update method called');
//     Log::info('Request data: ', $request->all());

//     // Find the blog by ID
//     $blog = Blog::find($id);

//     // Check if blog exists
//     if (!$blog) {
//         return response()->json(['error' => 'Blog Not Found!'], Response::HTTP_NOT_FOUND);
//     }

//     // Update blog without image
//     if (!$request->hasFile('image')) {
//         $blog->title = $request->title;
//         $blog->description = $request->description;
//     } else {
//         // Delete old image
//         if (File::exists(public_path('assets/img/blogs/' . $blog->image))) {
//             File::delete(public_path('assets/img/blogs/' . $blog->image));
//         }

//         // Upload new image
//         $image = $request->file('image');
//         $ext = $image->getClientOriginalExtension();
//         $filename = uniqid('blog') . '.' . $ext;
//         $image->move(public_path('assets/img/blogs/'), $filename);

//         // Update blog details
//         $blog->title = $request->title;
//         $blog->image = $filename;
//         $blog->description = $request->description;
//     }

//     // Add user_id if you need it
//     $blog->user_id = Auth::user()->id;

//     // Save the changes
//     if (!$blog->save()) {
//         return response()->json(['error' => 'Failed to update blog'], Response::HTTP_INTERNAL_SERVER_ERROR);
//     }

//     return (new BlogPostResource($blog))
//         ->response()
//         ->setStatusCode(Response::HTTP_ACCEPTED);
// }
    public function destroy(Blog $blogPost)
    {
        abort_if(Gate::denies('blog_post_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $blogPost->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function saveImage(UploadedFile $image)
    {
        $path = 'banner_image/' . Str::random();
        //$path = 'images/product_image';

        if (!Storage::exists($path)) {
            Storage::makeDirectory($path, 0755, true);
        }
        if (!Storage::putFileAS('public/' . $path, $image, $image->getClientOriginalName())) {
            throw new \Exception("Unable to save file \"{$image->getClientOriginalName()}\"");
        }

        return $path . '/' . $image->getClientOriginalName();
    }
}