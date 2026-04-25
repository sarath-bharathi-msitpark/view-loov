<?php

namespace App\Http\Controllers\FieldTrackAPI;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralSettingsController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getCountries()
    {
        $countries = Country::select('id', 'name')->orderBy('name')->where('created_by', Auth::user()->creatorId())->get();

        return response()->json([
            'is_success' => true,
            'countries' => $countries,
            'message' => 'Countries retrieved successfully.',
        ], 200);
    }

    /**
     * @param $countryId
     * @return JsonResponse
     */
    public function getStatesByCountry($countryId)
    {
        $country = Country::with('states:id,country_id,name')
            ->select('id', 'name')
            ->findOrFail($countryId);

        return response()->json([
            'is_success' => true,
            'data' => $country,
            'message' => 'Country and states retrieved successfully.',
        ], 200);
    }

    /**
     * @param $stateId
     * @return JsonResponse
     */
    public function getCitiesByState($stateId)
    {
        $state = State::with('cities:id,state_id,name')
            ->select('id', 'name')
            ->findOrFail($stateId);

        return response()->json([
            'is_success' => true,
            'data' => $state,
            'message' => 'State and cities retrieved successfully.',
        ], 200);
    }

    /**
     * @param $cityId
     * @return JsonResponse
     */
    public function getAreasByCity($cityId)
    {
        $city = City::with('areas:id,city_id,name')
            ->select('id', 'name')
            ->findOrFail($cityId);

        return response()->json([
            'is_success' => true,
            'data' => $city,
            'message' => 'City and areas retrieved successfully.',
        ], 200);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function getBeatByArea(string $id): JsonResponse
    {
        $area = Area::with('beats:id,area_id,name')
            ->select('id', 'name')
            ->findOrFail($id);

        return response()->json([
            'is_success' => true,
            'data' => $area,
            'message' => 'Area and beats retrieved successfully.',
        ], 200);
    }

}
