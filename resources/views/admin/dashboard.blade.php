@extends('admin.layout')

@section('title', 'الصفحة الرئيسية - لوحة التحكم')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">الصفحة الرئيسية</h1>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1 text-primary"></i>
                    <h5 class="card-title">إجمالي المستخدمين</h5>
                    <p class="display-6">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="bi bi-building fs-1 text-success"></i>
                    <h5 class="card-title">إجمالي الشقق</h5>
                    <p class="display-6">{{ $totalApartments }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="bi bi-person-plus fs-1 text-warning"></i>
                    <h5 class="card-title">المستخدمون المعلقون</h5>
                    <p class="display-6">{{ $pendingUsers }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="bi bi-geo-alt fs-1 text-info"></i>
                    <h5 class="card-title">إجمالي المحافظات</h5>
                    <p class="display-6">{{ $totalGovernorates }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Pending Users -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">أحدث طلبات التسجيل المعلقة</h5>
                </div>
                <div class="card-body">
                    <p class="text-center text-muted">جاري تحميل آخر المستخدمين المعلقين...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
