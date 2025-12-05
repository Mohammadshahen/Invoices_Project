<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:المنتجات',['only' => ['index']]);
        $this->middleware('permission:اضافة منتج',['only' => ['store']]);
        $this->middleware('permission:تعديل منتج',['only' => ['update']]);
        $this->middleware('permission:حذف منتج',['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = Section::all();
        $products = Product::all();
        return view("products.products" , compact('sections','products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        Product::create([
            'product_name' => $data['product_name'],
            'description' => $data['description'],
            'section_id' => $data['section_id'],
        ]);
        
        return redirect('/products')->with('success','تمت اضافة المنتج');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request,Product $product)
    {
        $date = $request->validated();
        $product->update([
            'product_name' => $date['product_name'],
            'description' => $date['description'],
            'section_id' => $date['section_id'],
        ]);
        return redirect('/products')->with('edit','تمت تعديل المنتج');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect('/products')->with('delete','تمت حذف المنتج');
    }
}
