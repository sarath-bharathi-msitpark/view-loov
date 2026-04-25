<?php

namespace App\Http\Controllers\Web\FieldTrack;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\Beat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function countryIndex(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $countriesQuery = Country::query()
            ->where('created_by', $creatorId);

        if ($request->filled('search')) {
            $countriesQuery->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('status')) {
            $countriesQuery->where('status', $request->status);
        }
        $countriesQuery->orderBy('created_at', 'desc');

        $perPage = $request->input('per_page', 10);
        $countries = $countriesQuery->paginate($perPage)->appends($request->query());

        return view('field_track.location.country', compact('countries'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function countryStore(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $request->validate([
            'name' => [
                'required',
                Rule::unique('countries')->where(fn($query) => $query->where('created_by', $creatorId))
            ],
            'code' => [
                'required',
                Rule::unique('countries')->where(fn($query) => $query->where('created_by', $creatorId))
            ],
        ]);

        Country::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'created_by' => $creatorId,
            'status' => 1
        ]);

        return redirect()->back()->with('success', 'Country has been successfully added');
    }

    /**
     * @param Request $request
     * @param Country $country
     * @return RedirectResponse
     */
    public function countryUpdate(Request $request, Country $country)
    {
        $creatorId = Auth::user()->creatorId();

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('countries')->where(fn($query) => $query->where('created_by', $creatorId))->ignore($country->id)
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('countries')->where(fn($query) => $query->where('created_by', $creatorId))->ignore($country->id)
            ],
        ]);

        $country->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'status' => $request->status ?? 1,
        ]);

        return back()->with('success', 'Country updated successfully.');
    }

    /**
     * @param Country $country
     * @return RedirectResponse
     */
    public function countryDestroy(Country $country)
    {
        $country->delete();
        return back()->with('success', 'Country deleted successfully.');
    }

    /**
     * @param Country $country
     * @return JsonResponse
     */
    public function countryToggleStatus(Country $country)
    {
        $country->status = !$country->status;
        $country->save();

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function stateIndex(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $countries = Country::query()
            ->select('name', 'id')
            ->where('status', 1)
            ->where('created_by', $creatorId)
            ->get();

        $statesQuery = State::query()
            ->where('created_by', $creatorId);

        if ($request->filled('search')) {
            $statesQuery->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('status')) {
            $statesQuery->where('status', $request->status);
        }
        $statesQuery->orderBy('created_at', 'desc');

        $perPage = $request->input('per_page', 10);
        $states = $statesQuery->paginate($perPage)->appends($request->query());

        return view('field_track.location.state', compact('countries', 'states'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function stateStore(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('states')->where(fn($query) => $query->where('created_by', $creatorId))
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('states')->where(fn($query) => $query->where('created_by', $creatorId))
            ],
            'country_id' => 'required|exists:countries,id',
        ]);

        State::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'country_id' => $request->country_id,
            'created_by' => $creatorId,
            'status' => 1
        ]);

        return redirect()->back()->with('success', 'State has been successfully added');
    }

    /**
     * @param Request $request
     * @param State $state
     * @return RedirectResponse
     */
    public function stateUpdate(Request $request, State $state)
    {
        $creatorId = Auth::user()->creatorId();

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('states')
                    ->where(fn($query) => $query->where('created_by', $creatorId))
                    ->ignore($state->id),
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('states')
                    ->where(fn($query) => $query->where('created_by', $creatorId))
                    ->ignore($state->id),
            ],
            'country_id' => 'required|exists:countries,id',
        ]);

        $state->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'country_id' => $request->country_id,
            'status' => $request->status ?? 1,
        ]);

        return back()->with('success', 'State updated successfully.');
    }

    /**
     * @param State $state
     * @return RedirectResponse
     */
    public function stateDestroy(State $state)
    {
        $state->delete();
        return back()->with('success', 'State deleted successfully.');
    }

    /**
     * @param State $state
     * @return JsonResponse
     */
    public function stateToggleStatus(State $state)
    {
        $state->status = !$state->status;
        $state->save();

        return response()->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return Factory|View|Application|object
     */
    public function cityIndex(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $countries = Country::query()
            ->select('name', 'id')
            ->where('status', 1)
            ->where('created_by', $creatorId)
            ->get();

        $citiesQuery = City::with('state.country')
            ->where('created_by', $creatorId);

        if ($request->filled('search')) {
            $search = $request->search;
            $citiesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('state', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhereHas('country', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $perPage = $request->input('per_page', 10);
        $cities = $citiesQuery->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return view('field_track.location.city', compact('countries', 'cities'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cityStore(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'name' => 'required|string|max:255|unique:cities,name,NULL,id,state_id,' . $request->state_id,
            'status' => 'required|in:0,1',
        ]);

        City::create([
            'state_id' => $request->state_id,
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => $creatorId,
        ]);

        return redirect()->back()->with('success', 'City added successfully.');
    }

    /**
     * @param Request $request
     * @param City $city
     * @return RedirectResponse
     */
    public function cityUpdate(Request $request, City $city)
    {
        $creatorId = Auth::user()->creatorId();

        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'name' => 'required|string|max:255|unique:cities,name,' . $city->id . ',id,state_id,' . $request->state_id,
            'status' => 'required|in:0,1',
        ]);

        $city->update([
            'state_id' => $request->state_id,
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => $creatorId,
        ]);

        return back()->with('success', 'City updated successfully.');
    }

    /**
     * @param City $city
     * @return RedirectResponse
     */
    public function cityDestroy(City $city)
    {
        $city->delete();
        return back()->with('success', 'City deleted successfully.');
    }

    /**
     * @param City $city
     * @return JsonResponse
     */
    public function cityToggleStatus(City $city)
    {
        $city->status = !$city->status;
        $city->save();

        return response()->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return Factory|View|Application|object
     */
    public function areaIndex(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $countries = Country::query()
            ->select('name', 'id')
            ->where('status', 1)
            ->where('created_by', $creatorId)
            ->get();

        $states = State::query()
            ->select('name', 'id')
            ->where('status', 1)
            ->where('created_by', $creatorId)
            ->get();

        $areasQuery = Area::with('city.state')
            ->where('created_by', $creatorId);

        if ($request->filled('search')) {
            $search = $request->search;
            $areasQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('city', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhereHas('state', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $perPage = $request->input('per_page', 10);
        $areas = $areasQuery->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return view('field_track.location.area', compact('countries', 'states', 'areas'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function areaStore(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255|unique:areas,name,NULL,id,city_id,' . $request->city_id,
            'status' => 'required|in:0,1',
        ]);

        $creatorId = Auth::user()->creatorId();

        Area::create([
            'city_id' => $request->city_id,
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => $creatorId,
        ]);

        return redirect()->back()->with('success', 'Area added successfully.');
    }

    /**
     * @param Request $request
     * @param Area $area
     * @return RedirectResponse
     */
    public function areaUpdate(Request $request, Area $area)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'name' => 'required|string|max:255|unique:areas,name,' . $area->id . ',id,city_id,' . $request->city_id,
            'status' => 'required|in:0,1',
        ]);

        $creatorId = Auth::user()->creatorId();

        $area->update([
            'city_id' => $request->city_id,
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => $creatorId,
        ]);

        return back()->with('success', 'Area updated successfully.');
    }

    /**
     * @param Area $area
     * @return RedirectResponse
     */
    public function areaDestroy(Area $area)
    {
        $area->delete();
        return back()->with('success', 'Area deleted successfully.');
    }

    /**
     * @param Area $area
     * @return JsonResponse
     */
    public function areaToggleStatus(Area $area)
    {
        $area->status = !$area->status;
        $area->save();

        return response()->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return Factory|View|Application|object
     */
    public function beatIndex(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $countries = Country::query()
            ->select('name', 'id')
            ->where('status', 1)
            ->where('created_by', $creatorId)
            ->get();

        $states = State::query()
            ->select('name', 'id')
            ->where('status', 1)
            ->where('created_by', $creatorId)
            ->get();

        $beatsQuery = Beat::with('area.city')
            ->where('created_by', $creatorId);

        if ($request->filled('search')) {
            $search = $request->search;
            $beatsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('area', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhereHas('city', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $perPage = $request->input('per_page', 10);
        $beats = $beatsQuery->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return view('field_track.location.beat', compact('countries', 'states', 'beats'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function beatStore(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|string|max:255|unique:beats,name,NULL,id,area_id,' . $request->area_id,
            'status' => 'required|in:0,1',
        ]);

        $creatorId = Auth::user()->creatorId();

        Beat::create([
            'area_id' => $request->area_id,
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => $creatorId,
        ]);

        return redirect()->back()->with('success', 'Beat added successfully.');
    }

    /**
     * @param Request $request
     * @param Beat $beat
     * @return RedirectResponse
     */
    public function beatUpdate(Request $request, Beat $beat)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|string|max:255|unique:beats,name,' . $beat->id . ',id,area_id,' . $request->area_id,
            'status' => 'required|in:0,1',
        ]);

        $creatorId = Auth::user()->creatorId();

        $beat->update([
            'area_id' => $request->area_id,
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => $creatorId,
        ]);

        return back()->with('success', 'Beat updated successfully.');
    }

    /**
     * @param Beat $beat
     * @return RedirectResponse
     */
    public function beatDestroy(Beat $beat)
    {
        $beat->delete();
        return back()->with('success', 'Beat deleted successfully.');
    }

    /**
     * @param Beat $beat
     * @return JsonResponse
     */
    public function beatToggleStatus(Beat $beat)
    {
        $beat->status = !$beat->status;
        $beat->save();

        return response()->json(['success' => true]);
    }

    /**
     * @param $countryId
     * @return JsonResponse
     */
    public function getStates($countryId)
    {
        $creatorId = Auth::user()->creatorId();

        $states = State::where('country_id', $countryId)
            ->where('created_by', $creatorId)
            ->where('status', 1)
            ->get();

        $states->prepend([
            'id' => '',
            'name' => 'Select State'
        ]);

        return response()->json($states);
    }

    /**
     * @param $stateId
     * @return JsonResponse
     */
    public function getCities($stateId)
    {
        $creatorId = Auth::user()->creatorId();

        $cities = City::where('state_id', $stateId)
            ->where('created_by', $creatorId)
            ->where('status', 1)
            ->get();

        $cities->prepend([
            'id' => '',
            'name' => 'Select City'
        ]);

        return response()->json($cities);
    }

    /**
     * @param $cityId
     * @return JsonResponse
     */
    public function getAreas($cityId)
    {
        $creatorId = Auth::user()->creatorId();

        $areas = Area::where('city_id', $cityId)
            ->where('created_by', $creatorId)
            ->where('status', 1)
            ->get();

        $areas->prepend([
            'id' => '',
            'name' => 'Select Area'
        ]);

        return response()->json($areas);
    }
}
