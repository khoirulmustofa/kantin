<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class UtilitiesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            $logSize = file_exists($logPath) ? round(filesize($logPath) / 1024 / 1024, 2) : 0;
            $isDebug = config('app.debug');

            return Inertia::render('Admin/Utilities/Index', [
                'menu' => 'utilities',
                'title' => 'System Utilities',
                'logSize' => $logSize,
                'isDebug' => $isDebug,
                'phpVersion' => PHP_VERSION,
                'laravelVersion' => \Illuminate\Foundation\Application::VERSION,
            ]);
        } catch (\Throwable $th) {
            return Inertia::render('Errors/NotFound', [
                'status' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function clearCache()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            return redirect()->back()->with('success', 'System cache cleared successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function clearSession()
    {
        try {
            $path = storage_path('framework/sessions');

            // Pastikan folder ada
            if (!File::isDirectory($path)) {
                return redirect()->back()->with('error', 'Session directory not found.');
            }

            // Ambil semua file (kecuali yang tersembunyi seperti .gitignore)
            $files = File::allFiles($path);

            foreach ($files as $file) {
                // Kita hapus filenya
                File::delete($file->getRealPath());
            }

            // PENTING: Flush sesi user yang sedang menekan tombol ini agar tidak 'nyangkut'
            session()->flush();
            return redirect()->back()->with('success', 'System sessions cleared successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function clearLog()
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            if (file_exists($logPath)) {
                file_put_contents($logPath, '');
            }
            return redirect()->back()->with('success', 'System logs cleared successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function toggleDebug()
    {
        try {
            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $content = file_get_contents($envPath);
                $isDebug = config('app.debug');
                $newVal = $isDebug ? 'false' : 'true';

                $content = preg_replace('/APP_DEBUG=(true|false)/', 'APP_DEBUG=' . $newVal, $content);
                file_put_contents($envPath, $content);

                // Clear cache to apply changes
                \Illuminate\Support\Facades\Artisan::call('config:clear');
            }

            return redirect()->back()->with('success', 'Debug mode updated successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
