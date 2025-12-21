<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Governorate;
use App\Models\City;
use App\Http\Requests\StoreApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Http\Resources\ApartmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Apartment::with(['user', 'governorate', 'city']);

            // Filter by governorate
            if ($request->has('governorate_id')) {
                $query->where('governorate_id', $request->governorate_id);
            }

            // Filter by city
            if ($request->has('city_id')) {
                $query->where('city_id', $request->city_id);
            }

            // Filter by price range
            if ($request->has('min_price')) {
                $query->where('daily_price', '>=', $request->min_price);
            }

            if ($request->has('max_price')) {
                $query->where('daily_price', '<=', $request->max_price);
            }

            // Search by title or description
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%');
                });
            }

            $apartments = $query->get();

            return ApartmentResource::collection($apartments);
        } catch (\Exception $e) {
            Log::error('Error fetching apartments: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch apartments'], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApartmentRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // Set the user_id to the authenticated user
            $validatedData['user_id'] = auth()->user()->id;

            // Handle image uploads
            for ($i = 1; $i <= 5; $i++) {
                if ($request->hasFile('image' . $i)) {
                    $validatedData['image' . $i] = $request->file('image' . $i)->store('apartments', 'public');
                }
            }

            $apartment = Apartment::create($validatedData);

            // Load relationships
            $apartment->load(['user', 'governorate', 'city']);

            return response()->json([
                'message' => 'تم إنشاء الشقة بنجاح',
                'apartment' => new ApartmentResource($apartment)
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating apartment: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create apartment'], 500);
        }
    }    /**
     * Display the specified resource.
     */
    public function show(Apartment $apartment)
    {
        try {
            $apartment->load(['user', 'governorate', 'city']);
            return new ApartmentResource($apartment);
        } catch (\Exception $e) {
            Log::error('Error fetching apartment: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch apartment'], 500);
        }
    }    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApartmentRequest $request, Apartment $apartment)
    {
        try {
            // Check if the authenticated user owns this apartment
            if (auth()->user()->id !== $apartment->user_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Get validated data only
            $validatedData = $request->validated();

            // Auto-update address when governorate or city changes
            if (isset($validatedData['governorate_id']) || isset($validatedData['city_id'])) {
                // Load relationships to get names
                $apartment->load(['governorate', 'city']);

                // Get the new governorate and city names
                $governorateName = null;
                $cityName = null;

                if (isset($validatedData['governorate_id'])) {
                    $governorate = \App\Models\Governorate::find($validatedData['governorate_id']);
                    $governorateName = $governorate ? $governorate->name : null;
                } else {
                    $governorateName = $apartment->governorate ? $apartment->governorate->name : null;
                }

                if (isset($validatedData['city_id'])) {
                    $city = \App\Models\City::find($validatedData['city_id']);
                    $cityName = $city ? $city->name : null;
                } else {
                    $cityName = $apartment->city ? $apartment->city->name : null;
                }

                // Construct new address
                $newAddressParts = array_filter([$cityName, $governorateName]);
                if (!empty($newAddressParts)) {
                    $validatedData['address'] = implode(', ', $newAddressParts);
                }
            }

            // Handle image updates separately
            for ($i = 1; $i <= 5; $i++) {
                if ($request->hasFile('image' . $i)) {
                    // Delete old image if exists
                    if ($apartment->{'image' . $i}) {
                        Storage::disk('public')->delete($apartment->{'image' . $i});
                    }
                    $validatedData['image' . $i] = $request->file('image' . $i)->store('apartments', 'public');
                }
            }

            // Only update if there's data to update
            if (!empty($validatedData)) {
                $apartment->update($validatedData);
            }

            // Load relationships
            $apartment->load(['user', 'governorate', 'city']);

            return response()->json([
                'message' => 'تم تحديث الشقة بنجاح',
                'apartment' => new ApartmentResource($apartment)
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating apartment: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update apartment'], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Apartment $apartment)
    {
        try {
            // Check if the authenticated user owns this apartment
            if (auth()->user()->id !== $apartment->user_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Delete images from storage
            for ($i = 1; $i <= 5; $i++) {
                if ($apartment->{'image' . $i}) {
                    Storage::disk('public')->delete($apartment->{'image' . $i});
                }
            }

            $apartment->delete();

            return response()->json(['message' => 'تم حذف الشقة بنجاح'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting apartment: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete apartment'], 500);
        }
    }

    /**
     * Get apartments owned by the authenticated user.
     */
    public function myApartments(Request $request)
    {
        try {
            $apartments = Apartment::with(['governorate', 'city'])
                ->where('user_id', auth()->user()->id)
                ->paginate(10);

            return response()->json($apartments, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching user apartments: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch apartments'], 500);
        }
    }
}
