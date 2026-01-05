<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Http\Requests\StoreApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Http\Resources\ApartmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Apartment::query();

        // Filter by governorate if provided
        if ($request->has('governorate_id')) {
            $query->where('governorate_id', $request->governorate_id);
        }

        // Filter by city if provided
        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by search term if provided
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $apartments = $query->with(['governorate', 'city', 'user'])->get();

        if ($apartments->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد شقق لعرضها',
                'data' => []
            ], 200);
        }

        return response()->json([
            'data' => ApartmentResource::collection($apartments)
        ]);
    }

    public function myApartments(Request $request)
    {
        $user = $request->user();
        $apartments = $user->apartments()->with(['governorate', 'city'])->get();

        if ($apartments->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد شقق خاصة بك',
                'data' => []
            ], 200);
        }

        return response()->json([
            'data' => ApartmentResource::collection($apartments)
        ]);
    }

    public function store(StoreApartmentRequest $request)
    {
        $apartment = $request->user()->apartments()->create($request->validated());

        return response()->json([
            'message' => 'تم إنشاء الشقة بنجاح',
            'apartment' => new ApartmentResource($apartment)
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $apartment = Apartment::with(['governorate', 'city', 'user'])->find($id);

        if (!$apartment) {
            return response()->json(['message' => 'الشقة غير موجودة'], 404);
        }

        return new ApartmentResource($apartment);
    }

    public function update(UpdateApartmentRequest $request, $id)
    {
        $apartment = Apartment::find($id);

        if (!$apartment) {
            return response()->json(['message' => 'الشقة غير موجودة'], 404);
        }

        // Check if the authenticated user is the owner of the apartment
        if ($apartment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك بتعديل هذه الشقة'], 403);
        }

        $apartment->update($request->validated());

        return response()->json([
            'message' => 'تم تعديل الشقة بنجاح',
            'apartment' => new ApartmentResource($apartment)
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $apartment = Apartment::find($id);

        if (!$apartment) {
            return response()->json(['message' => 'الشقة غير موجودة'], 404);
        }

        // Check if the authenticated user is the owner of the apartment
        if ($apartment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك بحذف هذه الشقة'], 403);
        }

        $apartment->delete();

        return response()->json(['message' => 'تم حذف الشقة بنجاح']);
    }

    public function checkAvailability(Request $request, $id)
    {
        $apartment = Apartment::find($id);

        if (!$apartment) {
            return response()->json(['message' => 'الشقة غير موجودة'], 404);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Check if there are any existing bookings that conflict with the requested dates
        $existingBooking =
            \App\Models\Booking::where('apartment_id', $id)
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
                'available' => false,
                'message' => 'الشقة محجوزة من ' . $existingBooking->start_date->format('Y-m-d') . ' إلى ' . $existingBooking->end_date->format('Y-m-d'),
                'existing_booking' => [
                    'start_date' => $existingBooking->start_date->format('Y-m-d'),
                    'end_date' => $existingBooking->end_date->format('Y-m-d'),
                    'status' => $existingBooking->status
                ]
            ], 200);
        }

        return response()->json([
            'available' => true,
            'message' => 'الشقة متاحة في الفترة المحددة'
        ], 200);
    }
}
