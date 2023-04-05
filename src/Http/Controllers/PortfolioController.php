<?php

namespace Local\CMS\Http\Controllers;

use Illuminate\Http\Request;
use Local\CMS\Models\File;
use Local\CMS\Models\ImagePortfolio;
use Local\CMS\Models\Portfolio;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $portfolios =  Portfolio::get();
        return view('modules::portfolio.index', compact('portfolios'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('modules::portfolio.create');
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
            'title' => "required|string",
            'description' => "required|string",
            'files' => "required|array",
            'files.*' => "required|integer|exists:files,id"
        ]);

        $files = $request->get('files');

        $slug = $this->__generateUniqueSlug($request->get('title'), Portfolio::class);

        $portfolio = Portfolio::create([
            "title" => $request->get('title'),
            "slug" => $slug,
            "description" => $request->get('description'),
            "active" => 1
        ]);

        foreach ($files as $id) {
            $imagePortfolio = ImagePortfolio::create([
                "portfolio_id" => $portfolio->id,
                "sequence" => 0,
                "active" => 1
            ]);
            $imagePortfolio->files()->sync([$id]);
        }

        return back()->withSuccess('Portfolio created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Portfolio $portfolio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Portfolio $portfolio)
    {
        $images = $portfolio->images;
        return view('modules::portfolio.edit', compact('portfolio', 'images'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        $this->validate($request, [
            'title' => "required|string",
            'description' => "required|string",
            'files' => "required|array",
            'files.*' => "required|integer|exists:files,id"
        ]);

        $files = $request->get('files');

        $slug = $this->__generateUniqueSlug($request->get('title'), Portfolio::class);

        $portfolio->title = $request->get('title');

        $data = [
            "title" => $request->get('title'),
            "description" => $request->get('description'),
            "active" => 1
        ];

        if($portfolio->isDirty('title'))
        {
            $data = array_merge($data, [
                "slug" => $portfolio->isDirty('title')
            ]);
        }

        $portfolio->update($data);

        if(count($files) > 0) {
            $portfolio->images()->delete();
            foreach ($files as $id) {
                $imagePortfolio = ImagePortfolio::create([
                    "portfolio_id" => $portfolio->id,
                    "sequence" => 0,
                    "active" => 1
                ]);
                $imagePortfolio->files()->attach($id);
            }
        }

        return back()->withSuccess('Portfolio updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Portfolio $portfolio)
    {
        $portfolio->delete();

        return back()->withSuccess('Portfolio deleted.');
    }
}
