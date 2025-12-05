@extends('layouts.master')
@section('css')
<!--Internal  Font Awesome -->
<link href="{{URL::asset('assets/plugins/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
<!--Internal  treeview -->
<link href="{{URL::asset('assets/plugins/treeview/treeview-rtl.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('title')
تعديل الصلاحيات  
@stop

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الصلاحيات</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تعديل الصلاحيات</span>
        </div>
    </div>
</div>
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
                    <h3>تعديل الصلاحية: {{ $role->name }}</h3>
                </div>
                
                <form method="POST" action="{{ route('roles.update', $role->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">اسم الصلاحية</label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $role->name) }}" 
                               placeholder="أدخل اسم الصلاحية" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">الصلاحيات</h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label mr-3" for="selectAll">تحديد الكل</label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($permission as $value)
                                        <div class="col-md-3 mb-2">
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input permission-checkbox mr-2" 
                                                       type="checkbox" 
                                                       name="permission[]" 
                                                       value="{{ $value->name }}" 
                                                       id="permission_{{ $value->id }}"
                                                       {{ in_array($value->name, $role_permissions) ? 'checked' : '' }}>
                                                <label class="form-check-label mt-1 mr-4" for="permission_{{ $value->id }}">
                                                    {{ $value->name }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @error('permission')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save"></i> تحديث
                            </button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary px-4">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{URL::asset('assets/plugins/treeview/treeview.js')}}"></script>
<script>
$(document).ready(function() {
    // تحديد/إلغاء تحديد الكل
    $('#selectAll').on('change', function() {
        $('.permission-checkbox').prop('checked', this.checked);
        updateSelectAllButton();
    });
    
    // تحديث زر تحديد الكل عند تغيير الصلاحيات
    $('.permission-checkbox').on('change', function() {
        updateSelectAllButton();
    });
    
    // دالة لتحديث حالة زر تحديد الكل
    function updateSelectAllButton() {
        const total = $('.permission-checkbox').length;
        const checked = $('.permission-checkbox:checked').length;
        $('#selectAll').prop('checked', total === checked);
    }
    
    // التهيئة الأولية
    updateSelectAllButton();
});
</script>
@endsection