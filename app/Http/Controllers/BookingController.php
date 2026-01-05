<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Apartment;
use App\Models\Wallet;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function createBooking(StoreBookingRequest $request, $apartmentId)
    {
        $apartment = Apartment::findOrFail($apartmentId);

        // Check availability
        $existingBooking = Booking::where('apartment_id', $apartmentId)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function ($query) use ($request) {
                          $query->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                      });
            })
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingBooking) {
            return response()->json([
                'message' => 'الشقة محجوزة من ' . $existingBooking->start_date->format('Y-m-d') . ' إلى ' . $existingBooking->end_date->format('Y-m-d'),
                'existing_booking' => [
                    'start_date' => $existingBooking->start_date->format('Y-m-d'),
                    'end_date' => $existingBooking->end_date->format('Y-m-d'),
                    'status' => $existingBooking->status
                ]
            ], 400);
        }

        // Calculate total amount
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $days = $startDate->diffInDays($endDate) + 1;
        $totalAmount = $days * $apartment->daily_price;

        // Check if user has enough balance
        $userWallet = Wallet::where('user_id', $request->user()->id)->first();
        if (!$userWallet || $userWallet->balance < $totalAmount) {
            return response()->json(['message' => 'لا يوجد رصيد كافي في المحفظة'], 400);
        }

        // Check if the apartment belongs to the user trying to book it
        if ($apartment->user_id === $request->user()->id) {
            return response()->json(['message' => 'لا يمكنك حجز شقتك الخاصة'], 400);
        }

        // Create booking
        $booking = new Booking();
        $booking->user_id = $request->user()->id;
        $booking->apartment_id = $apartmentId;
        $booking->start_date = $request->start_date;
        $booking->end_date = $request->end_date;
        $booking->amount = $totalAmount;
        $booking->status = 'pending';
        $booking->save();

        // Deduct amount from user's wallet
        $userWallet->balance -= $totalAmount;
        $userWallet->save();

        return response()->json([
            'message' => 'تم إنشاء الحجز بنجاح',
            'booking' => new BookingResource($booking)
        ], 201);
    }

    public function modifyBooking(UpdateBookingRequest $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Check if the authenticated user is the owner of the booking
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك بتعديل هذا الحجز'], 403);
        }

        // Check if booking is confirmed - if so, user can't modify it
        if ($booking->status === 'confirmed') {
            return response()->json(['message' => 'لا يمكن تعديل الحجز بعد تأكيده من قبل المالك'], 400);
        }

        // Check availability for new dates
        $existingBooking = Booking::where('apartment_id', $booking->apartment_id)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function ($query) use ($request) {
                          $query->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                      });
            })
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingBooking) {
            return response()->json([
                'message' => 'الشقة محجوزة من ' . $existingBooking->start_date->format('Y-m-d') . ' إلى ' . $existingBooking->end_date->format('Y-m-d'),
                'existing_booking' => [
                    'start_date' => $existingBooking->start_date->format('Y-m-d'),
                    'end_date' => $existingBooking->end_date->format('Y-m-d'),
                    'status' => $existingBooking->status
                ]
            ], 400);
        }

        // Calculate new total amount
        $apartment = Apartment::find($booking->apartment_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $days = $startDate->diffInDays($endDate) + 1;
        $newTotalAmount = $days * $apartment->daily_price;

        // Refund old amount to user's wallet
        $userWallet = Wallet::where('user_id', $request->user()->id)->first();
        $userWallet->balance += $booking->amount;
        $userWallet->save();

        // Check if user has enough balance for new booking
        if ($userWallet->balance < $newTotalAmount) {
            // Restore the old balance
            $userWallet->balance -= $booking->amount;
            $userWallet->save();
            return response()->json(['message' => 'لا يوجد رصيد كافي في المحفظة'], 400);
        }

        // Deduct new amount from user's wallet
        $userWallet->balance -= $newTotalAmount;
        $userWallet->save();

        // Update booking
        $booking->start_date = $request->start_date;
        $booking->end_date = $request->end_date;
        $booking->amount = $newTotalAmount;
        $booking->save();

        return response()->json([
            'message' => 'تم تعديل الحجز بنجاح',
            'booking' => new BookingResource($booking)
        ]);
    }

    public function cancelBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Check if the authenticated user is the owner of the booking
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك بإلغاء هذا الحجز'], 403);
        }

        // Check if booking is confirmed - if so, user can't cancel it
        if ($booking->status === 'confirmed') {
            return response()->json(['message' => 'لا يمكن إلغاء الحجز بعد تأكيده من قبل المالك'], 400);
        }

        // Refund amount to user's wallet
        $userWallet = Wallet::where('user_id', $request->user()->id)->first();
        $userWallet->balance += $booking->total_amount;
        $userWallet->save();

        // Update booking status
        $booking->status = 'cancelled';
        $booking->save();

        return response()->json(['message' => 'تم إلغاء الحجز بنجاح']);
    }

    public function confirmBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Check if the authenticated user is the owner of the apartment
        if ($booking->apartment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك بتأكيد هذا الحجز'], 403);
        }

        // Check if booking is already confirmed or rejected
        if (in_array($booking->status, ['confirmed', 'rejected', 'cancelled', 'completed'])) {
            return response()->json(['message' => 'لا يمكن تأكيد هذا الحجز'], 400);
        }

        // Transfer amount from tenant's wallet to owner's wallet
        $tenantWallet = Wallet::where('user_id', $booking->user_id)->first();
        $ownerWallet = Wallet::where('user_id', $booking->apartment->user_id)->first();

        // Create owner wallet if it doesn't exist
        if (!$ownerWallet) {
            $ownerWallet = Wallet::create([
                'user_id' => $booking->apartment->user_id,
                'balance' => 0
            ]);
        }

        // Transfer the amount
        $ownerWallet->balance += $booking->amount;
        $ownerWallet->save();

        $booking->status = 'confirmed';
        $booking->save();

        return response()->json([
            'message' => 'تم تأكيد الحجز بنجاح ونقل الأجرة إلى محفظة المالك',
            'booking' => new BookingResource($booking)
        ]);
    }

    public function rejectBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Check if the authenticated user is the owner of the apartment
        if ($booking->apartment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك برفض هذا الحجز'], 403);
        }

        // Check if booking is already confirmed or rejected
        if (in_array($booking->status, ['confirmed', 'rejected', 'cancelled', 'completed'])) {
            return response()->json(['message' => 'لا يمكن رفض هذا الحجز'], 400);
        }

        // Refund amount to user's wallet
        $userWallet = Wallet::where('user_id', $request->user()->id)->first();
        $userWallet->balance += $booking->amount;
        $userWallet->save();

        $booking->status = 'rejected';
        $booking->save();

        return response()->json(['message' => 'تم رفض الحجز بنجاح']);
    }

    public function userBookings(Request $request)
    {
        $query = Booking::where('user_id', $request->user()->id)
            ->with(['apartment', 'apartment.governorate', 'apartment.city']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(10);

        return response()->json([
            'data' => BookingResource::collection($bookings->items()),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ]
        ]);
    }

    public function apartmentBookings(Request $request, $apartmentId)
    {
        $apartment = Apartment::findOrFail($apartmentId);

        // Check if the authenticated user is the owner of the apartment
        if ($apartment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك بعرض حجوزات هذه الشقة'], 403);
        }

        $bookings = Booking::where('apartment_id', $apartmentId)
            ->with(['user'])
            ->paginate(10);

        return response()->json([
            'data' => BookingResource::collection($bookings->items()),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ]
        ]);
    }

    // عرض الحجوزات الخاصة بالشقق التي يملكها المستخدم
    public function ownerBookings(Request $request)
    {
        $user = $request->user();
        $query = Booking::whereHas('apartment', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['user', 'apartment']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(10);

        return response()->json([
            'data' => BookingResource::collection($bookings->items()),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ]
        ]);
    }
}
