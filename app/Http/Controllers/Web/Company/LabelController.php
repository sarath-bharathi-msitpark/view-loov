<?php

namespace App\Http\Controllers\Web\Company;
use App\Http\Controllers\Controller;

use App\Models\Label;
use App\Models\Pipeline;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function __construct()
    {
        $this->middleware(
            [
                'auth',
                'XSS',
            ]
        );
    }

    /**
     * Display a listing of the relabel.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $labels = Label::select('labels.*', 'pipelines.name as pipeline')->join('pipelines', 'pipelines.id', '=', 'labels.pipeline_id')->where('pipelines.created_by', '=', \Auth::user()->ownerId())->where('labels.created_by', '=', \Auth::user()->ownerId())->orderBy('labels.pipeline_id')->get();
        $label = Label::where('created_by', \Auth::user()->ownerId())->get();
        $pipelines = [];

        foreach ($labels as $label) {
            if (!array_key_exists($label->pipeline_id, $pipelines)) {
                $pipelines[$label->pipeline_id] = [];
                $pipelines[$label->pipeline_id]['name'] = $label['pipeline'];
                $pipelines[$label->pipeline_id]['labels'] = [];
            }
            $pipelines[$label->pipeline_id]['labels'][] = $label;
        }

        return view('company.labels.index')->with('pipelines', $pipelines);
    }

    /**
     * Show the form for creating a new relabel.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pipelines = Pipeline::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
        $colors = Label::$colors;

        return view('company.labels.create')->with('pipelines', $pipelines)->with('colors', $colors);
    }

    /**
     * Store a newly created relabel in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
                'pipeline_id' => 'required',
                'color' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->route('labels.index')->with('error', $messages->first());
        }

        $label = new Label();
        $label->name = $request->name;
        $label->color = $request->color;
        $label->pipeline_id = $request->pipeline_id;
        $label->created_by = \Auth::user()->ownerId();
        $label->save();

        return redirect()->route('organization.labels.index')->with('success', __('Label successfully created!'));
    }

    /**
     * Display the specified relabel.
     *
     * @param \App\Label $label
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Label $label)
    {
        return redirect()->route('organization.labels.index');
    }

    /**
     * Show the form for editing the specified relabel.
     *
     * @param \App\Label $label
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Label $label)
    {
        if ($label->created_by == \Auth::user()->ownerId()) {
            $pipelines = Pipeline::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
            $colors = Label::$colors;

            return view('company.labels.edit', compact('label', 'pipelines', 'colors'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Update the specified relabel in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Label $label
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Label $label)
    {
        if ($label->created_by == \Auth::user()->ownerId()) {

            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required|max:20',
                    'pipeline_id' => 'required',
                    'color' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->route('users')->with('error', $messages->first());
            }

            $label->name = $request->name;
            $label->color = $request->color;
            $label->pipeline_id = $request->pipeline_id;
            $label->save();

            return redirect()->route('organization.labels.index')->with('success', __('Label successfully updated!'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified relabel from storage.
     *
     * @param \App\Label $label
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Label $label)
    {
        if ($label->created_by == \Auth::user()->ownerId()) {
            $label->delete();

            return redirect()->route('organization.labels.index')->with('success', __('Label successfully deleted!'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
