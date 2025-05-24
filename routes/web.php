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



Route::get('/admin/user/{user}/wishlist/download', function (\App\Models\User $user) {
    $photos = $user->wishlist()->with('photo')->get();

    if ($photos->isEmpty()) {
        return back()->with('error', 'No wishlist photos found for this user.');
    }

    $zipFileName = 'wishlist_' . Str::slug($user->name) . '_' . time() . '.zip';
    $zipRelativePath = 'zips/' . $zipFileName;
    $zipFullPath = storage_path('app/public/' . $zipRelativePath);

    // Ensure 'zips' directory exists
    if (!Storage::disk('public')->exists('zips')) {
        Storage::disk('public')->makeDirectory('zips');
    }

    // Create zip file
    $zip = new \ZipArchive; // <-- Global class, no use statement needed
    if ($zip->open($zipFullPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
        foreach ($photos as $item) {
            if (!empty($item->photo) && !empty($item->photo->filename)) {
                $photoPath = storage_path('app/public/photos/' . $item->photo->filename);
                if (file_exists($photoPath)) {
                    $zip->addFile($photoPath, $item->photo->filename);
                }
            }
        }
        $zip->close();
    } else {
        return back()->with('error', 'Could not create ZIP file.');
    }

    // Stream the ZIP file as a download to avoid memory issues
    return response()->streamDownload(function () use ($zipFullPath) {
        $stream = fopen($zipFullPath, 'rb');
        fpassthru($stream);
        fclose($stream);
    }, $zipFileName);
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
