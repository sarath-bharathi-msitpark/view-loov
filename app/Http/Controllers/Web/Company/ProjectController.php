<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Bug;
use App\Models\BugComment;
use App\Models\BugFile;
use App\Models\BugStatus;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectTeam;
use App\Models\ProjectUser;
use App\Models\TaskStage;
use App\Models\Team;
use App\Models\TimeTracker;
use App\Models\User;
use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * @param $view
     * @return Factory|View|Application|object
     */
    public function index($view = 'grid')
    {
        return view('company.projects.index', compact('view'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function filterProjectView(Request $request)
    {
        $authUser = Auth::user();

        // Check role & permission
        $hasAdminRole = $authUser->hasRole(ROLE_ADMINISTRATOR);
        $canShareProjects = $authUser->can('project_management');

        // Determine accessible project IDs
        if ($hasAdminRole || $canShareProjects) {
            // Admin or has permission → can view all projects of creator
            $user_projects = Project::where('created_by', $authUser->creatorId())
                ->pluck('id')
                ->toArray();
        } else {
            // Normal user → only their assigned and created projects
            $assigned_projects = $authUser->projects()
                ->select('project_users.project_id')
                ->pluck('project_users.project_id')
                ->toArray();

            $created_projects = Project::where('created_by', $authUser->creatorId())
                ->where('client_id', $authUser->id)
                ->pluck('id')
                ->toArray();

            $user_projects = array_unique(array_merge($assigned_projects, $created_projects));
        }

        // Handle AJAX filters
        if ($request->ajax() && $request->has('view') && $request->has('sort')) {
            [$sortField, $sortOrder] = explode('-', $request->sort) + [null, null];
            $sortField = $sortField ?: 'created_at';
            $sortOrder = $sortOrder ?: 'desc';

            $projects = Project::whereIn('id', $user_projects)
                ->orderBy($sortField, $sortOrder);

            // Keyword filter
            if (!empty($request->keyword)) {
                $keyword = $request->keyword;
                $projects->where(function ($q) use ($keyword) {
                    $q->where('project_name', 'LIKE', "%{$keyword}%")
                        ->orWhereRaw('FIND_IN_SET(?, tags)', [$keyword]);
                });
            }

            // Status filter
            if (!empty($request->status)) {
                $projects->whereIn('status', (array)$request->status);
            }

            $projects = $projects->get();

            // --- Compute progress ---
            foreach ($projects as $project) {
                $allTasks = $project->tasks ?? collect();

                $totalChecklist = 0;
                $completedChecklist = 0;

                foreach ($allTasks as $task) {
                    if (isset($task->checklist) && is_iterable($task->checklist)) {
                        $totalChecklist += count($task->checklist);
                        $completedChecklist += collect($task->checklist)
                            ->where('status', 1)
                            ->count();
                    }
                }

                $project->progress = $totalChecklist > 0
                    ? round(($completedChecklist / $totalChecklist) * 100, 2)
                    : 0;
            }

            // --- Fetch last task stage (used in project card UI) ---
            $last_task = TaskStage::where('created_by', $authUser->creatorId())
                ->orderBy('order', 'DESC')
                ->first();

            $returnHTML = view('company.projects.' . $request->view, compact('projects', 'user_projects', 'last_task'))->render();

            return response()->json([
                'success' => true,
                'html' => $returnHTML,
            ]);
        }

        return response()->json(['success' => false, 'html' => ''], 400);
    }

    /**
     * @return Factory|View|Application|object
     */
    public function create()
    {
        $teams = Team::where('created_by', '=', \Auth::user()->creatorId())
            ->get()->pluck('name', 'id');
        $users = User::where('created_by', \Auth::user()->creatorId())
            ->where('type', '!=', 'client')
            ->whereHas('employee', function ($q) {
                $q->where('is_active', '1');
            })
            ->pluck('name', 'id');
        $teams->prepend('Select Teams', '');
        $users->prepend('Select Users', '');
        return view('company.projects.create', compact('teams', 'users'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'project_name' => 'required',
                'project_id' => 'required',
                'teams' => 'required|array',
                'users' => 'required|array',
                'description' => 'nullable',
                'status' => 'required',
                'on_board_date' => 'nullable|date',
                'support_start_date' => 'nullable|date',
                'support_end_date' => 'nullable|date',
                'renewal_date' => 'nullable|date',
                'project_image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', Utility::errorFormat($validator->getMessageBag()));
        }

        DB::beginTransaction();

        try {
            $projectImageUrl = null;
            if ($request->hasFile('project_image')) {
                $filenameWithExt = $request->file('project_image')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('project_image')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $companySlug = Str::slug(Auth::user()->company->name ?? 'company');
                $nameSlug = Str::slug($request->project_name);
                $dir = "uploads/companies/{$companySlug}/projects/{$nameSlug}/images";

                $imagePath = $dir . '/' . $filenameWithExt;
                if (\File::exists($imagePath)) {
                    \File::delete($imagePath);
                }

                $path = Utility::upload_file($request, 'project_image', $fileNameToStore, $dir, []);

                if ($path['flag'] == 1) {
                    $projectImageUrl = $path['url'];
                } else {
                    return redirect()->back()->with('error', $path['msg']);
                }
            }

            // Create project
            $project = Project::create([
                'project_name' => ucwords(strtolower($request->project_name)),
                'project_id' => $request->project_id,
                'description' => $request->description,
                'status' => $request->status,
                'on_board_date' => $request->on_board_date ?? now(),
                'support_start_date' => $request->support_start_date ?? now(),
                'support_end_date' => $request->support_end_date ?? now(),
                'renewal_date' => $request->renewal_date ?? now(),
                'created_by' => Auth::user()->creatorId(),
                'project_image' => $projectImageUrl,
            ]);

            // Store teams
            foreach ($request->teams as $teamId) {
                ProjectTeam::create([
                    'project_id' => $project->id,
                    'team_id' => $teamId,
                    'invited_by' => Auth::user()->id,
                ]);
            }

            // Store users
            foreach ($request->users as $userId) {
                ProjectUser::create([
                    'project_id' => $project->id,
                    'user_id' => $userId,
                    'invited_by' => Auth::user()->id,
                ]);
            }

            DB::commit();

            return redirect()->route('organization.projects.index')
                ->with('success', __('Project Added Successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Project Store Error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('Something went wrong while saving the project.'));
        }
    }

    /**
     * @param Project $project
     * @return Factory|View|Application|JsonResponse|object
     */
    public function edit(Project $project)
    {
        $project = Project::findOrfail($project->id);

        $teams = Team::where('created_by', '=', Auth::user()->creatorId())
            ->pluck('name', 'id');

        $users = User::where('created_by', Auth::user()->creatorId())
            ->where('type', '!=', 'client')
            ->whereHas('employee', function ($q) {
                $q->where('is_active', '1');
            })
            ->pluck('name', 'id');

        $selectedTeams = $project->teams()->pluck('teams.id')->toArray();
        $selectedUsers = $project->users()->pluck('users.id')->toArray();

        if ($project->created_by == Auth::user()->creatorId()) {
            return view('company.projects.edit', compact(
                'project',
                'teams',
                'users',
                'selectedTeams',
                'selectedUsers'
            ));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return RedirectResponse
     */
    public function update(Request $request, Project $project)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'project_name' => 'required',
                'project_id' => 'required',
                'teams' => 'nullable|array',
                'users' => 'nullable|array',
                'description' => 'nullable|string',
                'status' => 'required',
                'on_board_date' => 'nullable|date',
                'support_start_date' => 'nullable|date',
                'support_end_date' => 'nullable|date',
                'renewal_date' => 'nullable|date',
                'project_image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', Utility::errorFormat($validator->getMessageBag()));
        }

        DB::beginTransaction();

        try {

            $projectImageUrl = $project->project_image;

            if ($request->hasFile('project_image')) {
                $filenameWithExt = $request->file('project_image')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('project_image')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $companySlug = Str::slug(Auth::user()->company->name ?? 'company');
                $nameSlug = Str::slug($request->project_name);
                $dir = "uploads/companies/{$companySlug}/projects/{$nameSlug}/images";

                if (!empty($project->project_image)) {
                    $oldPath = $project->project_image;
                    if (\File::exists($oldPath)) {
                        \File::delete($oldPath);
                    }
                }

                $path = Utility::upload_file($request, 'project_image', $fileNameToStore, $dir, []);

                if ($path['flag'] == 1) {
                    $projectImageUrl = $path['url'];
                } else {
                    return redirect()->back()->with('error', $path['msg']);
                }
            }

            $project->update([
                'project_name' => ucwords(strtolower($request->project_name)),
                'project_id' => $request->project_id,
                'description' => $request->description,
                'status' => $request->status,
                'on_board_date' => $request->on_board_date ?? $project->on_board_date,
                'support_start_date' => $request->support_start_date ?? $project->support_start_date,
                'support_end_date' => $request->support_end_date ?? $project->support_end_date,
                'renewal_date' => $request->renewal_date ?? $project->renewal_date,
                'project_image' => $projectImageUrl,
            ]);

            if ($request->filled('teams')) {
                $project->teams()->sync($request->teams);
            }

            if ($request->filled('users')) {
                $project->users()->sync($request->users);
            }

            DB::commit();

            return redirect()->route('organization.projects.index')
                ->with('success', __('Project Updated Successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Project Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('Something went wrong while updating the project.'));
        }
    }

    /**
     * @param Project $project
     * @return RedirectResponse
     */
    public function destroy(Project $project)
    {
        if (!empty($project->project_image)) {
            $file_path = $project->project_image;
            Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
        }
        $project->delete();
        return redirect()->back()->with('success', __('Project Successfully Deleted.'));
    }

    /**
     * @param Request $request
     * @param $project_id
     * @return Factory|View|Application|object
     */
    public function inviteMemberView(Request $request, $project_id)
    {
        $project = Project::find($project_id);

        $user_project = $project->users->pluck('id')->toArray();

        $user_contact = User::where('created_by', Auth::user()->creatorId())
            ->where('type', '!=', 'client')->whereNOTIn('id', $user_project)
            ->whereHas('employee', function ($query) {
                $query->where('is_active', true);
            })
            ->pluck('id')->toArray();

        $arrUser = array_unique($user_contact);

        $users = User::whereIn('id', $arrUser)->get();

        return view('company.projects.invite', compact('project_id', 'users'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function inviteProjectUserMember(Request $request)
    {
        $authuser = Auth::user();

        // Check if user is already invited to this project
        $alreadyInvited = ProjectUser::where('project_id', $request->project_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($alreadyInvited) {
            return response()->json([
                'code' => 409,
                'status' => 'Error',
                'error' => __('This user has already been invited to the project.'),
            ]);
        }

        $invitingUser = User::findOrFail($request->user_id);

        // Create project-user relation
        ProjectUser::create([
            'project_id' => $request->project_id,
            'user_id' => $invitingUser->id,
            'invited_by' => $authuser->id,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $authuser->id,
            'project_id' => $request->project_id,
            'log_type' => 'Invite User',
            'remark' => json_encode(['title' => $invitingUser->name]),
        ]);

        return response()->json([
            'code' => 200,
            'status' => 'Success',
            'success' => __('User invited successfully.'),
        ]);
    }

    /**
     * @param $id
     * @param $user_id
     * @return RedirectResponse
     */
    public function destroyProjectUser($id, $user_id)
    {
        $project = Project::find($id);
        if ($project->created_by == \Auth::user()->ownerId()) {
            ProjectUser::where('project_id', '=', $project->id)->where('user_id', '=', $user_id)->delete();

            return redirect()->back()->with('success', __('User successfully deleted!'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function loadUser(Request $request)
    {
        if ($request->ajax()) {
            $project = Project::find($request->project_id);
            $returnHTML = view('company.projects.users', compact('project'))->render();

            return response()->json(
                [
                    'success' => true,
                    'html' => $returnHTML,
                ]
            );
        }
    }

    /**
     * @param Project $project
     * @return Factory|View|Application|object
     */
    public function show(Project $project)
    {
        // dd('hi');
        $usr = Auth::user();

        $project_data = [];
        // Task Count
        $tasks = Project::projectTask($project->id);
        $project_task = $tasks->count();
        $completedTask = ProjectTask::where('project_id', $project->id)->where('is_complete', 1)->get();

        $project_done_task = $completedTask->count();

        $project_data['task'] = [
            'total' => number_format($project_task),
            'done' => number_format($project_done_task),
            'percentage' => Utility::getPercentage($project_done_task, $project_task),
        ];

        // end Task Count

        // Expense
        $expAmt = 0;
        foreach ($project->expense as $expense) {
            $expAmt += $expense->amount;
        }

        $project_data['expense'] = [
            'allocated' => $project->budget,
            'total' => $expAmt,
            'percentage' => Utility::getPercentage($expAmt, $project->budget),
        ];
        // end expense

        // Users Assigned
        $total_users = User::where('created_by', '=', $usr->id)->count();

        $project_data['user_assigned'] = [
            'total' => number_format($total_users) . '/' . number_format($total_users),
            'percentage' => Utility::getPercentage($total_users, $total_users),
        ];
        // end users assigned

        // Day left
        $total_day = Carbon::parse($project->start_date)->diffInDays(Carbon::parse($project->end_date));
        $remaining_day = Carbon::parse($project->start_date)->diffInDays(now());
        $project_data['day_left'] = [
            'day' => number_format($remaining_day) . '/' . number_format($total_day),
            'percentage' => Utility::getPercentage($remaining_day, $total_day),
        ];
        // end Day left

        // Open Task
        $remaining_task = ProjectTask::where('project_id', '=', $project->id)->where('is_complete', '=', 0)->where('created_by', \Auth::user()->creatorId())->count();
        $total_task = $project->tasks->count();

        $project_data['open_task'] = [
            'tasks' => number_format($remaining_task) . '/' . number_format($total_task),
            'percentage' => Utility::getPercentage($remaining_task, $total_task),
        ];
        // end open task

        // Milestone
        $total_milestone = $project->milestones()->count();
        $complete_milestone = $project->milestones()->where('status', 'LIKE', 'complete')->count();
        $project_data['milestone'] = [
            'total' => number_format($complete_milestone) . '/' . number_format($total_milestone),
            'percentage' => Utility::getPercentage($complete_milestone, $total_milestone),
        ];
        // End Milestone

        // Time spent

        $times = $project->timesheets()->where('created_by', '=', $usr->id)->pluck('time')->toArray();
        $totaltime = str_replace(':', '.', Utility::timeToHr($times));
        $project_data['time_spent'] = [
            'total' => number_format($totaltime) . '/' . number_format($totaltime),
            'percentage' => Utility::getPercentage(number_format($totaltime), $totaltime),
        ];
        // end time spent

        // Allocated Hours
        $hrs = Project::projectHrs($project->id);

        $project_data['task_allocated_hrs'] = [
            'hrs' => number_format($hrs['allocated']) . '/' . number_format($hrs['allocated']),
            'percentage' => Utility::getPercentage($hrs['allocated'], $hrs['allocated']),
        ];
        // end allocated hours

        // Chart
        $seven_days = Utility::getLastSevenDays();
        $chart_task = [];
        $chart_timesheet = [];
        $cnt = 0;
        $cnt1 = 0;

        foreach (array_keys($seven_days) as $k => $date) {
            $task_cnt = $project->tasks()->where('is_complete', '=', 1)->whereRaw("find_in_set('" . $usr->id . "',assign_to)")->where('marked_at', 'LIKE', $date)->count();
            $arrTimesheet = $project->timesheets()->where('created_by', '=', $usr->id)->where('date', 'LIKE', $date)->pluck('time')->toArray();

            // Task Chart Count
            $cnt += $task_cnt;

            // Timesheet Chart Count
            $timesheet_cnt = str_replace(':', '.', Utility::timeToHr($arrTimesheet));
            $cn[] = $timesheet_cnt;
            $cnt1 += $timesheet_cnt;

            $chart_task[] = $task_cnt;
            $chart_timesheet[] = $timesheet_cnt;
        }

        $project_data['task_chart'] = [
            'chart' => $chart_task,
            'total' => $cnt,
        ];
        $project_data['timesheet_chart'] = [
            'chart' => $chart_timesheet,
            'total' => $cnt1,
        ];

        $last_task = TaskStage::orderBy('order', 'DESC')->where('created_by', \Auth::user()->creatorId())->first();

        // end chart

        $stages = TaskStage::orderBy('order')
            ->where('created_by', Auth::user()->creatorId())
            ->get();

        $totalTasks = ProjectTask::where('project_id', $project->id)->count();

        foreach ($stages as $stage) {
            $taskCount = ProjectTask::where('project_id', $project->id)
                ->where('stage_id', $stage->id)
                ->count();

            $stage->task_count = $taskCount;
            $stage->percentage = $totalTasks > 0 ? round(($taskCount / $totalTasks) * 100, 2) : 0;
        }

        // ======================
        // Checklist Progress
        // ======================
        $allTasks = $project->tasks;

        // Count checklist items
        $totalChecklist = 0;
        $completedChecklist = 0;

        foreach ($allTasks as $task) {
            if (isset($task->checklist) && is_iterable($task->checklist)) {
                $totalChecklist += count($task->checklist);
                $completedChecklist += collect($task->checklist)->where('status', 1)->count();
            }
        }

        // Calculate overall progress
        if ($totalChecklist > 0) {
            $projectProgress = round(($completedChecklist / $totalChecklist) * 100, 2);
        } else {
            $projectProgress = 0;
        }

        return view('company.projects.view', compact('project', 'project_data', 'last_task', 'stages', 'totalTasks', 'projectProgress'));
    }

    // Project Gantt Chart
    public function gantt($projectID, $duration = 'Week')
    {
        $project = Project::find($projectID);
        $tasks = [];

        if ($project) {
            $tasksobj = $project->tasks;

            foreach ($tasksobj as $task) {
                $tmp = [];
                $tmp['id'] = 'task_' . $task->id;
                $tmp['name'] = $task->name;
                $tmp['start'] = $task->start_date;
                $tmp['end'] = $task->end_date;
                $tmp['custom_class'] = (empty($task->priority_color) ? '#ecf0f1' : $task->priority_color);
                $tmp['progress'] = str_replace('%', '', $task->taskProgress($task)['percentage']);
                $tmp['extra'] = [
                    'priority' => ucfirst(__($task->priority)),
                    'comments' => count($task->comments),
                    'duration' => Utility::getDateFormated($task->start_date) . ' - ' . Utility::getDateFormated($task->end_date),
                ];
                $tasks[] = $tmp;
            }
        }

        return view('company.projects.gantt', compact('project', 'tasks', 'duration'));
    }

    public function ganttPost($projectID, Request $request)
    {
        $project = Project::find($projectID);

        if ($project) {
            $id = trim($request->task_id, 'task_');
            $task = ProjectTask::find($id);
            $task->start_date = $request->start;
            $task->end_date = $request->end;
            $task->save();

            return response()->json(
                [
                    'is_success' => true,
                    'message' => __("Time Updated"),
                ], 200
            );
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'message' => __("Something is wrong."),
                ], 400
            );
        }
    }

    public function bug($project_id)
    {
        $user = Auth::user();
        $project = Project::find($project_id);

        if (!empty($project) && $project->created_by == Auth::user()->creatorId()) {

            if ($user->type != 'company') {
//                if (\Auth::user()->type == 'client') {
                $bugs = Bug::where('project_id', $project->id)->get();
//                } else {
//                    $bugs = Bug::where('project_id', $project->id)->whereRaw("find_in_set('" . $user->id . "',assign_to)")->get();
//                }
            }

            if ($user->type == 'company') {
                $bugs = Bug::where('project_id', '=', $project_id)->get();
            }
            $bugStatus = BugStatus::where('created_by', Auth::user()->creatorId())
            ->orderBy('order', 'ASC')
            ->get();
            $statusCounts = [];
            foreach ($bugStatus as $bs) {
                $statusCounts[$bs->id] = $bugs->filter(fn($b) => $b->status == $bs->id)->count();
            }

            return view('company.project_bugs.bug', compact('project', 'bugs', 'bugStatus', 'statusCounts'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function bugCreate($project_id)
    {
        $priority = Bug::$priority;
        $status = BugStatus::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('title', 'id');
        $project_user = ProjectUser::where('project_id', $project_id)->get();

        $users = [];
        foreach ($project_user as $key => $user) {

            $user_data = User::find($user->user_id);
            if (isset($user_data)) {
                $key = $user->user_id;
                $user_name = !empty($user_data) ? $user_data->name : '';
                $users[$key] = $user_name;
            }
        }

        return view('company.project_bugs.bugCreate', compact('status', 'project_id', 'priority', 'users'));
    }

    function bugNumber()
    {
        $latest = Bug::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->bug_id + 1;
    }

public function bugStore(Request $request, $project_id)
{
    $validator = \Validator::make(
        $request->all(),
        [
            'title'      => 'required',
            'priority'   => 'required',
            'status'     => 'required',
            'assign_to'  => 'required',
            'start_date' => 'required',
            'due_date'   => 'required',
            'files'      => 'nullable|array',
            'files.*'    => 'nullable|file|max:20480',
        ]
    );
 
    if ($validator->fails()) {
        /* Return JSON error so fetch() in blade can handle it */
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'error' => $validator->getMessageBag()->first()], 422);
        }
        return redirect()->route('organization.task.bug', $project_id)
            ->with('error', $validator->getMessageBag()->first());
    }
 
    $usr     = \Auth::user();
    $project = Project::findOrFail($project_id);
 
    /* ── Generate task UID ── */
    $prefix  = ($project->project_id ?? 'TASK') . 'BUG';
    $lastBug = Bug::where('task_uid', 'like', $prefix . '%')->orderByDesc('id')->first();
 
    if ($lastBug && preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $lastBug->task_uid, $matches)) {
        $nextNumber = (int) $matches[1] + 1;
    } else {
        $nextNumber = 1;
    }
 
    $taskUid = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
 
    /* ── Create Bug ── */
    $bug             = new Bug();
    $bug->bug_id     = $this->bugNumber();
    $bug->project_id = $project_id;
    $bug->task_uid   = $taskUid;
    $bug->title      = ucwords($request->title);
    $bug->priority   = $request->priority;
    $bug->start_date = $request->start_date;
    $bug->due_date   = $request->due_date;
    $bug->description= $request->description;
    $bug->status     = $request->status;
    $bug->assign_to  = $request->assign_to;
    $bug->created_by = $usr->id;
    $bug->save();
 
    ActivityLog::create([
        'user_id'    => $usr->id,
        'project_id' => $project_id,
        'log_type'   => 'Create Bug',
        'remark'     => json_encode(['title' => $bug->title]),
    ]);
 
    /* ── Multi-file upload — direct move, no Utility injection ── */
    if ($request->hasFile('files')) {
 
        $createdBy   = User::find($usr->created_by);
        $companySlug = \Str::slug($createdBy->name ?? 'company');
 
        /*
         * Utility::upload_file() local path:
         *   $request->file->move(storage_path($path), $name)
         * → storage/uploads/companies/.../files/
         *
         * get_file() → Storage::disk('local')->url($path)
         * local disk url = /storage/app/$path
         * So DB stores: "uploads/companies/.../files/name.ext"
         */
        $dir         = "uploads/companies/{$companySlug}/projects/{$project_id}/bugs/{$bug->id}/files";
        $absoluteDir = storage_path($dir);   /* storage/uploads/... */
 
        if (!file_exists($absoluteDir)) {
            mkdir($absoluteDir, 0775, true);
        }
 
        foreach ($request->file('files') as $uploadedFile) {
 
            if (!$uploadedFile || !$uploadedFile->isValid()) {
                continue;
            }
 
            $original        = $uploadedFile->getClientOriginalName();
            $extension       = $uploadedFile->getClientOriginalExtension();
            $nameOnly        = pathinfo($original, PATHINFO_FILENAME);
            $fileNameToStore = \Str::slug($nameOnly) . '_' . time() . '_' . uniqid() . '.' . $extension;
 
            /* ✅ getSize() BEFORE move() — after move /tmp file is gone */
            $fileSize = round(($uploadedFile->getSize() / 1024) / 1024, 2) . ' MB';
 
            try {
                $uploadedFile->move($absoluteDir, $fileNameToStore);
            } catch (\Exception $e) {
                \Log::error('Bug file upload error: ' . $e->getMessage() . ' | file: ' . $original);
                continue;
            }
 
            /* Relative path stored in DB — get_file() prepends storage/app/ */
            $relativePath = $dir . '/' . $fileNameToStore;
 
            BugFile::create([
                'bug_id'     => $bug->id,
                'file'       => $relativePath,
                'name'       => $original,
                'extension'  => '.' . $extension,
                'file_size'  => $fileSize,   /* ✅ captured before move */
                'created_by' => $usr->id,
                'user_type'  => $usr->type,
            ]);
        }
    }
 
    /* ── Return JSON for AJAX / redirect for normal form ── */
    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'success'  => true,
            'message'  => __('Bug successfully created.'),
            'redirect' => route('organization.task.bug', $project_id),
        ]);
    }
 
    return redirect()->route('organization.task.bug', $project_id)
        ->with('success', __('Bug successfully created.'));
}

    public function bugEdit($project_id, $bug_id)
    {
        $bug = Bug::find($bug_id);
        $priority = Bug::$priority;
        $status = BugStatus::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('title', 'id');
        $project_user = ProjectUser::where('project_id', $project_id)->get();
        $users = array();
        foreach ($project_user as $user) {
            $user_data = User::where('id', $user->user_id)->first();
            if (isset($user_data)) {
                $key = $user->user_id;
                $user_name = !empty($user_data) ? $user_data->name : '';
                $users[$key] = $user_name;
            }
        }

        return view('company.project_bugs.bugEdit', compact('status', 'project_id', 'priority', 'users', 'bug'));

    }

    public function bugUpdate(Request $request, $project_id, $bug_id)
    {

        $validator = \Validator::make(
            $request->all(), [
                'title' => 'required',
                'priority' => 'required',
                'status' => 'required',
                'assign_to' => 'required',
                'start_date' => 'required',
                'due_date' => 'nullable',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->route('organization.task.bug', $project_id)->with('error', $messages->first());
        }
        $bug = Bug::find($bug_id);
        $bug->title = ucwords($request->title);
        $bug->priority = $request->priority;
        $bug->start_date = $request->start_date;
        $bug->due_date = $request->due_date;
        $bug->description = $request->description;
        $bug->status = $request->status;
        $bug->assign_to = $request->assign_to;
        $bug->save();

        return redirect()->route('organization.task.bug', $project_id)->with('success', __('Bug successfully created.'));
    }

    public function bugDestroy($project_id, $bug_id)
    {
        $bug = Bug::find($bug_id);
        $bug->delete();

        return redirect()->route('organization.task.bug', $project_id)->with('success', __('Bug successfully deleted.'));
    }

    public function bugKanban($project_id)
    {
        $user = Auth::user();

        $project = Project::find($project_id);

        if (!empty($project) && $project->created_by == $user->creatorId()) {
            if ($user->type != 'company') {
                $bugStatus = BugStatus::where('created_by', '=', Auth::user()->creatorId())->orderBy('order', 'ASC')->get();
            }

            if ($user->type == 'company' || $user->type == 'client') {
                $bugStatus = BugStatus::where('created_by', '=', Auth::user()->creatorId())->orderBy('order', 'ASC')->get();
            }
            return view('company.project_bugs.bugKanban', compact('project', 'bugStatus'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

  public function bugKanbanOrder(Request $request)
{
    $post = $request->all();

    // ── Move the dragged bug to the new status column ──
    $bug = Bug::find($post['bug_id']);
    if (!$bug) {
        return response()->json(['success' => false, 'message' => 'Bug not found'], 404);
    }

    $status = BugStatus::find($post['status_id']);
    if ($status) {
        $bug->status = $post['status_id'];
        $bug->save();
    }

    // ── Re-order all bugs in the target column ──
    if (!empty($post['sort']) && is_array($post['sort'])) {
        foreach ($post['sort'] as $key => $item) {
            if ($item && $item !== 'null') {
                $bug_order = Bug::find($item);
                if ($bug_order) {
                    $bug_order->order  = $key;
                    $bug_order->status = $post['status_id'];
                    $bug_order->save();
                }
            }
        }
    }

    return response()->json(['success' => true]);
}

    public function bugShow($project_id, $bug_id)
    {
        $bug = Bug::with([
            'comments.createdBy.employee',
            'bugFiles'
        ])->findOrFail($bug_id);

        return view('company.project_bugs.bugShow', compact('bug'));
    }

    public function bugCommentStore(Request $request, $project_id, $bug_id)
    {
        $request->validate([
            'comment' => 'required|string'
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        // Gender-based avatar selection
        if ($employee && $employee->gender == GENDER_MALE) {
            $avatar = asset('assets/assestsnew/menimg.png');
        } elseif ($employee && $employee->gender == GENDER_FEMALE) {
            $avatar = asset('assets/assestsnew/femaile-report.svg');
        } else {
            $profile = \App\Models\Utility::get_file($user->avatar);

            $avatar = $user->avatar
                ? $profile
                : asset('assets/assestsnew/menimg.png');
        }

        $comment = BugComment::create([
            'bug_id' => $bug_id,
            'comment' => $request->comment,
            'created_by' => $user->authId(),
            'user_type' => $user->type,
        ]);

        return response()->json([
            'is_success' => true,
            'message' => __("Bug comment successfully created."),
            'data' => [
                'comment' => $comment->comment,
                'deleteUrl' => route('organization.bug.comment.destroy', $comment->id),
                'name' => $user->name,
                'avatar' => $avatar,
            ]
        ]);
    }

    public function bugCommentDestroy($comment_id)
    {
        $comment = BugComment::findOrFail($comment_id);
        $comment->delete();
        return response()->json(['success' => true]);
    }

    public function bugCommentStoreFile(Request $request, $bug_id)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $user = \Auth::user();
        $bug = Bug::findOrFail($bug_id);
        $project_id = $bug->project_id;

        // Company name from creator
        $createdBy = User::find($user->created_by);
        $companySlug = \Str::slug($createdBy->name ?? 'company');

        // Folder structure
        $dir = "uploads/companies/{$companySlug}/projects/{$project_id}/bugs/{$bug->id}/files";

        $fileUrl = null;
        $relativePath = null;

        if ($request->hasFile('file')) {

            $file = $request->file('file');
            $original = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $nameOnly = pathinfo($original, PATHINFO_FILENAME);

            $fileNameToStore = $nameOnly . '_' . time() . '.' . $extension;

            // 🔧 Use Utility file upload
            $path = \App\Models\Utility::upload_file($request, 'file', $fileNameToStore, $dir, []);

            if ($path['flag'] != 1) {
                return response()->json(['error' => $path['msg']], 400);
            }

            $fileUrl = $path['url'];
            $relativePath = "{$dir}/{$fileNameToStore}";
        }

        // Save DB record
        $bugFile = BugFile::create([
            'bug_id' => $bug->id,
            'file' => $relativePath,
            'name' => $original,
            'extension' => "." . $extension,
            'file_size' => round(($file->getSize() / 1024) / 1024, 2) . ' MB',
            'created_by' => $user->authId(),
            'user_type' => $user->type,
        ]);

        // Prepare frontend data
        $bugFile->file_url = \App\Models\Utility::get_file($relativePath);
        $bugFile->deleteUrl = route('organization.bug.comment.file.destroy', $bugFile->id);

        return response()->json($bugFile);
    }

    public function bugCommentDestroyFile(Request $request, $file_id)
    {
        $commentFile = BugFile::findOrFail($file_id);

        // Correct original upload path
        $filePath = storage_path('app/' . $commentFile->file);

        if (file_exists($filePath)) {
            \File::delete($filePath);
        }

        $commentFile->delete();

        return response()->json(['success' => true]);
    }

    public function tracker($id)
    {
        $treckers = TimeTracker::where('project_id', $id)->get();
        return view('company.time_trackers.index', compact('treckers'));
    }

    public function getProjectChart($arrParam)
    {
        $arrDuration = [];
        if ($arrParam['duration'] && $arrParam['duration'] == 'week') {
            $previous_week = Utility::getFirstSeventhWeekDay(-1);
            foreach ($previous_week['datePeriod'] as $dateObject) {
                $arrDuration[$dateObject->format('Y-m-d')] = $dateObject->format('D');
            }
        }

        $arrTask = [
            'label' => [],
            'color' => [],
        ];
        $stages = TaskStage::where('created_by', '=', $arrParam['created_by'])->orderBy('order');

        foreach ($arrDuration as $date => $label) {
            $objProject = projectTask::select('stage_id', \DB::raw('count(*) as total'))->whereDate('updated_at', '=', $date)->groupBy('stage_id');

            if (isset($arrParam['project_id'])) {
                $objProject->where('project_id', '=', $arrParam['project_id']);
            }


            $data = $objProject->pluck('total', 'stage_id')->all();

            foreach ($stages->pluck('name', 'id')->toArray() as $id => $stage) {
                $arrTask[$id][] = isset($data[$id]) ? $data[$id] : 0;
            }
            $arrTask['label'][] = __($label);
        }
        $arrTask['stages'] = $stages->pluck('name', 'id')->toArray();

        return $arrTask;
    }

    public function copylink_setting_create($projectID)
    {
        $objUser = Auth::user();
        $project = Project::select('projects.*')->join('project_users', 'projects.id', '=', 'project_users.project_id')->where('project_users.user_id', '=', $objUser->id)->where('projects.id', '=', $projectID)->first();
        $result = json_decode($project->copylinksetting);
        return view('company.projects.copylink_setting', compact('project', 'projectID', 'result'));
    }

    public function copylinksetting(Request $request, $id)
    {
        $objUser = Auth::user();

        $data = [];
        $data['basic_details'] = isset($request->basic_details) ? 'on' : 'off';
        $data['member'] = isset($request->member) ? 'on' : 'off';
        $data['milestone'] = isset($request->milestone) ? 'on' : 'off';
        $data['client'] = isset($request->client) ? 'on' : 'off';
        $data['progress'] = isset($request->progress) ? 'on' : 'off';
        $data['activity'] = isset($request->activity) ? 'on' : 'off';
        $data['attachment'] = isset($request->attachment) ? 'on' : 'off';
        $data['bug_report'] = isset($request->bug_report) ? 'on' : 'off';
        $data['expense'] = isset($request->expense) ? 'on' : 'off';
        $data['task'] = isset($request->task) ? 'on' : 'off';
        $data['tracker_details'] = isset($request->tracker_details) ? 'on' : 'off';
        $data['timesheet'] = isset($request->timesheet) ? 'on' : 'off';
        $data['password_protected'] = isset($request->password_protected) ? 'on' : 'off';
        $project = Project::select('projects.*')
            ->join('project_users', 'projects.id', '=', 'project_users.project_id')
            ->where('project_users.user_id', '=', $objUser->id)
            ->where('projects.id', '=', $id)->first();

        if (isset($request->password_protected) && $request->password_protected == 'on') {
            $project->password = base64_encode($request->password);

        } else {
            $project->password = null;
        }


        $project->copylinksetting = (count($data) > 0) ? json_encode($data) : null;
        $project->save();
        return redirect()->back()->with('success', __('Copy Link Setting Save Successfully!'));
    }

    public function projectlink(Request $request, $project_id, $lang = '')
    {
        try {
            $id = \Illuminate\Support\Facades\Crypt::decrypt($project_id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Project Not Found.'));
        }

        $id = \Illuminate\Support\Facades\Crypt::decrypt($project_id);

        $project = Project::find($id);

        $data = [];
        $data['basic_details'] = isset($request->basic_details) ? 'on' : 'off';
        $data['member'] = isset($request->member) ? 'on' : 'off';
        $data['milestone'] = isset($request->milestone) ? 'on' : 'off';
        $data['activity'] = isset($request->activity) ? 'on' : 'off';
        $data['attachment'] = isset($request->attachment) ? 'on' : 'off';
        $data['bug_report'] = isset($request->bug_report) ? 'on' : 'off';
        $data['expense'] = isset($request->expense) ? 'on' : 'off';
        $data['task'] = isset($request->task) ? 'on' : 'off';
        $data['tracker_details'] = isset($request->tracker_details) ? 'on' : 'off';
        $data['timesheet'] = isset($request->timesheet) ? 'on' : 'off';
        $data['password_protected'] = isset($request->password_protected) ? 'on' : 'off';


        if (Auth::user() != null) {
            $usr = Auth::user();
        } else {
            $usr = User::where('id', $project->created_by)->first();
        }

        $user_projects = $usr->projects->pluck('id')->toArray();

        $project_data = [];

        // Task Count
        $project_task = $project->tasks->count();

        $project_done_task = $project->tasks->where('is_complete', '=', 1)->count();

        $project_data['task'] = [
            'total' => number_format($project_task),
            'done' => number_format($project_done_task),
            'percentage' => Utility::getPercentage($project_done_task, $project_task),
        ];

        // end Task Count


        // Users Assigned
        $total_users = User::where('created_by', '=', $usr->id)->count();

        $project_data['user_assigned'] = [
            'total' => number_format($total_users) . '/' . number_format($total_users),
            'percentage' => Utility::getPercentage($total_users, $total_users),
        ];
        // End Users Assigned


        // Day left
        $total_day = Carbon::parse($project->start_date)->diffInDays(Carbon::parse($project->end_date));
        $remaining_day = Carbon::parse($project->start_date)->diffInDays(now());
        $project_data['day_left'] = [
            'day' => number_format($remaining_day) . '/' . number_format($total_day),
            'percentage' => Utility::getPercentage($remaining_day, $total_day),
        ];
        // end day left

        if ($usr->checkProject($project->id) == 'Owner') {
            $remaining_task = ProjectTask::where('project_id', '=', $project->id)->where('is_complete', '=', 0)->count();
            $total_task = ProjectTask::where('project_id', '=', $project->id)->count();
        } else {
            $remaining_task = ProjectTask::where('project_id', '=', $project->id)->where('is_complete', '=', 0)->whereRaw("find_in_set('" . $usr->id . "',assign_to)")->count();
            $total_task = ProjectTask::where('project_id', '=', $project->id)->whereRaw("find_in_set('" . $usr->id . "',assign_to)")->count();
        }
        $project_data['open_task'] = [
            'tasks' => number_format($remaining_task) . '/' . number_format($total_task),
            'percentage' => Utility::getPercentage($remaining_task, $total_task),
        ];

        // Milestone
        $total_milestone = $project->milestones()->count();

        $complete_milestone = $project->milestones()->where('status', 'LIKE', 'complete')->count();
        $project_data['milestone'] = [
            'total' => number_format($complete_milestone) . '/' . number_format($total_milestone),
            'percentage' => Utility::getPercentage($complete_milestone, $total_milestone),
        ];
        // End Milestone


        // Chart
        $seven_days = Utility::getLastSevenDays();
        $chart_task = [];
        $chart_timesheet = [];
        $cnt = 0;
        $cnt1 = 0;

        foreach (array_keys($seven_days) as $k => $date) {
            if ($usr->checkProject($project->id) == 'Owner') {
                $task_cnt = $project->tasks()->where('is_complete', '=', 1)->where('marked_at', 'LIKE', $date)->count();
                $arrTimesheet = $project->timesheets()->where('date', 'LIKE', $date)->pluck('time')->toArray();
            } else {
                $task_cnt = $project->tasks()->where('is_complete', '=', 1)->whereRaw("find_in_set('" . $usr->id . "',assign_to)")->where('marked_at', 'LIKE', $date)->count();
                $arrTimesheet = $project->timesheets()->where('created_by', '=', $usr->id)->where('date', 'LIKE', $date)->pluck('time')->toArray();
            }

            // Task Chart Count
            $cnt += $task_cnt;

            // Timesheet Chart Count
            $timesheet_cnt = str_replace(':', '.', Utility::timeToHr($arrTimesheet));
            $cn[] = $timesheet_cnt;
            $cnt1 += number_format($timesheet_cnt, 2);

            $chart_task[] = $task_cnt;
            $chart_timesheet[] = number_format($timesheet_cnt, 2);
        }

        // Allocated Hours
        $hrs = Project::projectHrs($project->id);


        $project_data['task_allocated_hrs'] = [
            'hrs' => number_format($hrs['allocated']) . '/' . number_format($hrs['allocated']),
            'percentage' => Utility::getPercentage($hrs['allocated'], $hrs['allocated']),
        ];

        // end allocated hours

        // Time spent
        if ($usr->checkProject($project->id) == 'Owner') {
            $times = $project->timesheets->pluck('time')->toArray();
        } else {
            $times = $project->timesheets()->where('created_by', '=', $usr->id)->pluck('time')->toArray();
        }
        $totaltime = str_replace(':', '.', Utility::timeToHr($times));
        $estimatedtime = $project->estimated_hrs != '' ? $project->estimated_hrs : '0';
        $project_data['time_spent'] = [
            'total' => number_format($totaltime) . '/' . number_format($estimatedtime),
            'percentage' => Utility::getPercentage(number_format($totaltime), $estimatedtime),
        ];
        // end time spent

        $project_data['task_chart'] = [
            'chart' => $chart_task,
            'total' => $cnt,
        ];

        $project_data['timesheet_chart'] = [
            'chart' => $chart_timesheet,
            'total' => $cnt1,
        ];
        if (isset($request->milestone) && in_array("milestone", $request->milestone)) {
            $milestones = Milestone::where('project_id', $project->id)->get();

            foreach ($milestones as $milestone) {

                $post = new Milestone();
                $post['project_id'] = $milestone->id;
                $post['title'] = $milestone->title;
                $post['status'] = $milestone->status;
                $post['description'] = $milestone->description;
                $post->save();
            }
        }

        if (isset($request->task) && in_array("task", $request->task)) {
            $tasks = ProjectTask::where('project_id', $project->id)->where('stage_id', $stage->id)->get();
            $activities = ActivityLog::where('project_id', $project->id)->where('task_id', $task->id)->get();

            foreach ($activities as $activity) {

                $activitylog = new ActivityLog();
                $activitylog['user_id'] = $activity->user_id;
                $activitylog['project_id'] = $activity->id;
                $activitylog['task_id'] = $activity->id;
                $activitylog['log_type'] = $activity->log_type;
                $activitylog['remark'] = $activity->remark;
                $activitylog->save();
            }
        }

        $stages = TaskStage::where('project_id', '=', $id)->orderBy('order')->get();
        foreach ($stages as &$status) {
            $stageClass[] = 'task-list-' . $status->id;
            $task = ProjectTask::where('project_id', '=', $id);

            // check project is shared or owner
            if ($usr->checkProject($project_id) == 'Shared') {
                $task->whereRaw(
                    "find_in_set('" . $usr->id . "',assign_to)"
                );
            }
            //end

            $task->orderBy('order');
            $status['tasks'] = $task->where('stage_id', '=', $status->id)->get();
        }

        $treckers = TimeTracker::where('project_id', $id)->where('created_by', $usr->id)->get();

        //bug report

        $bugs = Bug::where('project_id', $project->id)->get();


        //task
        $tasks = ProjectTask::where('project_id', $project->id)->get();

        //lang


        $lang = !empty($lang) ? $lang : (!empty($usr->lang) ? $usr->lang : env('DEFAULT_ADMIN_LANG'));

        \App::setLocale($lang);

//        dd($lang);


        if (\Session::get('copy_pass_true' . $id) == $project->password . '-' . $id) {

            return view('company.projects.copylink', compact('data', 'project', 'project_data', 'stages', 'treckers', 'usr', 'bugs', 'tasks', 'lang'));
        } else {

            if (!isset(json_decode($project->copylinksetting)->password_protected) || json_decode($project->copylinksetting)->password_protected != 'on') {

                return view('company.projects.copylink', compact('data', 'project', 'project_data', 'stages', 'treckers', 'usr', 'lang', 'tasks', 'bugs'));

            } elseif (isset(json_decode($project->copylinksetting)->password_protected) && json_decode($project->copylinksetting)->password_protected == 'on' && $request->password == base64_decode($project->password)) {

                \Session::put('copy_pass_true' . $id, $project->password . '-' . $id);


                return view('company.projects.copylink', compact('data', 'project', 'project_data', 'stages', 'treckers', 'usr', 'lang', 'bugs', 'tasks'));

            } else {
                return view('company.projects.copylink_password', compact('id'));
            }
        }

    }
}
