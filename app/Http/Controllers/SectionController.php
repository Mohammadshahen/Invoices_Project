<?php

namespace App\Http\Controllers;

use App\Models\section;
use Error;
use Illuminate\Contracts\Session;
use Illuminate\Http\Request;

use function Pest\Laravel\session;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = section::all();
        return view('sections.sections',compact('sections'));
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
            "section_name" => 'required|unique:sections',
            "description" => 'required'
        ],[
            'section_name.required' => 'يجب ادخال اسم القسم',
            'section_name.unique' =>'القسم موجود بالفعل',
            'description.required' =>'يجب ادخال وصف للقسم'
        ]);

        section::create([
            "section_name" => $request->section_name,
            "description" => $request->description,
        ]);
        return redirect("/sections")->with('success','تمت اضافة القسم');
    }

    /**
     * Display the specified resource.
     */
    public function show(section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            "section_name" => 'required|unique:sections,section_name,' . $id,
            "description" => 'required'
        ],[
            'section_name.required' => 'يجب ادخال اسم القسم',
            'section_name.unique' =>'القسم موجود بالفعل',
            'description.required' =>'يجب ادخال وصف للقسم'
        ]);

        $section = section::find($id);
        $section->update([
            'section_name' => $request->section_name,
            'description' => $request->description,
        ]);
        return redirect("/sections")->with('edit','تمت تعديل القسم');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $section = section::find($id);
        $section->delete();
        return redirect("/sections")->with('delete','تمت حذف القسم');        
    }
}
