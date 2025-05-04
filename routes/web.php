<?php

use App\Http\Controllers\GalleryController;
use App\Http\Controllers\VideoController;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

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
            // Get the original filename
            $originalName = $file->getClientOriginalName();

            // Store the file and retain the original filename
            $path = $file->storeAs('photos', $originalName, 'public');

            // Save the original filename to the database
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

Route::get('/admin/user/{user}/wishlist/download', function (\App\Models\User $user) {
    $photos = $user->wishlist()->with('photo')->get();

    if ($photos->isEmpty()) {
        return back()->with('error', 'No wishlist photos found for this user.');
    }

    $zipFileName = 'wishlist_' . Str::slug($user->name) . '_' . time() . '.zip';
    $zipPath = storage_path('app/public/zips/' . $zipFileName);

    // Ensure directory exists
    if (!Storage::disk('public')->exists('zips')) {
        Storage::disk('public')->makeDirectory('zips');
    }

    $zip = new ZipArchive;
    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        foreach ($photos as $item) {
            $photoPath = storage_path('app/public/photos/' . $item->photo->filename);
            if (file_exists($photoPath)) {
                $zip->addFile($photoPath, $item->photo->filename);
            }
        }
        $zip->close();
    }

    return response()->download($zipPath)->deleteFileAfterSend(true);
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
