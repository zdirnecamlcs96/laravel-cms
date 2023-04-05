<?php

namespace Local\CMS\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Local\Ecommerce\Models\Category;
use Local\Ecommerce\Models\Product;
use Local\Ecommerce\Models\ProductVariant;
use Local\Ecommerce\Models\ProductVariantOption;
use Local\Ecommerce\Models\Sku;
use stdClass;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Product::class);
    }

    public function index()
    {
        // dd('sda');
        $products = Product::latest()->get();
        return view('modules::products.index', compact('products'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('active', true)->get()->pluck('name', 'id');
        $variants_data = $product->variants()->with('options')->get();
        $skus_data = new Collection();

        $product_skus = $product->skus;


        foreach ($product_skus as $sku) {
            $skus_data->push([
                "sku" => $sku,
                'variant_options' => $sku->sku_product_options()->get()->pluck('value', 'product_variant.value'),
                'display_options' => $sku->sku_product_options()->get()->pluck('value')
            ]);
        }

        return view('modules::products.edit', compact('product', 'categories', 'skus_data', 'variants_data'));
    }

    public function update(Request $request, Product $product)
    {  
        $this->validate($request, [
            'thumbnail' => "required",
            'name' => "required",
            'description' => "required",
            'price' => "required_if:skus_data,[]|min:1",
            'categories' => "required",
            'weight' => "required",
            'sequence' => "required"
        ], [
            'price.required_if' => 'The price field is required when there are no variants.'
        ]);
        if($request->options) {
            $options = $request->get('options');
            foreach($options as $option) {
                if(empty($option['name'])) {
                    return response()->json(['message' => 'Option name is required'], 500);
                } else if(empty($option['values'])) {
                    return response()->json(['message' => 'Option value is required for '.$option['name']], 500);
                }
            }
        }

        DB::beginTransaction();

        try {
            $product->update([
                "quantity" => $request->get('quantity'),
                "name" => $request->get('name'),
                "description" => $request->get('description'),
                "price" => $request->get('price'),
                "special_price" => $request->get('special_price'),
                "special_price_type" => 'Fixed',
                "special_price_start" => ($request->get('special_price_start')) ? 
                    Carbon::createFromFormat('d/m/Y',$request->get('special_price_start')) : null,
                "special_price_end" => ($request->get('special_price_end')) ? 
                    Carbon::createFromFormat('d/m/Y',$request->get('special_price_end')) : null,
                "active" => $request->get('active') ? true : false,
                "weight" => $request->get('weight') ?? 0,
                "sequence" => $request->get('sequence'),
                "is_feature" => $request->get('is_feature') ? true : false,
            ]);

            $product->files()->detach();
            $product->files()->attach($request->get('thumbnail'), ['zone' => 'product_thumbnail']);
            $product->files()->attach($request->get('additional_images'), ['zone' => 'product_additional_images']);

            $product->categories()->sync($request->get('categories'));

            $product->updateVariants($request->get('variants_data'), $request->get('skus_data'));
        } catch (\Exception $ex) {
            DB::rollBack();
       
           return response()->json(['message'=>'Something went wrong '.$ex->getMessage()],500);
        }

        DB::commit();

        return response()->json([
            "message" => "Product updated.",
            "redirect" => route('admin.products.edit', $product)
        ]);
    }

    public function create()
    {
        $categories = Category::where('active', true)->get()->pluck('name', 'id');
        return view('modules::products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'thumbnail' => "required",
            'additional_images' => "required",
            'name' => "required",
            'description' => "required",
            'price' => "required_if:skus_data,[]|min:1",
            'categories' => "required",
            'weight' => "required"
        ], [
            'price.required_if' => 'The price field is required when there are no variants.'
        ]);

        DB::beginTransaction();

        try {
            $product = Product::create([
                "name" => $request->get('name'),
                "description" => $request->get('description'),
                "price" => $this->__requestFilled('price', 0),
                "special_price" => $request->get('special_price') ?? 0,
                "special_price_type" => 'Fixed',
                "special_price_start" => $request->get('special_price_start') ? Carbon::createFromFormat('d/m/Y', $request->get('special_price_start')) : null,
                "special_price_end" => $request->get('special_price_end') ? Carbon::createFromFormat('d/m/Y', $request->get('special_price_end')) : null,
                "quantity" => $request->get('quantity'),
                "active" => $request->get('active'),
                "weight" => $request->get('weight') ?? 0,
                "sequence" => $request->get('sequence') ?? Product::count() + 1 ,
                "is_feature" => $request->get('is_feature'),
            ]);

            $product->files()->detach();
            $product->files()->attach($request->get('thumbnail'), ['zone' => 'product_thumbnail']);
            $product->files()->attach($request->get('additional_images'), ['zone' => 'product_additional_images']);
            $product->categories()->sync($request->get('categories'));

            foreach (json_decode($request->get('variants_data')) as $variants) {
                $product_variant = $product->variants()->create([
                    'value' => $variants->value,
                ]);

                foreach ($variants->options as $option) {
                    $product_variant->options()->create([
                        'value' => $option->value,
                    ]);
                }
            }

            $proOpts = $product->variants()->with('options')->get()->flatten()->all();

            foreach (json_decode($request->get('skus_data')) as $sku_data) {

                if (!$sku_data->sku->price || $sku_data->sku->price < 1) {
                    throw ValidationException::withMessages([
                        'price' => 'The price in sku\'s field is required and minimum value is 1.'
                    ]);
                }

                if(!$sku_data->sku->quantity){
                    throw ValidationException::withMessages([
                        'quantity' => 'The quantity in sku\'s field is required and minimum value is 1.'
                    ]);
                }

                $sku_item = $product->skus()->create([
                    'code' => $sku_data->sku->code,
                    'price' => !empty($sku_data->sku->price) ? $sku_data->sku->price : $product->price,
                    'special_price' => !empty($sku_data->sku->special_price) ? $sku_data->sku->special_price : $product->special_price,
                    'special_price_type' => !empty($sku_data->sku->special_price_type) ? $sku_data->sku->special_price_type : $product->special_price_type,
                    'special_price_start' => !empty($sku_data->sku->special_price_start) ? $sku_data->sku->special_price_start : $product->special_price_start,
                    'special_price_end' => !empty($sku_data->sku->special_price_end) ? $sku_data->sku->special_price_end : $product->special_price_end,
                    'quantity' => $sku_data->sku->quantity,
                ]);


                $options = is_string($sku_data->display_options) ? [$sku_data->display_options] : $sku_data->display_options;

                // array_map('trim', explode(',', $sku_data->display_options));

                $ids = [];
                foreach ($options as $i => $opt) {
                    $id = $proOpts[$i]->options->where('value', $opt)->first()?->id;
                    array_push($ids, $id);
                }

                $sku_item->sku_product_options()->sync($ids);
            };
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return response()->json([
            "message" => "Product created.",
            "redirect" => route('admin.products.index')
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        $product->files()->detach();
        $product->categories()->sync([]);

        return redirect()->route('admin.products.index')->withSuccess('Product deleted.');
    }
}
