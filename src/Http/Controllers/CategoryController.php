<?php

namespace Local\CMS\Http\Controllers;

use Illuminate\Http\Request;
use Local\Ecommerce\Models\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Category::class);
    }

    public function index()
    {
        $categories = Category::all();
        return view('modules::categories.index', compact('categories'));
    }

    public function edit(Category $category)
    {
        return view('modules::categories.edit',compact('category'));
    }


    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            'name' => "required|unique:categories,name," . $category->id,
        ]);

        $category->update([
            "name" => $request->get('name'),
            "active" => $request->get('active') ? true : false,
        ]);

        return redirect()->route('admin.categories.index')->withSuccess('Category updated.');
    }

    public function create()
    {
        return view('modules::categories.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => "required|unique:categories,name",
        ]);

        Category::create([
            "name" => $request->get('name'),
            "active" => $request->get('active') ? true : false,
        ]);

        return redirect()->route('admin.categories.index')->withSuccess('Category created.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->withSuccess('Category deleted.');
    }
}
