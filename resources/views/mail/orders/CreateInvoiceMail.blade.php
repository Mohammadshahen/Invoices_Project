<x-mail::message>
# فاتورة جديدة

تم إنشاء فاتورة جديدة برقم: **{{ $invoice_id }}**

يمكنك عرض الفاتورة من خلال الرابط التالي:

<x-mail::button :url="$url_id">
عرض الفاتورة
</x-mail::button>

أو نسخ الرابط:  
{{ $url_id }}

شكراً لاستخدامك خدماتنا,  
{{ config('app.name') }}
</x-mail::message>