<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Photo;
use App\Models\Wishlist;

class GalleryController extends Controller
{
    public function show($token)
    {
        $user = User::where('access_token', $token)->firstOrFail();
        $photos = Photo::all();
        $wishlist = $user->wishlists->pluck('photo_id')->toArray();

        return view('gallery', compact('user', 'photos', 'wishlist'));
    }

    public function toggleWishlist(Request $request)
    {
        $user = User::where('access_token', $request->token)->firstOrFail();
        $photoId = $request->photo_id;

        $existing = Wishlist::where('user_id', $user->id)
                            ->where('photo_id', $photoId)
                            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'photo_id' => $photoId
            ]);
        }

        return response()->json(['success' => true]);
    }
}
