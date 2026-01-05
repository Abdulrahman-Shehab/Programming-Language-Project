@extends('admin.layout')

@section('title', 'المستخدمون المقبولون')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">المستخدمون المقبولون</h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم الكامل</th>
                            <th>رقم الهاتف</th>
                            <th>الرصيد</th>
                            <th>تاريخ الميلاد</th>
                            <th>تاريخ التسجيل</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->wallet ? $user->wallet->balance : 0 }} ل.س</td>
                            <td>{{ $user->birth_date->format('Y-m-d') }}</td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-btn" data-user-id="{{ $user->id }}">حذف</button>
                                <button class="btn btn-success btn-sm add-funds-btn" data-user-id="{{ $user->id }}">إضافة رصيد</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">لا يوجد مستخدمون مقبولون</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف هذا المستخدم؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">حذف</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Funds Modal -->
<div class="modal fade" id="fundsModal" tabindex="-1" aria-labelledby="fundsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fundsModalLabel">إضافة رصيد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="amount" class="form-label">المبلغ</label>
                    <input type="number" class="form-control" id="amount" min="0" step="1000">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success" id="confirmFunds">إضافة</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentUserId = null;

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentUserId = this.getAttribute('data-user-id');
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (currentUserId) {
            fetch(`/admin/users/${currentUserId}/delete`, {
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

    document.querySelectorAll('.add-funds-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentUserId = this.getAttribute('data-user-id');
            document.getElementById('amount').value = '';
            const modal = new bootstrap.Modal(document.getElementById('fundsModal'));
            modal.show();
        });
    });

    document.getElementById('confirmFunds').addEventListener('click', function() {
        if (currentUserId) {
            const amount = document.getElementById('amount').value;
            if (amount && amount > 0) {
                fetch(`/admin/users/${currentUserId}/add-funds`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ amount: parseFloat(amount) })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message + '\\nالرصيد الجديد: ' + data.new_balance + ' ل.س');
                        location.reload();
                    } else {
                        alert('حدث خطأ: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('حدث خطأ في الاتصال');
                });
            } else {
                alert('الرجاء إدخال مبلغ صحيح');
            }
        }
    });
</script>
@endsection
