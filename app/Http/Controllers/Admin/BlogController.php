<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Blog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Requests\BlogRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
class BlogController extends Controller
{
    public function index(){
        $blogs = Blog::with('users')->latest()->get();
        // return $blogs;
        return view('Admin.blogs.index', compact('blogs'));
    }

    public function create(){
        return view('Admin.blogs.create');
    }

    public function store(BlogRequest $request){
        $data = $request->validated();
        // user_id
        $data['user_id'] = Auth::user()->id;
        //dd($data);

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
        return redirect('/admin/blogs/')->with('success', "Blog Created.");

        // $request->validate([
        //     'title' => 'required',
        //     'image' => 'required',
        //     'description' => 'required',
        // ]);

        // $image = $request->file('image');
        // $ext = $image->getClientOriginalExtension();
        // $filename = uniqid('blog') . '.' . $ext; // Generate a unique filename
        // $image->move(public_path('assets/img/blogs/'), $filename);

        // Blog::create([
        //     'title' => $request->title,
        //     'image' => $filename,
        //     'description' => $request->description,
        //     'user_id' => Auth::user()->id
        // ]);

        // return redirect('/admin/blogs/')->with('success', "Blog Created.");
    }

    public function view($id){
        $blog = Blog::withCount(['likes', 'comments'])->where('id', $id)->first();
        // return $blog;
        return view('Admin.blogs.view', compact('blog'));
    }

    public function edit($id){
        $blog = Blog::find($id);
        return view('Admin.blogs.edit', compact('blog'));
    }

    public function update(BlogRequest $request, Blog $blog)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::user()->id;

        $image = $data['image'] ?? null;
        
        if ($image) {
            // Handle old image if exists
            if ($blog->image) {
                Storage::delete('public/' . $blog->image);
            }

            $relativePath = $this->saveImage($image);
            $data['image'] = URL::to(Storage::url($relativePath));
            $data['image_mime'] = $image->getClientMimeType();
            $data['image_size'] = $image->getSize();
        }

        try {
            $blog->update($data);
            return redirect('/admin/blogs/')->with('success', "Blog Updated.");
        } catch (\Exception $e) {
            Log::error("Failed to update blog: " . $e->getMessage());
            return redirect('/admin/blogs/')->with('error', "Failed to update blog.");
        }
    }

    public function delete(Request $request){
        $id = $request->id;
        $blog = Blog::find($id);
        if(!$blog){
            return redirect()->back()->with('error', "Blog Not Found!");
        }
        Blog::destroy($id);
        return redirect('/admin/blogs/')->with('success', "Blog Removed.");
    }

    public function saveImage(UploadedFile $image)
    {
        $path = 'blog_image/' . Str::random();

        if (!Storage::exists($path)) {
            Storage::makeDirectory($path, 0755, true);
        }

        if (!Storage::putFileAs('public/' . $path, $image, $image->getClientOriginalName())) {
            throw new \Exception("Unable to save file \"{$image->getClientOriginalName()}\"");
        }

        return $path . '/' . $image->getClientOriginalName();
    }
    // public function saveImage(UploadedFile $image)
    // {
    //     $path = 'blog_image/' . Str::random();
    //     //$path = 'images/product_image';

    //     if (!Storage::exists($path)) {
    //         Storage::makeDirectory($path, 0755, true);
    //     }
    //     if (!Storage::putFileAS('public/' . $path, $image, $image->getClientOriginalName())) {
    //         throw new \Exception("Unable to save file \"{$image->getClientOriginalName()}\"");
    //     }

    //     return $path . '/' . $image->getClientOriginalName();
    // }
}