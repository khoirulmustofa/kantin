<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        try {
            return Inertia::render('Admin/Setting/Index', [
                'menu' => 'settings',
                'title' => 'Settings',
            ]);
        } catch (\Throwable $th) {
            return Inertia::render('Errors/NotFound', [
                'status' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function getSettings()
    {
        try {
            $settings = \App\Models\Setting::all()->pluck('value', 'key');
            return response()->json([
                'status' => true,
                'settings' => $settings
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $data = $request->except(['_token', '_method']);

            foreach ($data as $key => $value) {
                // Handle front_slider (multiple files)
                if ($key === 'front_slider') {
                    if ($request->hasFile('front_slider')) {
                        $files = $request->file('front_slider');
                        $existingSlider = \App\Models\Setting::where('key', 'front_slider')->first();
                        $images = $existingSlider ? json_decode($existingSlider->value, true) : [];
                        if (!is_array($images)) $images = [];

                        foreach ($files as $file) {
                            $path = $file->store('settings/slider', 'public');
                            $images[] = $path;
                        }
                        $value = json_encode(array_values($images));
                    } else {
                        // Skip updating front_slider if no new files are uploaded
                        continue;
                    }
                }
                // Handle single file uploads
                elseif ($request->hasFile($key)) {
                    $file = $request->file($key);

                    // Get old setting to delete old image if exists
                    $oldSetting = \App\Models\Setting::where('key', $key)->first();
                    if ($oldSetting && $oldSetting->value && !str_starts_with($oldSetting->value, '[')) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldSetting->value);
                    }

                    $path = $file->store('settings', 'public');
                    $value = $path;
                }

                if ($value !== null) {
                    \App\Models\Setting::updateOrCreate(
                        ['key' => $key],
                        ['value' => $value]
                    );
                }
            }

            Cache::forget('app_settings');

            return redirect()->back()->with('success', 'Settings updated successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function deleteSliderImage(Request $request)
    {
        $request->validate([
            'image_path' => 'required|string'
        ]);

        try {
            $existingSlider = \App\Models\Setting::where('key', 'front_slider')->first();
            if ($existingSlider) {
                $images = json_decode($existingSlider->value, true);
                if (is_array($images)) {
                    $newImages = array_filter($images, function ($img) use ($request) {
                        return $img !== $request->image_path;
                    });

                    if (count($newImages) !== count($images)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($request->image_path);
                        $existingSlider->update(['value' => json_encode(array_values($newImages))]);
                        Cache::forget('app_settings');
                    }
                }
            }

            return response()->json(['status' => true, 'message' => 'Image deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()], 500);
        }
    }
}
