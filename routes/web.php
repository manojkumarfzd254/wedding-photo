<?php

use App\Http\Controllers\GalleryController;
use App\Http\Controllers\VideoController;
use App\Jobs\GenerateUserWishlistZip;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/gallery/{token}', [GalleryController::class, 'show']);
Route::post('/wishlist-toggle', [GalleryController::class, 'toggleWishlist']);

Route::get('/admin/upload', function () {
    return view('upload');
});


Route::post('/admin/upload', function (Request $request) {
    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $file) {
            // Get original file name
            $originalName = $file->getClientOriginalName();
            $storagePath = 'photos/' . $originalName;

            // Skip if file with same name already exists in storage
            if (Storage::disk('public')->exists($storagePath)) {
                continue;
            }

            // Store file
            $path = $file->storeAs('photos', $originalName, 'public');

            // Save to database
            Photo::create(['filename' => $originalName]);
        }
    }

    return response()->json(['success' => true]);
});


Route::get('/admin/users', function () {
    $users = User::withCount('wishlist')->get(); // also show how many photos in each wishlist
    return view('admin.users.index', compact('users'));
})->name('admin.users.index');

Route::get('/admin/user/{user}/wishlist', function (User $user) {
    $photos = $user->wishlist()->with('photo')->get();
    return view('admin.users.wishlist', compact('user', 'photos'));
})->name('admin.user.wishlist');



Route::get('/admin/user/{user}/wishlist/prepare-download', function (\App\Models\User $user) {
    GenerateUserWishlistZip::dispatch($user->id);
    return back()->with('success', 'Your wishlist ZIP is being prepared. Check back in a few minutes.');
})->name('admin.user.wishlist.prepare');


Route::get('/admin/user/{user}/wishlist/download', function (\App\Models\User $user) {
    $relativePath = $user->wishlist_zip_path;

    if (!$relativePath || !Storage::disk('public')->exists($relativePath)) {
        return back()->with('error', 'The ZIP file is not ready yet. Please try again later.');
    }

    return response()->download(storage_path('app/public/' . $relativePath));
})->name('admin.user.wishlist.download');




Route::get('/routes', function () {
    $routes = collect(Route::getRoutes())->map(function ($route) {
        return [
            'method' => implode('|', $route->methods),
            'uri' => $route->uri,
            'name' => $route->getName(),
            'action' => $route->getActionName(),
            'middleware' => implode(', ', $route->middleware()),
        ];
    });

    return view('routes.index', compact('routes'));
}); // Optional: restrict to logged-in users only

Route::post('/upload-videos', [VideoController::class, 'upload'])->name('videos.upload');
