@extends('admin.layout')

@section('title', 'إدارة المواقع')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إدارة المواقع</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">إضافة محافظة جديدة</h5>
                </div>
                <div class="card-body">
                    <form id="addGovernorateForm">
                        @csrf
                        <div class="mb-3">
                            <label for="governorateName" class="form-label">اسم المحافظة</label>
                            <input type="text" class="form-control" id="governorateName" name="name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">إضافة محافظة</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">إضافة مدينة جديدة</h5>
                </div>
                <div class="card-body">
                    <form id="addCityForm">
                        @csrf
                        <div class="mb-3">
                            <label for="cityName" class="form-label">اسم المدينة</label>
                            <input type="text" class="form-control" id="cityName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="governorateSelect" class="form-label">المحافظة</label>
                            <select class="form-control" id="governorateSelect" name="governorate_id" required>
                                <option value="">اختر محافظة</option>
                                @foreach($governorates as $governorate)
                                <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">إضافة مدينة</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>المحافظة</th>
                            <th>المدن</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($governorates as $governorate)
                        <tr>
                            <td>{{ $governorate->id }}</td>
                            <td>{{ $governorate->name }}</td>
                            <td>
                                @if($governorate->cities->count() > 0)
                                    <ul class="list-unstyled mb-0">
                                        @foreach($governorate->cities as $city)
                                        <li>
                                            {{ $city->name }}
                                            <button class="btn btn-sm btn-danger ms-2 delete-city-btn" data-city-id="{{ $city->id }}">حذف</button>
                                        </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">لا توجد مدن</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-governorate-btn" data-governorate-id="{{ $governorate->id }}">حذف</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">لا توجد محافظات</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Governorate Modal -->
<div class="modal fade" id="deleteGovernorateModal" tabindex="-1" aria-labelledby="deleteGovernorateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteGovernorateModalLabel">تأكيد حذف المحافظة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف هذه المحافظة؟ سيتم حذف جميع المدن المرتبطة بها.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirmGovernorateDelete">حذف</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete City Modal -->
<div class="modal fade" id="deleteCityModal" tabindex="-1" aria-labelledby="deleteCityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCityModalLabel">تأكيد حذف المدينة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف هذه المدينة؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirmCityDelete">حذف</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentGovernorateId = null;
    let currentCityId = null;

    // Add Governorate
    document.getElementById('addGovernorateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('governorateName').value;

        fetch('/admin/governorates', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            alert('حدث خطأ في الاتصال');
        });
    });

    // Add City
    document.getElementById('addCityForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('cityName').value;
        const governorateId = document.getElementById('governorateSelect').value;

        fetch('/admin/cities', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ name: name, governorate_id: governorateId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            alert('حدث خطأ في الاتصال');
        });
    });

    // Delete Governorate
    document.querySelectorAll('.delete-governorate-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentGovernorateId = this.getAttribute('data-governorate-id');
            const modal = new bootstrap.Modal(document.getElementById('deleteGovernorateModal'));
            modal.show();
        });
    });

    document.getElementById('confirmGovernorateDelete').addEventListener('click', function() {
        if (currentGovernorateId) {
            fetch(`/admin/governorates/${currentGovernorateId}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('حدث خطأ: ' + data.message);
                }
            })
            .catch(error => {
                alert('حدث خطأ في الاتصال');
            });
        }
    });

    // Delete City
    document.querySelectorAll('.delete-city-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentCityId = this.getAttribute('data-city-id');
            const modal = new bootstrap.Modal(document.getElementById('deleteCityModal'));
            modal.show();
        });
    });

    document.getElementById('confirmCityDelete').addEventListener('click', function() {
        if (currentCityId) {
            fetch(`/admin/cities/${currentCityId}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('حدث خطأ: ' + data.message);
                }
            })
            .catch(error => {
                alert('حدث خطأ في الاتصال');
            });
        }
    });
</script>
@endsection
