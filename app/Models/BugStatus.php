<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BugStatus extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'created_by',
        'order',
    ];

    /**
     * @param $project_id
     * @return mixed
     */
    public function bugs($project_id)
    {
        if (\Auth::user()->type == 'company')
            return Bug::where('status', '=', $this->id)->where('project_id', '=', $project_id)->orderBy('order')->get();
        elseif (\Auth::user()->type == 'client') {
            return Bug::where('status', '=', $this->id)->where('project_id', '=', $project_id)->orderBy('order')->get();
        } else
//            return Bug::where('status', '=', $this->id)->where('project_id', '=', $project_id)->whereRaw("find_in_set('" . \Auth::user()->id . "',assign_to)")->orderBy('order')->get();
            return Bug::where('status', '=', $this->id)->where('project_id', '=', $project_id)->orderBy('order')->get();
    }

    /**
     * @param $project_id
     * @return mixed
     */
    public function assign_bugs($project_id)
    {
        return Bug::where('status', '=', $this->id)->where('project_id', '=', $project_id)->where('assign_to', '=', \Auth::user()->id)->orderBy('order')->get();
    }
}
