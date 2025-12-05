@extends('layouts.master')
@section('css')
<!--Internal  Font Awesome -->
<link href="{{URL::asset('assets/plugins/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
<!--Internal  treeview -->
<link href="{{URL::asset('assets/plugins/treeview/treeview-rtl.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('title')
اضافة الصلاحيات 
@stop

@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الصلاحيات</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">
                / اضافة نوع مستخدم
            </span>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection

@section('content')

@if (count($errors) > 0)
<div class="alert alert-danger">
    <button aria-label="Close" class="close" data-dismiss="alert" type="button">
        <span aria-hidden="true">&times;</span>
    </button>
    <strong>خطأ</strong>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- row -->
<div class="row">
    <div class="col-md-12">
        <div class="card mg-b-20">
            <div class="card-body">
                <div class="main-content-label mg-b-5">
                    <h3>اضافة صلاحية جديدة</h3>
                </div>
                
                <form action="{{ route('roles.store') }}" method="POST" id="roleForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">اسم الدور / الصلاحية</label>
                                <input type="text" name="name" id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" 
                                       placeholder="أدخل اسم الصلاحية" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">الصلاحيات</h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label mt-0 mr-3" for="selectAll">تحديد الكل</label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($permission as $value)
                                        <div class="col-md-3 mb-3">
                                            <div class="d-flex align-items-center">
                                                <input class="form-check-input permission-checkbox" 
                                                    type="checkbox" 
                                                    name="permission[]" 
                                                    value="{{ $value->name }}" 
                                                    id="permission_{{ $value->id }}"
                                                    style="flex-shrink: 0;">
                                                <label class="form-check-label mt-1 mr-3" for="permission_{{ $value->id }}">
                                                    {{ $value->name }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @error('permission')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ
                            </button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- row closed -->
@endsection

@section('js')
<script src="{{URL::asset('assets/plugins/treeview/treeview.js')}}"></script>
<script>
$(document).ready(function() {
    // تحديد/إلغاء تحديد الكل
    $('#selectAll').on('change', function() {
        $('.permission-checkbox').prop('checked', this.checked);
    });
    
    // إذا تم تحديد كل الصلاحيات يدوياً، حدد "تحديد الكل"
    $('.permission-checkbox').on('change', function() {
        const total = $('.permission-checkbox').length;
        const checked = $('.permission-checkbox:checked').length;
        $('#selectAll').prop('checked', total === checked);
    });
    
    // تحقق قبل الإرسال
    $('#roleForm').on('submit', function(e) {
        const name = $('#name').val().trim();
        const checkedPermissions = $('input[name="permission[]"]:checked').length;
        
        if (name === '') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'الرجاء إدخال اسم الدور'
            });
            return false;
        }
        
        if (checkedPermissions === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'الرجاء اختيار صلاحية واحدة على الأقل'
            });
            return false;
        }
        
        return true;
    });
});
</script>

@if(session('success'))
<script>
    $(document).ready(function() {
        Swal.fire({
            icon: 'success',
            title: 'نجاح',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    });
</script>
@endif

@if(session('error'))
<script>
    $(document).ready(function() {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: '{{ session('error') }}'
        });
    });
</script>
@endif
@endsection