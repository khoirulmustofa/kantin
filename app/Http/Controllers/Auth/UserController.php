<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getPermissions(Request $request)
    {
        // Jika tidak ada user login, berikan respon 200 dengan array kosong
        if (!$request->user()) {
            return response()->json([
                'user' => null,
                'roles' => [],
                'permissions' => [],
            ]);
        }

        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
            ],
            'roles' => $request->user()->getRoleNames(),
            'permissions' => $request->user()->getAllPermissions()->pluck('name'),
        ]);
    }
}
