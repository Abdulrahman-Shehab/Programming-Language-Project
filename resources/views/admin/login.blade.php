<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول الإدارة - نظام حجز الشقق</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }
        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="text-center mb-4">
            <h2>تسجيل دخول الإدارة</h2>
            <p>الرجاء إدخال معلومات الدخول</p>
        </div>

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="mb-3">
                <label for="phone" class="form-label">رقم الهاتف</label>
                <input type="text" class="form-control" id="phone" name="phone" value="0943701590" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">كلمة المرور</label>
                <input type="password" class="form-control" id="password" name="password" value="Asdf1234" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">تسجيل الدخول</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">المستخدم الافتراضي: 0943701590 / Asdf1234</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
