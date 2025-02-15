<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacades;
use Illuminate\Support\Facades\Storage;

class BaseController extends Controller
{
    public function upload($folder = 'images', $key = 'avatar')
    {
        return Storage::disk('public')->putFile($folder, request()->file($key), 'public');
    }

    public function sendSuccess($data = [], $message, $code = 200)
    {
        $obj = [
            'status' => true,
            'data' => $data,
            'message' => $message
        ];
        return response()->json($obj, $code);
    }

    public function sendError($message, $code = 404)
    {
        $obj = [
            'status' => false,
            'message' => $message
        ];
        return response()->json($obj, $code);
    }

}
