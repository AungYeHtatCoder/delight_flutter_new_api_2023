<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;

class ProfileApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
{
    $user = User::find(Auth::user()->id);

    if (auth()->user()->hasRole('Admin')) {
        return response()->json([
            'role' => 'Admin',
            'user' => $user,
        ], 200);
    } else {
        return response()->json([
            'role' => 'User',
            'user' => $user,
        ], 200);
    }
}

    // public function index()
    // {
    //     $user = auth()->user();
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'User Profile',
    //         'data' => $user
    //     ], 200);
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // Update the specified resource in storage.
public function update(Request $request)
{
    dd($request->all());
    if ($request->hasFile('profile')) {
        $profile = $request->file('profile');
        $ext = $profile->getClientOriginalExtension();

        // Checking the file extension
        if ($ext === "png" || $ext === "jpeg" || $ext === "jpg") {
            $user = User::find(Auth::user()->id);

            // Delete existing profile if it exists
            if ($user->profile) {
                File::delete(public_path('assets/img/profile/' . $user->profile));
            }

            // Save the new image
            $filename = uniqid('profile') . '.' . $ext;
            $profile->move(public_path('assets/img/profile/'), $filename);

            // Update the profile in the database
            $user->update([
                'profile' => $filename
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile has been updated',
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Please use a valid file type!',
            ], 400);
        }
    } else {
        return response()->json([
            'success' => false,
            'message' => 'No profile image uploaded',
        ], 400);
    }
}


// Password change function
public function changePassword(Request $request)
{
    $request->validate([
        'old_password' => 'required',
        'password' => 'required|confirmed|min:8',
    ]);

    $user = User::find(Auth::user()->id);

    if (Hash::check($request->old_password, $user->password)) {
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password has been updated',
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Old password does not match!',
        ], 400);
    }
}

// Phone and address update function
public function PhoneAddressChange(Request $request)
{
    $request->validate([
        'phone' => 'required',
        'address' => 'required',
    ]);

    $user = User::find(Auth::user()->id);
    $user->update([
        'phone' => $request->phone,
        'address' => $request->address,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Phone and address have been updated',
    ], 200);
}




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}