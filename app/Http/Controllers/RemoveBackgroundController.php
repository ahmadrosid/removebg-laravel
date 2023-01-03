<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RemoveBackgroundController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image',
        ]);

        if ($validator->fails()) {
            return response([
                "error" => true,
                "errors" => $validator->errors(),
            ], 400);
        }

        $photo = $request->file('photo');
        $name = Str::random(40) . "." . $photo->getClientOriginalExtension();
        Storage::putFileAs('public/photos', $photo, $name);

        $response = Http::withHeaders([
            'X-Api-Key' => config("app.photo_room.api_key")
        ])
        ->timeout(60)
        ->attach(
            'image_file', file_get_contents(Storage::disk('public')->path('photos/' . $name)), $name
        )->post('https://sdk.photoroom.com/v1/segment');

        if ($response->getStatusCode() != 200) {
            return $response;
        }

        Storage::put('public/outputs/'.$name, $response->getBody());
        return [
            'original' => asset("/storage/photos/" . $name),
            'result' => asset("/storage/outputs/" . $name),
        ];
    }
}
