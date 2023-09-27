<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Admin\Banner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BannerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage; // Import the Storage facade
use Illuminate\Support\Facades\Validator;
class BannerApiController extends Controller
{
    public function index()
    {
        $banners = Banner::all();
        return BannerResource::collection($banners);
    }

    public function show($id)
    {
        $banner = Banner::findOrFail($id);
        return new BannerResource($banner);
    }

    public function store(Request $request){
    // Validation
    $validator = Validator::make($request->all(), [
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 401);
    }

    // Handling Image
    $image = $request->file('image');
    $ext = $image->getClientOriginalExtension();
    $filename = uniqid('banner') . '.' . $ext;
    
    // Save the image in a directory in your public folder or any storage disk
    $path = $image->storeAs('public/assets/img/banners', $filename);  // Update the path as needed

    // Create new banner
    $banner = Banner::create([
        'image' => $filename
    ]);
        return response()->json(['success' => 'Banner Created', 'banner' => $banner], 200);

}
    public function update(Request $request, $id)
{
    // Validation rules for the image only
    // $validator = Validator::make($request->all(), [
    //     'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    // ]);

    // // Handle validation failure
    // if ($validator->fails()) {
    //     return response()->json(['error' => $validator->errors()], 401);
    // }

    // Find the existing banner or fail
    $banner = Banner::findOrFail($id);

    // Handling Image
    if ($request->hasFile('image')) {
        // Delete the old image file
        Storage::delete('public/assets/img/banners/' . $banner->image);

        // Upload the new image
        $image = $request->file('image');
        $ext = $image->getClientOriginalExtension();
        $filename = uniqid('banner') . '.' . $ext;
        $path = $image->storeAs('public/assets/img/banners', $filename);

        // Update the image field
        $banner->image = $filename;
    }

    // Save the changes
    $banner->save();

    return response()->json(['success' => 'Banner Image Updated', 'banner' => $banner], 200);
}

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return response()->json(['message' => 'Banner deleted'], 200);
    }
    
    public function statusChange(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required'
        ]);
    
        $banner = Banner::find($id);
    
        if (!$banner) {
            return response()->json(['error' => 'Banner Not Found!'], 404);
        }
    
        $banner->update([
            'status' => $request->status
        ]);
    
        return response()->json(['success' => 'Banner Status Updated.'], 200);
    }
}