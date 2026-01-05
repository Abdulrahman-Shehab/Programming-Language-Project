@extends('admin.layout')

@section('title', 'المستخدمون المعلقون')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">المستخدمون المعلقون</h1>
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
                                <button class="btn btn-success btn-sm me-2 approve-btn" data-user-id="{{ $user->id }}">قبول</button>
                                <button class="btn btn-danger btn-sm reject-btn" data-user-id="{{ $user->id }}">رفض</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">لا يوجد مستخدمون معلقون</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">تأكيد العملية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من تنفيذ هذه العملية؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="confirmAction">تأكيد</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentUserId = null;
    let currentAction = null;

    document.querySelectorAll('.approve-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentUserId = this.getAttribute('data-user-id');
            currentAction = 'approve';
            document.getElementById('confirmModalLabel').textContent = 'تأكيد القبول';
            document.querySelector('.modal-body').textContent = 'هل أنت متأكد من قبول هذا المستخدم؟';
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        });
    });

    document.querySelectorAll('.reject-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentUserId = this.getAttribute('data-user-id');
            currentAction = 'reject';
            document.getElementById('confirmModalLabel').textContent = 'تأكيد الرفض';
            document.querySelector('.modal-body').textContent = 'هل أنت متأكد من رفض هذا المستخدم؟';
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        });
    });

    document.getElementById('confirmAction').addEventListener('click', function() {
        if (currentUserId && currentAction) {
            fetch(`/admin/users/${currentUserId}/${currentAction}`, {
                method: 'POST',
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
