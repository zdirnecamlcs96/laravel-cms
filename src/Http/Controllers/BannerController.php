<?php

namespace Local\CMS\Http\Controllers;

use Local\CMS\Models\Banner;
use Illuminate\Http\Request;
use Local\CMS\Models\File;
use Local\CMS\Traits\Helpers;
use Spatie\Activitylog\Models\Activity;

class BannerController extends Controller
{
    use Helpers;

    public function __construct()
    {
        $this->authorizeResource(Banner::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lastModified = Activity::where('subject_type', Banner::class)->orderBy('updated_at', 'desc')->first();
        $banners = Banner::whereActive(1)->orderBy('sequence')->get();
        return view('modules::banner.index', compact('banners', 'lastModified'));
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
        $this->validate($request, [
            'files' => "required|array",
            'files.*' => "required|integer"
        ]);

        $files = $request->get('files');

        foreach ($files as $id) {
            $banner = Banner::create([
                "title" => null,
                "desc" => null,
                "link" => null,
                "active" => 1
            ]);
            $banner->files()->detach();
            $banner->files()->attach([$id => ['zone' => 'banner-cover']]);
        }

        return back()->withSuccess('Banner(s) updated.');
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
    public function update(Request $request, Banner $banner)
    {
        // dd($request->all());

        $this->validate($request, [
            'sequence' => 'required|integer',
            'link' => 'max:153600'
        ]);

        $current_seq = $banner->sequence ?? 0;
        $target_seq = $request->get('sequence');

        if ($current_seq < $target_seq) {

            $banner_higher_seq = Banner::where('sequence', '>', $current_seq)
                ->where('sequence', '<=', $target_seq)
                ->get();

            foreach ($banner_higher_seq as $banner_higher) {
                $banner_higher->sequence -= 1;
                $banner_higher->save();
            }
        } elseif ($current_seq > $target_seq) {

            $banner_lower_seq = Banner::where('sequence', '<', $current_seq)
                ->where('sequence', '>=', $target_seq)
                ->get();

            foreach ($banner_lower_seq as $banner_lower) {
                $banner_lower->sequence += 1;
                $banner_lower->save();
            }
        }
        $banner->update([
            "sequence" => $target_seq,
            "title" => $request->get('title'),
            "desc" => $request->get('desc'),
            'display_in' => $request->get('display_in')
        ]);
        if($request->file('link')){
            $location = $this->__moveFile($request->file('link'),'file',null,true);
            $banner->update([
                "link" => $location
            ]);
        }


        return back()->with('Success','Banner updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Banner $banner)
    {
        $banner->delete();

        return back()->withSuccess('Banner deleted.');
    }
}
