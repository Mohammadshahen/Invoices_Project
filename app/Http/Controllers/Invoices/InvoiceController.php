<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Requests\Invoice\Status_UpdateInvoiceRequest;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Models\Invoices\Invoice;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends RoutingController
{
    public function __construct()
    {
        $this->middleware('permission:الفواتير', ['only' => ['index']]);
        $this->middleware('permission:اضافة فاتورة', ['only' => ['create','store']]);
        $this->middleware('permission:تعديل الفاتورة', ['only' => ['edit','update']]);
        $this->middleware('permission:حذف الفاتورة', ['only' => ['destroy']]);
        $this->middleware('permission:تغير حالة الدفع', ['only' => ['Status_show','Status_update']]);
        $this->middleware('permission:ارشيف الفواتير', ['only' => ['archive','restore','force_destroy']]);
        $this->middleware('permission:طباعةالفاتورة', ['only' => ['printInvoice']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = Section::all();
        $invoices = Invoice::all();
        return view('invoices.invoices',compact('invoices'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = Section::all();
        return view('invoices.add_invoice',compact('sections'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        // بدء Transaction
        DB::beginTransaction();
        
        try {
            // الحصول على البيانات المفحوصة
            $validatedData = $request->validated();
            
            // إنشاء الفاتورة
            $invoice = Invoice::create([
                'invoice_number' => $validatedData['invoice_number'],
                'invoice_date' => $validatedData['invoice_date'],
                'due_date' => $validatedData['due_date'],
                'product_id' => $validatedData['product_id'],
                'section_id' => $validatedData['section_id'],
                'amount_collection' => $validatedData['amount_collection'],
                'amount_commission' => $validatedData['amount_commission'],
                'discount' => $validatedData['discount'],
                'value_vat' => $validatedData['value_vat'],
                'rate_vat' => $validatedData['rate_vat'],
                'total' => $validatedData['total'],
                'status' => 'غير مدفوعة',
                'value_status' => 2,
                'note' => $validatedData['note'] ?? null,
            ]);
            
            // إنشاء تفاصيل الفاتورة باستخدام علاقة invoice_id الصحيحة
            $invoice->detail()->create([
                'invoice_id' => $invoice->id,
                'product_id' => $validatedData['product_id'],
                'section_id' => $validatedData['section_id'],
                'status' => 'غير مدفوعة',
                'value_status' => 2,
                'note' => $validatedData['note'] ?? null,
                'date' => now(),
            ]);
            
            // حفظ المرفقات إذا وجدت
            if ($request->hasFile('pic')) {
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
            }
            
            // تأكيد Transaction إذا نجحت جميع العمليات
            DB::commit();
            
           
            return redirect()->route('invoices.index')
                ->with('success', 'تم إضافة الفاتورة رقم ' . $invoice->invoice_number . ' بنجاح');
                
        } catch (\Exception $e) {
            // التراجع عن جميع العمليات في حالة الخطأ
            DB::rollBack();
            
            
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء حفظ الفاتورة. الرجاء المحاولة مرة أخرى.');
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
    public function edit(Invoice $invoice)
    {
        $sections = Section::all();
        return view('invoices.update_invoice',compact('invoice','sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        try{
            $data = $request->validated();
            $invoice->update([
                'invoice_number' => $data['invoice_number'],
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
                'product_id' => $data['product_id'] ?? $invoice->product_id,
                'section_id' => $data['section_id'] ?? $invoice->section_id,
                'amount_collection' => $data['amount_collection'],
                'amount_commission' => $data['amount_commission'],
                'discount' => $data['discount'],
                'value_vat' => $data['value_vat'],
                'rate_vat' => $data['rate_vat'],
                'total' => $data['total'],
                'note' => $data['note'] ?? null,
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
        return redirect()->back()->with('success','تم حذف الفاتورة بنجاح');
    }


    public function getProducts($section_id){
        $products = Product::where('section_id',$section_id)->get();
        return response()->json($products);
    }

    public function Status_show(Invoice $invoice){
        return view('invoices.status_update',compact('invoice')); 
    }

    public function Status_update(Status_UpdateInvoiceRequest $request, Invoice $invoice){
        try{
            $data = $request->validated();
            DB::beginTransaction();

            if($data['status'] == 'مدفوعة'){
                $value_status =1;
            }elseif($data['status'] == 'مدفوعة جزئيا'){
                $value_status =3;
            }else{
                $value_status =2;
            }
            $invoice->update([
                'status' => $data['status'],
                'value_status' => $value_status,
                'payment_date' => $data['payment_date'],
            ]);
            $invoice->detail()->create([
                'invoice_id' => $invoice->id,
                'product_id' => $invoice->product_id,
                'section_id' => $invoice->section_id,
                'status' => $data['status'],
                'value_status' => $value_status,
                'payment_date' => $data['payment_date'],
                'note' => $data['note'] ?? null,
            ]);
            DB::commit();

            return redirect()->route('invoices.index')->with('success','تم تحديث حالة الفاتورة بنجاح');

        }catch(\Exception $e){
            DB::rollBack();
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

    public function invoicesPartial(){
        $invoices = Invoice::where('value_status','3')->get();
        return view('invoices.invoices_partial',compact('invoices'));
    }
    public function invoicePaid(){
        $invoices = Invoice::where('value_status','1')->get();
        return view('invoices.invoices_paid',compact('invoices'));
    }
    public function invoiceUnPaid(){
        $invoices = Invoice::where('value_status','2')->get();
        return view('invoices.invoices_unpaid',compact('invoices'));
    }




}
