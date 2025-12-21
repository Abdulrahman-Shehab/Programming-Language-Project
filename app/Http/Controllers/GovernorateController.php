<?php

namespace App\Http\Controllers;

use App\Models\Governorate;
use App\Http\Requests\StoreGovernorateRequest;
use App\Http\Requests\UpdateGovernorateRequest;
use App\Http\Resources\GovernorateResource;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $governorates = Governorate::all();
        return GovernorateResource::collection($governorates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGovernorateRequest $request)
    {
        $governorate = Governorate::create($request->validated());
        return new GovernorateResource($governorate);
    }

    /**
     * Display the specified resource.
     */
    public function show(Governorate $governorate)
    {
        return new GovernorateResource($governorate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGovernorateRequest $request, Governorate $governorate)
    {
        $governorate->update($request->validated());
        return new GovernorateResource($governorate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Governorate $governorate)
    {
        $governorate->delete();
        return response()->json(['message' => 'Governorate deleted successfully']);
    }
}
