<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PhotoController extends Controller
{
    public function submit(Request $request)
    {
        $image = $request->input('image');
        $name = $request->input('name');
        $detectio = $request->input('detectio') ? true : false;

        $response = Http::post('http://127.0.0.2:8010/detectingbell', [
            'image' => $image,
            'name' => $name,
            'detectio' => $detectio
        ]);

        return $response->json();
    }
}
