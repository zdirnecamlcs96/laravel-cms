<?php

namespace Local\CMS\Http\Controllers;

use Illuminate\Http\Request;
use Local\CMS\Models\NewsEvent;
use Carbon\Carbon;

class NewsEventController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(NewsEvent::class);
    }

    public function index()
    {
        $newsEvents = NewsEvent::orderBy('position')->get();
        return view('modules::newsEvents.index', compact('newsEvents'));
    }

    public function edit(NewsEvent $newsEvent)
    {
        return view('modules::newsEvents.edit', compact('newsEvent'));
    }

    public function update(Request $request, NewsEvent $newsEvent)
    {
        $this->validate($request, [
            'categories' => "required",
            'title' => "required",
            'description' => "required",
            'display_date' => 'nullable|date_format:d/m/Y',
            'status' => 'required|in:0,1',
            'position' => 'nullable|numeric|max:99999|min:0'
        ]);
        $original = $newsEvent->position;

        $default_date = $newsEvent->display_date ?? $newsEvent->created_at;
        $displayDate = Carbon::createFromFormat('d/m/Y', $request->get('display_date'))->format('Y-m-d');

        $newsEvent->update([
            "categories" => $request->get('categories'),
            "title" => $request->get('title'),
            "description" => $request->get('description'),
            "status" => $request->get('status'),
            "display_date" => $displayDate ?? $default_date,
            "position" => $request->position,
        ]);

        // NewsEvent::updatePosition($original,$newsEvent,$request->position);


        $newsEvent->files()->detach();
        $newsEvent->files()->attach([$request->get('banner') => ['zone' => 'banner']]);
        $newsEvent->files()->attach([$request->get('thumbnail') => ['zone' => 'thumbnail']]);

        return redirect()->route('admin.newsEvents.index')->withSuccess('News & Event updated.');
    }

    public function create()
    {
        return view('modules::newsEvents.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'categories' => "required",
            'description' => "required",
            'display_date' => 'nullable|date_format:d/m/Y',
            'status' => 'required|in:0,1',
            'position' => 'nullable|numeric|max:99999|min:0',
            'title' => 'required|string|max:191'
        ]);

        $displayDate = Carbon::createFromFormat('d/m/Y', $request->get('display_date'));
        $newEvent = NewsEvent::create([
            "categories" => $request->get('categories'),
            "description" => $request->get('description'),
            "status" => $request->get('status'),
            "display_date" => $displayDate ?? now(),
            "title" => $request->title,
            "position" => $request->position,
        ]);

        $newEvent->files()->detach();
        $newEvent->files()->attach([$request->get('banner') => ['zone' => 'banner']]);
        $newEvent->files()->attach([$request->get('thumbnail') => ['zone' => 'thumbnail']]);

        $original ='';
        // NewsEvent::updatePosition($original,$newEvent,$request->position);

        return redirect()->route('admin.newsEvents.index')->withSuccess('News & Event created.');
    }

    public function destroy(NewsEvent $newsEvent)
    {
        $newsEvent->delete();
        return redirect()->route('admin.newsEvents.index')->withSuccess('News & Event deleted.');
    }
}
