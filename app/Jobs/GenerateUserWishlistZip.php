<?php
namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateUserWishlistZip implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        $user = User::find($this->userId);

        if (!$user) return;

        $photos = $user->wishlist()->with('photo')->get();

        if ($photos->isEmpty()) return;

        $fileName = 'wishlist_' . Str::slug($user->name) . '_' . time() . '.zip';
        $relativePath = 'zips/' . $fileName;
        $fullPath = storage_path('app/public/' . $relativePath);

        // Ensure directory exists
        if (!Storage::disk('public')->exists('zips')) {
            Storage::disk('public')->makeDirectory('zips');
        }

        $zip = new \ZipArchive;
        if ($zip->open($fullPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($photos as $item) {
                if (!empty($item->photo) && !empty($item->photo->filename)) {
                    $photoPath = storage_path('app/public/photos/' . $item->photo->filename);
                    if (file_exists($photoPath)) {
                        $zip->addFile($photoPath, $item->photo->filename);
                    }
                }
            }
            $zip->close();
        }

        // Optionally: save path to DB or cache for download
        $user->update(['wishlist_zip_path' => $relativePath]);
    }
}
