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
        $apartment = Apartment::find($apartmentId);

        if (!$apartment) {
            return response()->json(['message' => 'الشقة غير موجودة'], 404);
        }

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

        return response()->json([
            'message' => 'تم إنشاء الحجز بنجاح',
            'booking' => new BookingResource($booking)
        ], 201);
    }

    public function modifyBooking(UpdateBookingRequest $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }

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

        // Note: No refund needed since amount was not deducted when booking was created

        // Check if user has enough balance for new booking
        $userWallet = Wallet::where('user_id', $request->user()->id)->first();
        if (!$userWallet || $userWallet->balance < $newTotalAmount) {
            return response()->json(['message' => 'لا يوجد رصيد كافي في المحفظة'], 400);
        }

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
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }

        // Check if the authenticated user is the owner of the booking
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك بإلغاء هذا الحجز'], 403);
        }

        // Check if booking is confirmed - if so, user can't cancel it
        if ($booking->status === 'confirmed') {
            return response()->json(['message' => 'لا يمكن إلغاء الحجز بعد تأكيده من قبل المالك'], 400);
        }

        // Note: No refund needed since amount was not deducted when booking was created

        // Update booking status
        $booking->status = 'cancelled';
        $booking->save();

        return response()->json(['message' => 'تم إلغاء الحجز بنجاح']);
    }

    public function confirmBooking(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }

        // Check if the authenticated user is the owner of the apartment
        if ($booking->apartment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك بتأكيد هذا الحجز'], 403);
        }

        // Check if booking is already confirmed, rejected, cancelled or completed
        if (in_array($booking->status, ['confirmed', 'rejected', 'cancelled', 'completed'])) {
            return response()->json(['message' => 'لا يمكن تأكيد هذا الحجز'], 400);
        }

        // Check if tenant has enough balance
        $tenantWallet = Wallet::where('user_id', $booking->user_id)->first();
        if (!$tenantWallet || $tenantWallet->balance < $booking->amount) {
            return response()->json(['message' => 'لا يوجد رصيد كافي في محفظة المستأجر'], 400);
        }

        // Transfer amount from tenant's wallet to owner's wallet
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
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }

        // Check if the authenticated user is the owner of the apartment
        if ($booking->apartment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك برفض هذا الحجز'], 403);
        }

        // Check if booking is already confirmed, rejected, cancelled or completed
        if (in_array($booking->status, ['confirmed', 'rejected', 'cancelled', 'completed'])) {
            return response()->json(['message' => 'لا يمكن رفض هذا الحجز'], 400);
        }

        // Note: No refund needed since amount was not deducted when booking was created

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

        $bookings = $query->get();

        return response()->json([
            'data' => BookingResource::collection($bookings)
        ]);
    }

    public function apartmentBookings(Request $request, $apartmentId)
    {
        $apartment = Apartment::find($apartmentId);

        if (!$apartment) {
            return response()->json(['message' => 'الشقة غير موجودة'], 404);
        }

        // Check if the authenticated user is the owner of the apartment
        if ($apartment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك بعرض حجوزات هذه الشقة'], 403);
        }

        $bookings = Booking::where('apartment_id', $apartmentId)
            ->with(['user'])
            ->get();

        return response()->json([
            'data' => BookingResource::collection($bookings)
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

        $bookings = $query->get();

        return response()->json([
            'data' => BookingResource::collection($bookings)
        ]);
    }
}
