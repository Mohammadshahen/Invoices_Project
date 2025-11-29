<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Mail\InvoiceMail;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceAttachments;
use App\Models\Invoices\InvoiceDetail;
use App\Models\Product;
use App\Models\section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\In;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = section::all();
        $invoices = Invoice::all();
        return view('invoices.invoices',compact('invoices'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = section::all();
        return view('invoices.add_invoice',compact('sections'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        
        try{
        $invoice = Invoice::create([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'product_id' => $request->product_id,
            'section_id' => $request->section_id,
            'amount_collection' => $request->amount_collection,
            'amount_commission' => $request->amount_commission,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'rate_vat' => $request->rate_vat,
            'total' => $request->total,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
        ]);

        $invoice_id = Invoice::latest()->first()->id;
        $invoice->detail()->create([
            'product_id' => $request->product_id,
            'section_id' => $request->section_id,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
        ]);

        if ($request->hasFile('pic')) {

            $file_name = $request->file('pic')->getClientOriginalName();
            $path = $request->file('pic')->storeAs('Attachment/' . $invoice_id ,$file_name,'public');
            
            $invoice->attachment()->create([
                'file_path' => $path,
            ]);
        }

        return redirect()->route('invoices.index')->with('success','تم اضافة الفاتورة بنجاح');
    }catch(\Exception $e){
        return redirect()->back()->with('error','حدث خطأ ما يرجى المحاولة لاحقا' . $e->getMessage() );
    }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {    
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $sections = section::all();
        return view('invoices.update_invoice',compact('invoice','sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreInvoiceRequest $request, Invoice $invoice)
    {
        try{
        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'product_id' => $request->product_id ?? $invoice->product_id,
            'section_id' => $request->section_id ?? $invoice->section_id,
            'amount_collection' => $request->amount_collection,
            'amount_commission' => $request->amount_commission,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'rate_vat' => $request->rate_vat,
            'total' => $request->total,
            'note' => $request->note,
        ]);

        return redirect()->route('invoices.index')->with('success','تم تعديل الفاتورة بنجاح');
    }catch(\Exception $e){
        return redirect()->back()->with('error', $e->getMessage() );
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success','تم حذف الفاتورة بنجاح');
    }

    public function getProducts($section_id){
        $products = Product::where('section_id',$section_id)->get();
        return response()->json($products);
    }

    public function Status_show(Invoice $invoice){
        return view('invoices.status_update',compact('invoice')); 
    }

    public function Status_update(Request $request, Invoice $invoice){
        try{

        if($request->status == 'مدفوعة'){
            $value_status =1;
        }elseif($request->status == 'مدفوعة جزئيا'){
            $value_status =3;
        }else{
            $value_status =2;
        }

        $invoice->update([
            'status' => $request->status,
            'value_status' => $value_status,
            'payment_date' => $request->payment_date,
        ]);

        $invoice->detail()->create([
            'invoice_id' => $invoice->id,
            'product_id' => $invoice->product_id,
            'section_id' => $invoice->section_id,
            'status' => $request->status,
            'value_status' => $value_status,
            'payment_date' => $request->payment_date,
            'note' => $request->note,
        ]);

        return redirect()->route('invoices.index')->with('success','تم تحديث حالة الفاتورة بنجاح');

        }catch(\Exception $e){
            return redirect()->back()->with('error', $e->getMessage() );
        }
    }

    public function archive(){
        $invoices = Invoice::onlyTrashed()->get();
        return view('invoices.archive_Invoices',compact('invoices'));
    }

    public function force_destroy(string $id){
        // return $id;
        $invoice = Invoice::withTrashed()->where('id',$id)->first();
        if( $invoice->attachment ){
            foreach ( $invoice->attachment as $attachment ) {
                Storage::disk('public')->delete( $attachment->file_path );
            }
        }
        $invoice->forceDelete();
        return redirect()->route('archive')->with('success','تم حذف الفاتورة نهائيا بنجاح');
    }

    public function restore(string $id){
        $invoice = Invoice::withTrashed()->where('id',$id)->first();
        $invoice->restore();
        return redirect()->route('archive')->with('success','تم استعادة الفاتورة بنجاح');
    }
    public function printInvoice(Invoice $invoice){
        return view('invoices.print_invoice',compact('invoice'));
    }




}
