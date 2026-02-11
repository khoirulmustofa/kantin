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
            $settings = \App\Models\Setting::all()->pluck('value', 'key');

            return Inertia::render('Admin/Setting/Index', [
                'menu' => 'settings',
                'title' => 'Settings',
                'settings' => $settings,
            ]);
        } catch (\Throwable $th) {
            return Inertia::render('Errors/Error500', [
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            $data = $request->except(['_token', '_method']);

            foreach ($data as $key => $value) {
                // If the value is a file (like site_logo), handle the upload
                if ($request->hasFile($key)) {
                    $file = $request->file($key);

                    // Get old setting to delete old image if exists
                    $oldSetting = \App\Models\Setting::where('key', $key)->first();
                    if ($oldSetting && $oldSetting->value) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldSetting->value);
                    }

                    $path = $file->store('settings', 'public');
                    $value = $path;
                }

                \App\Models\Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            Cache::forget('app_settings');

            return redirect()->back()->with('success', 'Settings updated successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
