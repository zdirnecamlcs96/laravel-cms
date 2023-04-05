<?php

namespace Local\CMS\Http\Controllers;

use Local\CMS\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Local\CMS\Models\File;
use Local\CMS\Traits\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class FileController extends Controller
{
    use Helpers;

    public function __construct()
    {
        $this->authorizeResource(File::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = File::onlyPublic()->get();
        return view('modules::file.index', compact('files'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "file" => "required|array",
            "file.*" => "required|mimes:jpeg,jpg,png",
        ]);

        if ($validator->fails()) {
            return $this->__apiFailed("Validaiton Failed.", $validator->messages()->all());
        }

        DB::beginTransaction();

        if($request->hasFile("file"))
        {
            foreach ($request->file("file") as $file) {
                $uploadedFile = $this->__storeImage($file);
                if(empty($uploadedFile)) {
                    return $this->__apiFailed("Failed to upload file(s).");
                }
                $oFile = $uploadedFile->original ?? $uploadedFile;
                File::create([
                    "name" => $oFile->filename,
                    "original_name" => $this->__getFileOriginalName($file),
                    "extension" => $oFile->extension,
                    "mime" => $oFile->mime(),
                    "size" => $oFile->filesize(),
                    "path" => File::PATH_TO_STORAGE,
                    "ip_address" => $request->ip()
                ]);
            }
        }

        DB::commit();

        return $this->__apiSuccess('File(s) uploaded.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'link' => 'url',
        ]);

        $validator->setAttributeNames([
            'file' => 'Link',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, "formerror");
        }

        $banner = Banner::find($id);
        $current_seq = $banner->seq;
        $target_seq = $request->input('seq');

        if ($current_seq < $target_seq) {
            $banner_higher_seq = Banner::where('seq', '>', $current_seq)->where('seq', '<=', $target_seq)->get();
            foreach ($banner_higher_seq as $banner_higher) {
                $banner_higher->seq -= 1;
                $banner_higher->save();
            }
        } elseif ($current_seq > $target_seq) {
            $banner_lower_seq = Banner::where('seq', '<', $current_seq)->where('seq', '>=', $target_seq)->get();
            foreach ($banner_lower_seq as $banner_lower) {
                $banner_lower->seq += 1;
                $banner_lower->save();
            }
        }

        $banner->seq = $target_seq;
        $banner->title = $request->input('title');
        $banner->desc = $request->input('desc');
        $banner->link = $request->input('link');
        $banner->save();

        return redirect()->back()->withSuccess('Banner has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        $file->delete();

        return back()->withSuccess('File deleted.');
    }

    public function manager() {
        $files = File::get();
        Config::set('adminlte.classes_topnav', config('adminlte.classes_topnav') . ' d-none');
        Config::set('adminlte.layout_topnav', true);

        return view('modules::file.index', compact('files'));
    }
}
