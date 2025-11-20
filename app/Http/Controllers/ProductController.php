<?php

namespace App\Http\Controllers;

use App\Models\product;
use App\Models\section;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = section::all();
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
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|unique:products',
            'section_id' => 'required|exists:sections,id'
        ],[
            'product_name.required' => 'يجب ادخال اسم المنتج',
            'product_name.unique' => 'المنتج موجود بالفعل',
            'section_id.required' => 'يجب تحديد القسم',
            'section_id.exists' => 'القسم غير موجود في قائمة الاقسام',
        ]);

        Product::create([
            'product_name' => $request->product_name,
            'description' => $request->description,
            'section_id' => $request->section_id,
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|unique:products,product_name,'. $id,
            'section_id' => 'required|exists:sections,id'
        ],[
            'product_name.required' => 'يجب ادخال اسم المنتج',
            'product_name.unique' => 'المنتج موجود بالفعل',
            'section_id.required' => 'يجب تحديد القسم',
            'section_id.exists' => 'القسم غير موجود في قائمة الاقسام',
        ]);

        $product = Product::find($id);
        $product->update([
            'product_name' => $request->product_name,
            'description' => $request->description,
            'section_id' => $request->section_id,
        ]);
        
        return redirect('/products')->with('edit','تمت تعديل المنتج');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,$id)
    {
        $product = Product::find($id);
        $product->delete();
        return redirect('/products')->with('delete','تمت حذف المنتج');
    }
}
