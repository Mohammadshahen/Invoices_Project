@extends('layouts.master')
@section('css')
<!-- Internal Nice-select css -->
<link href="{{URL::asset('assets/plugins/jquery-nice-select/css/nice-select.css')}}" rel="stylesheet" />
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('title')
    تعديل مستخدم 
@stop

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">المستخدمين</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تعديل مستخدم</span>
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
                    <h4 class="card-title mb-0">تعديل مستخدم: {{ $user->name }}</h4>
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
                
                <form method="POST" action="{{ route('users.update', $user->id) }}" id="editUserForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>اسم المستخدم: <span class="text-danger">*</span></label>
                            <input type="text" name="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label>البريد الإلكتروني: <span class="text-danger">*</span></label>
                            <input type="email" name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>كلمة المرور: <span class="text-muted">(اتركه فارغاً إذا لم ترد التغيير)</span></label>
                            <input type="password" name="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="أدخل كلمة مرور جديدة">
                            <small class="form-text text-muted">يجب أن تكون 6 أحرف على الأقل</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label>تأكيد كلمة المرور:</label>
                            <input type="password" name="password_confirmation" 
                                   class="form-control" 
                                   placeholder="أعد إدخال كلمة المرور">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">حالة المستخدم</label>
                            <select name="status" class="form-control nice-select">
                                @php
                                    $currentStatus = old('status', $user->status ?? $user->Status ?? 'active');
                                @endphp
                                <option value="active" {{ $currentStatus == 'active' || $currentStatus == 'مفعل' ? 'selected' : '' }}>مفعل</option>
                                <option value="inactive" {{ $currentStatus == 'inactive' || $currentStatus == 'غير مفعل' ? 'selected' : '' }}>غير مفعل</option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الأدوار <span class="text-danger">*</span></label>
                            <select name="roles[]" class="form-control select2" multiple required>
                               @foreach($roles as $role)
                                    <option value="{{ $role->name }}" 
                                            {{ (old('roles') && in_array($role->name, old('roles'))) || in_array($role->name, $user_role) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('roles')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-save"></i> تحديث
                            </button>
                            <button type="reset" class="btn btn-secondary px-5">
                                <i class="fas fa-redo"></i> إعادة تعيين
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-danger px-5">
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
    $('.select2').select2({
        placeholder: "اختر الأدوار...",
        allowClear: true,
        language: {
            noResults: function() {
                return "لا توجد نتائج";
            }
        }
    });
    
    // تهيئة Nice Select
    {{-- $('.nice-select').niceSelect(); --}}
    
    // التحقق من كلمات المرور
    $('#editUserForm').on('submit', function(e) {
        const password = $('input[name="password"]').val();
        const confirmPassword = $('input[name="password_confirmation"]').val();
        const roles = $('select[name="roles[]"]').val();
        
        // إذا أدخل كلمة مرور، تحقق من التطابق
        if (password !== '' && password !== confirmPassword) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'كلمات المرور غير متطابقة'
            });
            return false;
        }
        
        // تحقق من الأدوار
        if (!roles || roles.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'الرجاء اختيار دور واحد على الأقل'
            });
            return false;
        }
        
        // تحقق من طول كلمة المرور إذا أدخلت
        if (password !== '' && password.length < 6) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'
            });
            return false;
        }
        
        return true;
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