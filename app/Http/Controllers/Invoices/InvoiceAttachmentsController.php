<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Controllers\Controller;
use App\Models\Invoices\InvoiceAttachments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\In;

class InvoiceAttachmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $attachment = InvoiceAttachments::find($id);
        return response()->file(storage_path('app/public/'.$attachment->file_path));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InvoiceAttachments $attachment)
    {
        Storage::disk('public')->delete( $attachment->file_path );
        $attachment->delete();
        return redirect()->back()->with('success', 'تم حف المرفق بنجاح');
    }
    public function download(string $id)
    {
        $attachment = InvoiceAttachments::find($id);
        return response()->download(storage_path('app/public/' . $attachment->file_path));
    }
}
