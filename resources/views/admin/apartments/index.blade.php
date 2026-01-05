@extends('admin.layout')

@section('title', 'إدارة الشقق')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إدارة الشقق</h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>العنوان</th>
                            <th>المالك</th>
                            <th>السعر اليومي</th>
                            <th>المحافظة</th>
                            <th>المدينة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apartments as $apartment)
                        <tr>
                            <td>{{ $apartment->id }}</td>
                            <td>{{ $apartment->title }}</td>
                            <td>{{ $apartment->user->first_name }} {{ $apartment->user->last_name }}</td>
                            <td>{{ $apartment->daily_price }} ل.س</td>
                            <td>{{ $apartment->governorate->name }}</td>
                            <td>{{ $apartment->city->name }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-apartment-btn" data-apartment-id="{{ $apartment->id }}">حذف</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">لا توجد شقق</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف هذه الشقة؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">حذف</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentApartmentId = null;

    document.querySelectorAll('.delete-apartment-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentApartmentId = this.getAttribute('data-apartment-id');
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (currentApartmentId) {
            fetch(`/admin/apartments/${currentApartmentId}/delete`, {
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
