<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Controllers\Controller;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceAttachments;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\In;

class InvoiceAttachmentsController extends RoutingController
{
    public function __construct()
    {
        $this->middleware('permission:اضافة مرفق', ['only' => ['store']]);
        $this->middleware('permission:حذف المرفق', ['only' => ['destroy']]);
    }
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
        // return $request;
        $invoice = Invoice::findOrFail($request->invoice_id);
         $file = $request->file('pic');
                $originalName = $file->getClientOriginalName();
                
                // إنشاء اسم فريد للملف
                $fileName = time() . '_' . str_replace(' ', '_', $originalName);
                
                // حفظ الملف
                $path = $file->storeAs('Attachment/' . $invoice->id, $fileName, 'public');
                
                // إنشاء سجل المرفق
                $invoice->attachment()->create([
                    'file_path' => $path,
                ]);
        return redirect()->back()->with('success', 'تم اضافة المرفق بنجاح');
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
    public function destroy(string $id)
    {
        $attachment = InvoiceAttachments::find($id);
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
