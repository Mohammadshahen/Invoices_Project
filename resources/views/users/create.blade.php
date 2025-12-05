@extends('layouts.master')
@section('css')
<!-- Internal Nice-select css -->
<link href="{{URL::asset('assets/plugins/jquery-nice-select/css/nice-select.css')}}" rel="stylesheet" />
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d1d9e6;
        min-height: 38px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #0162e8;
    }
</style>
@endsection

@section('title')
    اضافة مستخدم جديد 
@stop

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">المستخدمين</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ اضافة مستخدم جديد</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">إضافة مستخدم جديد</h4>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>خطأ في البيانات:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <form method="POST" action="{{ route('users.store') }}" id="createUserForm">
                    @csrf
                    
                    <div class="row">
                        <!-- البيانات الأساسية -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">اسم المستخدم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="أدخل الاسم الكامل" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="example@domain.com" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- كلمة المرور -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" 
                                       placeholder="أدخل كلمة المرور (6 أحرف على الأقل)" 
                                       minlength="6" 
                                       required>
                                <small class="form-text text-muted">يجب أن تكون كلمة المرور 8 أحرف على الأقل</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" 
                                       placeholder="أعد إدخال كلمة المرور" 
                                       required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- الحالة والأدوار -->
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">حالة المستخدم</label>
                            <select class="form-control nice-select" id="status" name="status"> <!-- تغيير name إلى status -->
                                <option value="active" {{ old('status') == 'مفعل' ? 'selected' : '' }}>مفعل</option>
                                <option value=" inactive" {{ old('status') == 'غير مفعل' ? 'selected' : '' }}>غير مفعل</option>
                            </select>
                        </div>
                    </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="roles">الأدوار <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="roles" name="roles[]" multiple required>
                                    @foreach($roles as $value)
                                        <option value="{{ $value->name }}" >
                                            {{ $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">يمكنك اختيار أكثر من دور</small>
                                @error('roles')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                   
                    <!-- أزرار الإجراءات -->
                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-user-plus mr-1"></i> إنشاء المستخدم
                            </button>
                            <button type="reset" class="btn btn-secondary px-5">
                                <i class="fas fa-redo mr-1"></i> إعادة تعيين
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-danger px-5">
                                <i class="fas fa-times mr-1"></i> إلغاء
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
<!-- Internal Nice-select js-->
<script src="{{URL::asset('assets/plugins/jquery-nice-select/js/jquery.nice-select.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery-nice-select/js/nice-select.js')}}"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // تهيئة Select2 للأدوار
    $('#roles').select2({
        placeholder: "اختر الأدوار...",
        allowClear: true,
        language: {
            noResults: function() {
                return "لا توجد نتائج";
            },
            searching: function() {
                return "جاري البحث...";
            }
        }
    });
    
    // تهيئة Nice Select
    {{-- $('.nice-select').niceSelect(); --}}
    
    // التحقق من كلمات المرور
    $('#password_confirmation').on('keyup', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();
        
        if (password !== confirmPassword && confirmPassword !== '') {
            $(this).addClass('is-invalid').removeClass('is-valid');
            $(this).parent().append('<div class="invalid-feedback">كلمات المرور غير متطابقة</div>');
        } else if (confirmPassword !== '') {
            $(this).removeClass('is-invalid').addClass('is-valid');
            $(this).parent().find('.invalid-feedback').remove();
        } else {
            $(this).removeClass('is-invalid is-valid');
            $(this).parent().find('.invalid-feedback').remove();
        }
    });
    
    // التحقق من النموذج قبل الإرسال
    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        
        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const confirmPassword = $('#password_confirmation').val();
        const roles = $('#roles').val();
        
        let errors = [];
        
        // التحقق من الحقول المطلوبة
        if (!name) errors.push('الاسم مطلوب');
        if (!email) errors.push('البريد الإلكتروني مطلوب');
        if (!password) errors.push('كلمة المرور مطلوبة');
        if (password.length < 6) errors.push('كلمة المرور يجب أن تكون 8 أحرف على الأقل');
        if (password !== confirmPassword) errors.push('كلمات المرور غير متطابقة');
        if (!roles || roles.length === 0) errors.push('يجب اختيار دور واحد على الأقل');
        
        if (errors.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ في البيانات',
                html: '<ul class="text-right"><li>' + errors.join('</li><li>') + '</li></ul>',
                confirmButtonText: 'حسنًا'
            });
            return false;
        }
        
        // إذا كل شيء صحيح، أرسل النموذج
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم إنشاء مستخدم جديد',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، أنشئ المستخدم',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
    
    // إظهار/إخفاء كلمة المرور
    $('.toggle-password').on('click', function() {
        const input = $(this).parent().find('input');
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'نجاح',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'خطأ',
        text: '{{ session('error') }}'
    });
</script>
@endif
@endsection