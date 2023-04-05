<?php

namespace Local\CMS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Local\CMS\Traits\Helpers;

class CkeditorController extends Controller
{
    use Helpers;

    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->toArray(), [
            "upload" => "required|file|image|max:8000"
        ], [
            "upload.required" => "Please select an image to upload.",
            "upload.file" => "Please upload an image.",
            "upload.image" => "Invalid file type. Please upload image type only. (jpg, jpeg, png, bmp and svg)",
            "upload.max" => "Upload limit exceeded, please upload the image within 8MB."
        ]);


        if ($validator->fails()) {
            return response()->json([
                "uploaded" => 0,
                "error" => [
                    "message" => $validator->errors()->first()
                ]
            ]);
        }

        if ($request->has("upload")) {

            $filename = Storage::put('ckeditor', $request->file('upload'));


            if (!$filename) {
                return response()->json([
                    "uploaded" => 0,
                    "error" => [
                        "message" => "Fail to upload image."
                    ]
                ]);
            }


            $fileurl = asset('storage/ckeditor/' . $request->file('upload')->hashName());

            return response()->json([
                "uploaded" => 1,
                "fileName" => $filename,
                "url" =>  $fileurl
            ]);
        }
    }
}
