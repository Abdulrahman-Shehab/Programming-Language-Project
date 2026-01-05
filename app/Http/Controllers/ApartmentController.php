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

    public function store(StoreApartmentRequest $request)
    {
        $apartment = $request->user()->apartments()->create($request->validated());

        return response()->json([
            'message' => 'تم إنشاء الشقة بنجاح',
            'apartment' => new ApartmentResource($apartment)
        ], 201);
    }

    public function show(Apartment $apartment)
    {
        return new ApartmentResource($apartment->load(['governorate', 'city', 'user']));
    }

    public function update(UpdateApartmentRequest $request, Apartment $apartment)
    {
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

    public function destroy(Request $request, Apartment $apartment)
    {
        // Check if the authenticated user is the owner of the apartment
        if ($apartment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك بحذف هذه الشقة'], 403);
        }

        $apartment->delete();

        return response()->json(['message' => 'تم حذف الشقة بنجاح']);
    }
}
