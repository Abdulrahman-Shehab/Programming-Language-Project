<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Apartment;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.auth')->except(['showLoginForm', 'login']);
    }

    // صفحة تسجيل الدخول
    public function showLoginForm()
    {
        return view('admin.login');
    }

    // تسجيل الدخول
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required'
        ]);

        if ($request->phone === '0943701590' && $request->password === 'Asdf1234') {
            session(['admin_logged_in' => true]);
            return redirect()->route('admin.dashboard');
        }

        return redirect()->back()->withErrors(['message' => 'بيانات الدخول غير صحيحة']);
    }

    // تسجيل الخروج
    public function logout()
    {
        session()->forget('admin_logged_in');
        return redirect()->route('admin.login');
    }

    // لوحة التحكم الرئيسية
    public function dashboard()
    {
        $pendingUsers = User::where('status', 'pending')->count();
        $approvedUsers = User::where('status', 'approved')->count();
        $rejectedUsers = User::where('status', 'rejected')->count();
        $totalUsers = User::count();
        $totalApartments = Apartment::count();
        $totalGovernorates = Governorate::count();
        $totalCities = City::count();

        return view('admin.dashboard', compact(
            'pendingUsers',
            'approvedUsers',
            'rejectedUsers',
            'totalUsers',
            'totalApartments',
            'totalGovernorates',
            'totalCities'
        ));
    }

    // عرض المستخدمين المعلقين
    public function showPendingUsers()
    {
        $users = User::where('status', 'pending')->get();
        return view('admin.users.pending', compact('users'));
    }

    // عرض المستخدمين المقبولين
    public function showApprovedUsers()
    {
        $users = User::where('status', 'approved')->get();
        return view('admin.users.approved', compact('users'));
    }

    // عرض المستخدمين المرفوضين
    public function showRejectedUsers()
    {
        $users = User::where('status', 'rejected')->get();
        return view('admin.users.rejected', compact('users'));
    }

    // قبول مستخدم
    public function approveUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->status = 'approved';
            $user->save();
            return response()->json(['success' => true, 'message' => 'تم قبول المستخدم بنجاح']);
        }
        return response()->json(['success' => false, 'message' => 'المستخدم غير موجود']);
    }

    // رفض مستخدم
    public function rejectUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->status = 'rejected';
            $user->save();
            return response()->json(['success' => true, 'message' => 'تم رفض المستخدم بنجاح']);
        }
        return response()->json(['success' => false, 'message' => 'المستخدم غير موجود']);
    }

    // عرض جميع الشقق
    public function showApartments()
    {
        $apartments = Apartment::with(['user', 'governorate', 'city'])->get();
        return view('admin.apartments.index', compact('apartments'));
    }

    // حذف شقة
    public function deleteApartment($id)
    {
        $apartment = Apartment::find($id);
        if ($apartment) {
            $apartment->delete();
            return response()->json(['success' => true, 'message' => 'تم حذف الشقة بنجاح']);
        }
        return response()->json(['success' => false, 'message' => 'الشقة غير موجودة']);
    }

    // عرض المحافظات والمدن
    public function showLocations()
    {
        $governorates = Governorate::with('cities')->get();
        return view('admin.locations.index', compact('governorates'));
    }

    // إضافة محافظة
    public function addGovernorate(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:governorates,name'
        ]);

        Governorate::create([
            'name' => $request->name
        ]);

        return response()->json(['success' => true, 'message' => 'تم إضافة المحافظة بنجاح']);
    }

    // حذف محافظة
    public function deleteGovernorate($id)
    {
        $governorate = Governorate::find($id);
        if ($governorate) {
            $governorate->delete();
            return response()->json(['success' => true, 'message' => 'تم حذف المحافظة بنجاح']);
        }
        return response()->json(['success' => false, 'message' => 'المحافظة غير موجودة']);
    }

    // إضافة مدينة
    public function addCity(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'governorate_id' => 'required|exists:governorates,id'
        ]);

        City::create([
            'name' => $request->name,
            'governorate_id' => $request->governorate_id
        ]);

        return response()->json(['success' => true, 'message' => 'تم إضافة المدينة بنجاح']);
    }

    // حذف مدينة
    public function deleteCity($id)
    {
        $city = City::find($id);
        if ($city) {
            $city->delete();
            return response()->json(['success' => true, 'message' => 'تم حذف المدينة بنجاح']);
        }
        return response()->json(['success' => false, 'message' => 'المدينة غير موجودة']);
    }

    // حذف مستخدم
    public function deleteUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->status = 'approved'; // تغيير الحالة إلى approved قبل الحذف لتمكين التسجيل مرة أخرى
            $user->save();
            $user->delete();
            return response()->json(['success' => true, 'message' => 'تم حذف المستخدم بنجاح']);
        }
        return response()->json(['success' => false, 'message' => 'المستخدم غير موجود']);
    }

    // إضافة رصيد لمستخدم
    public function addFunds(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $user = User::find($id);
        if ($user) {
            $amount = $request->amount;

            // إنشاء أو تحديث المحفظة
            $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
            $wallet->balance += $amount;
            $wallet->save();

            return response()->json(['success' => true, 'message' => 'تم إضافة الرصيد بنجاح', 'new_balance' => $wallet->balance]);
        }
        return response()->json(['success' => false, 'message' => 'المستخدم غير موجود']);
    }

    // إعادة قبول مستخدم مرفوض
    public function reApproveUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->status = 'approved';
            $user->save();
            return response()->json(['success' => true, 'message' => 'تم إعادة قبول المستخدم بنجاح']);
        }
        return response()->json(['success' => false, 'message' => 'المستخدم غير موجود']);
    }
}
