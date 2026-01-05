@extends('admin.layout')

@section('title', 'المستخدمون المرفوضون')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">المستخدمون المرفوضون</h1>
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
                                <button class="btn btn-success btn-sm re-approve-btn" data-user-id="{{ $user->id }}">إعادة القبول</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">لا يوجد مستخدمون مرفوضون</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Re-approve Modal -->
<div class="modal fade" id="reApproveModal" tabindex="-1" aria-labelledby="reApproveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reApproveModalLabel">تأكيد إعادة القبول</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من إعادة قبول هذا المستخدم؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success" id="confirmReApprove">إعادة القبول</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentUserId = null;

    document.querySelectorAll('.re-approve-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentUserId = this.getAttribute('data-user-id');
            const modal = new bootstrap.Modal(document.getElementById('reApproveModal'));
            modal.show();
        });
    });

    document.getElementById('confirmReApprove').addEventListener('click', function() {
        if (currentUserId) {
            fetch(`/admin/users/${currentUserId}/re-approve`, {
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
