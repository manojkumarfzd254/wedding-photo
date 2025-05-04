<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Video;

class VideoController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'videos.*' => 'required|mimes:mp4,mov,avi,webm|max:51200' // Max 50MB each
        ]);

        foreach ($request->file('videos') as $video) {
            $filename = uniqid() . '.' . $video->getClientOriginalExtension();
            $video->storeAs('public/videos', $filename);

            // Video::create([
            //     'filename' => $filename,
            //     'user_id' => auth()->id(), // or assign as needed
            // ]);
        }

        return back()->with('success', 'Videos uploaded successfully.');
    }
}
