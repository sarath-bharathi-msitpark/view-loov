<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Bug;
use App\Models\BugStatus;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\TaskChecklist;
use App\Models\TaskComment;
use App\Models\TaskFile;
use App\Models\TaskStage;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProjectTaskController extends Controller
{
    public function index($project_id)
    {
        $project = Project::where('id', $project_id)
            ->where('created_by', Auth::user()->creatorId())
            ->first();

        if ($project != null) {

            $stages = TaskStage::orderBy('order')
                ->where('created_by', Auth::user()->creatorId())
                ->get();

            foreach ($stages as $status) {
                $stageClass[] = 'task-list-' . $status->id;
                $task = ProjectTask::where('project_id', '=', $project_id);
                $task->orderBy('order');
                $status['tasks'] = $task->where('stage_id', '=', $status->id)->get();
                $status->task_count = $task->count();
            }

            return view('company.project_task.index', compact('stages', 'stageClass', 'project'));
        } else {
            return redirect()->route('organization.projects.index')->with('error', __('Project not found'));
        }
    }

    /**
     * @param $project_id
     * @param $stage_id
     * @return Factory|View|Application|object
     */
    public function create($project_id, $stage_id)
    {
        $project = Project::find($project_id);
        $hrs = Project::projectHrs($project_id);
        $settings = Utility::settings();

        return view('company.project_task.create', compact('project_id', 'stage_id', 'project', 'hrs', 'settings'));
    }

    /**
     * @param Request $request
     * @param $project_id
     * @param $stage_id
     * @return RedirectResponse
     */
    public function store(Request $request, $project_id, $stage_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'estimated_hrs' => 'required',
            'priority' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', Utility::errorFormat($validator->getMessageBag()));
        }

        $usr = Auth::user();
        $project = Project::findOrFail($project_id);

        $prefix = $project->project_id ?? 'TASK';

        $lastTask = ProjectTask::where('task_id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($lastTask && preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $lastTask->task_id, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        $taskId = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $post = [
            'project_id' => $project->id,
            'task_id' => $taskId,
            'stage_id' => $stage_id,
            'assign_to' => $request->assign_to,
            'created_by' => $usr->creatorId(),
            'start_date' => date("Y-m-d H:i:s", strtotime($request->start_date)),
            'end_date' => date("Y-m-d H:i:s", strtotime($request->end_date)),
            'name' => $request->name,
            'estimated_hrs' => $request->estimated_hrs,
            'priority' => $request->priority,
            'description' => $request->description,
        ];

        $task = ProjectTask::create($post);

        ActivityLog::create([
            'user_id' => $usr->id,
            'project_id' => $project_id,
            'task_id' => $task->id,
            'log_type' => 'Create Task',
            'remark' => json_encode(['title' => $task->name]),
        ]);

        return redirect()->back()->with('success', __('Task added successfully.'));
    }

    // For Taskboard View
    public function taskBoard($view)
    {
        if ($view == 'list') {
            return view('company.project_task.taskboard', compact('view'));
        } else {
            $usr = Auth::user();
            if (\Auth::user()->type == 'client') {
                $user_projects = Project::where('client_id', \Auth::user()->id)->pluck('id', 'id')->toArray();
            } elseif (\Auth::user()->type != 'client') {
                $user_projects = $usr->projects()->pluck('project_id', 'project_id')->toArray();
            }

            $tasks = ProjectTask::whereIn('project_id', $user_projects);
            if (\Auth::user()->type != 'company') {
                if (\Auth::user()->type == 'client') {
                    $tasks->where('created_by', \Auth::user()->creatorId());

                } else {
                    $tasks->whereRaw("find_in_set('" . $usr->id . "',assign_to)");
                }
            } else {
                $tasks->where('created_by', \Auth::user()->creatorId());
            }

            $tasks = $tasks->get();
            return view('company.project_task.grid', compact('tasks', 'view'));

        }

        return redirect()->back()->with('error', __('Permission Denied.'));

    }


    // For Load Task using ajax
    public function taskboardView(Request $request)
    {

        $usr = Auth::user();
        if (\Auth::user()->type == 'client') {
            $user_projects = Project::where('client_id', \Auth::user()->id)->pluck('id', 'id')->toArray();
        } elseif (\Auth::user()->type != 'client') {
            $user_projects = $usr->projects()->pluck('project_id', 'project_id')->toArray();
        }
        if ($request->ajax() && $request->has('view') && $request->has('sort')) {
            $sort = explode('-', $request->sort);
//            $task = ProjectTask::whereIn('project_id', $user_projects)->get();
            $tasks = ProjectTask::whereIn('project_id', $user_projects)->orderBy($sort[0], $sort[1]);
            if (\Auth::user()->type != 'company') {
                if (\Auth::user()->type == 'client') {
                    $tasks->where('created_by', \Auth::user()->creatorId());

                } else {
                    $tasks->whereRaw("find_in_set('" . $usr->id . "',assign_to)");
                }
            } else {
                $tasks->where('created_by', \Auth::user()->creatorId());
            }
            if (!empty($request->keyword)) {
                $tasks->where('name', 'LIKE', $request->keyword . '%');
            }
//            dd($tasks->get()->toArray());
            if (!empty($request->status)) {
                $todaydate = date('Y-m-d');

                // For Optimization
                $status = $request->status;
                foreach ($status as $k => $v) {
                    if ($v == 'due_today' || $v == 'over_due' || $v == 'starred' || $v == 'see_my_tasks') {
                        unset($status[$k]);
                    }
                }
                // end

                if (count($status) > 0) {
                    $tasks->whereIn('priority', $status);
                }


//                if(in_array('see_my_tasks', $request->status) && \Auth::user()->type!='company')
//                {
//                    $tasks->whereRaw("find_in_set('" . $usr->id . "',assign_to)");
//                }

                if (in_array('due_today', $request->status)) {
                    $tasks->where('end_date', $todaydate);
                }

                if (in_array('over_due', $request->status)) {
                    $tasks->where('end_date', '<', $todaydate);
                }

                if (in_array('starred', $request->status)) {
                    $tasks->where('is_favourite', '=', 1);
                }
            }

            $tasks = $tasks->with(['project'])->get();
            $view = $request->view;
            $returnHTML = view('company.project_task.' . $request->view, compact('tasks', 'view'))->render();

            return response()->json(
                [
                    'success' => true,
                    'html' => $returnHTML,
                ]
            );
        }
    }


    // For Taskboard View
    public function allBugList($view)
    {
        $bugStatus = BugStatus::where('created_by', \Auth::user()->creatorId())->get();
        if (Auth::user()->type == 'company') {
            $bugs = Bug::where('created_by', \Auth::user()->creatorId())->with(['project', 'createdBy', 'projectBUg'])->get();
        } elseif (Auth::user()->type != 'company') {
            if (\Auth::user()->type == 'client') {
                $user_projects = Project::where('client_id', \Auth::user()->id)->pluck('id', 'id')->toArray();
                $bugs = Bug::whereIn('project_id', $user_projects)->where('created_by', \Auth::user()->creatorId())->with(['project', 'createdBy'])->get();
            } else {
                $bugs = Bug::where('created_by', \Auth::user()->creatorId())->whereRaw("find_in_set('" . \Auth::user()->id . "',assign_to)")->with(['project', 'createdBy'])->get();
            }
        }
        if ($view == 'list') {
            return view('company.projects.allBugListView', compact('bugs', 'bugStatus', 'view'));
        } else {
            return view('company.projects.allBugGridView', compact('bugs', 'bugStatus', 'view'));
        }
        return redirect()->back()->with('error', __('Permission Denied.'));
    }

    public function show($project_id, $task_id)
    {
        $allow_progress = Project::find($project_id)->task_progress;
        $task = ProjectTask::find($task_id);

        return view('company.project_task.view', compact('task', 'allow_progress'));
    }

    public function changeStage(Request $request, $project_id)
    {
        $task = ProjectTask::find($request->id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $old_stage = $task->stage_id;
        $new_stage = $request->new_stage;

        if ($old_stage == $new_stage) {
            return response()->json(['message' => 'No change in stage']);
        }

        // Update stage
        $task->stage_id = $new_stage;
        $task->is_complete = 0;
        $task->marked_at = null;

        // Check if it’s the last stage
        $last_stage = TaskStage::where('created_by', \Auth::user()->creatorId())
            ->orderBy('order', 'DESC')
            ->first();

        if ($last_stage && $new_stage == $last_stage->id) {
            $task->is_complete = 1;
            $task->marked_at = now();
        }

        $task->save();

        // Add simple activity log
        ActivityLog::create([
            'user_id' => \Auth::id(),
            'project_id' => $project_id,
            'task_id' => $task->id,
            'log_type' => 'Move Task',
            'remark' => json_encode([
                'title' => $task->name,
                'old_stage' => optional(TaskStage::find($old_stage))->name,
                'new_stage' => optional(TaskStage::find($new_stage))->name,
            ]),
        ]);

        return response()->json([
            'message' => 'Stage updated successfully',
            'task_id' => $task->id,
            'new_stage' => $new_stage,
        ]);
    }

    public function showDetail($project_id, $task_id)
    {
        $project = Project::find($project_id);
        $allow_progress = $project->task_progress;
        $task = ProjectTask::findOrFail($task_id);

        $assignedUserIds = $task->assign_to ? explode(',', $task->assign_to) : [];
        $assignedUsers = User::whereIn('id', $assignedUserIds)->get();

        $stages = TaskStage::orderBy('order')
            ->where('created_by', Auth::user()->creatorId())
            ->get();

        $activityLogs = ActivityLog::where('project_id', $project->id)
            ->latest('created_at')
            ->paginate(5);

        return view('company.project_task.view_model', compact(
            'project',
            'task',
            'allow_progress',
            'stages',
            'activityLogs',
            'assignedUsers'
        ));
    }

    public function edit($project_id, $task_id)
    {
        $project = Project::find($project_id);
        $task = ProjectTask::find($task_id);
        $hrs = Project::projectHrs($project_id);

        return view('company.project_task.edit', compact('project', 'task', 'hrs'));
    }

    public function update(Request $request, $project_id, $task_id)
    {

        $validator = Validator::make(
            $request->all(), [
                'name' => 'required',
                'estimated_hrs' => 'required',
                'priority' => 'required',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', Utility::errorFormat($validator->getMessageBag()));
        }

        $post = $request->all();
        $task = ProjectTask::find($task_id);
        $task->update($post);

        return redirect()->back()->with('success', __('Task Updated successfully.'));
    }

    public function destroy($project_id, $task_id)
    {

        ProjectTask::deleteTask([$task_id]);

        return redirect()
            ->route('organization.projects.tasks.index', $project_id)
            ->with('success', __('Task deleted successfully.'));

        echo json_encode(['task_id' => $task_id]);
    }

    public function getStageTasks(Request $request, $stage_id)
    {

        $count = ProjectTask::where('stage_id', $stage_id)->count();
        echo json_encode($count);
    }

    public function changeCom($projectID, $taskId)
    {
        $project = Project::find($projectID);
        $task = ProjectTask::find($taskId);

        if ($task->is_complete == 0) {
            $last_stage = TaskStage::orderBy('order', 'DESC')->where('created_by', \Auth::user()->creatorId())->first();
            $task->is_complete = 1;
            $task->marked_at = date('Y-m-d');
            $task->stage_id = $last_stage->id;
        } else {
            $first_stage = TaskStage::orderBy('order', 'ASC')->where('created_by', \Auth::user()->creatorId())->first();
            $task->is_complete = 0;
            $task->marked_at = NULL;
            $task->stage_id = $first_stage->id;
        }

        $task->save();

        return [
            'com' => $task->is_complete,
            'task' => $task->id,
            'stage' => $task->stage_id,
        ];
    }

    public function changeFav($projectID, $taskId)
    {
        $task = ProjectTask::find($taskId);
        if ($task->is_favourite == 0) {
            $task->is_favourite = 1;
        } else {
            $task->is_favourite = 0;
        }

        $task->save();

        return [
            'fav' => $task->is_favourite,
        ];
    }

    public function changeProg(Request $request, $projectID, $taskId)
    {
        $task = ProjectTask::find($taskId);
        $task->progress = $request->progress;
        $task->save();

        return ['task_id' => $taskId];
    }

    public function checklistStore(Request $request, $projectID, $taskID)
    {

        $request->validate(
            ['name' => 'required']
        );

        $post = [];
        $post['name'] = $request->name;
        $post['task_id'] = $taskID;
        $post['user_type'] = 'User';
        $post['created_by'] = \Auth::user()->id;
        $post['status'] = 0;

        $checkList = TaskChecklist::create($post);
        $user = $checkList->user;
        $checkList->updateUrl = route(
            'organization.checklist.update', [
                $projectID,
                $checkList->id,
            ]
        );
        $checkList->deleteUrl = route(
            'organization.checklist.destroy', [
                $projectID,
                $checkList->id,
            ]
        );

        return $checkList->toJson();
    }

    public function checklistUpdate($projectID, $checklistID)
    {
        $checkList = TaskChecklist::find($checklistID);
        if ($checkList->status == 0) {
            $checkList->status = 1;
        } else {
            $checkList->status = 0;
        }
        $checkList->save();

        return $checkList->toJson();
    }

    public function checklistDestroy($projectID, $checklistID)
    {
        $checkList = TaskChecklist::find($checklistID);
        $checkList->delete();

        return "true";
    }

    public function commentStoreFile(Request $request, $projectID, $taskID)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        $user = Auth::user();
        $createdBy = User::find($user->created_by);

        if (!$createdBy) {
            return response()->json([
                'is_success' => false,
                'message' => 'Creator not found'
            ], 404);
        }

        // Build folder structure
        $companySlug = Str::slug($createdBy->name ?? 'company');
        $dir = "uploads/companies/{$companySlug}/projects/{$projectID}/tasks/{$taskID}/files";

        if (!$request->hasFile('file')) {
            return response()->json([
                'is_success' => false,
                'message' => 'File missing'
            ], 400);
        }

        // Build file names
        $fileObject = $request->file('file');
        $originalName = $fileObject->getClientOriginalName();
        $extension = $fileObject->getClientOriginalExtension();
        $fileNameToStore = pathinfo($originalName, PATHINFO_FILENAME) . '_' . time() . '.' . $extension;

        // Upload using Utility
        $path = Utility::upload_file($request, 'file', $fileNameToStore, $dir, []);

        if ($path['flag'] != 1) {
            return response()->json([
                'is_success' => false,
                'message' => $path['msg']
            ], 400);
        }

        // ⚠ Important: Always store only RELATIVE PATH in DB
        $relativePath = $dir . '/' . $fileNameToStore;

        // Save record
        $taskFile = TaskFile::create([
            'task_id' => $taskID,
            'file' => $relativePath,
            'name' => $originalName,
            'extension' => $extension,
            'created_by' => $user->id,
            'user_type' => 'User',
        ]);

        $deleteUrl = route('organization.comment.destroy.file', [$projectID, $taskID, $taskFile->id]);

        $fileFullURL = Utility::get_file($relativePath);

        return response()->json([
            'is_success' => true,
            'message' => 'File uploaded successfully',
            'data' => [
                'id' => $taskFile->id,
                'name' => $taskFile->name,
                'extension' => $taskFile->extension,
                'url' => $fileFullURL,
                'deleteUrl' => $deleteUrl
            ]
        ]);
    }

    public function commentDestroyFile(Request $request, $projectID, $taskID, $fileID)
    {
        $commentFile = TaskFile::find($fileID);
        $path = storage_path('tasks/' . $commentFile->file);
        if (file_exists($path)) {
            \File::delete($path);
        }
        $commentFile->delete();

        return "true";
    }

    public function commentDestroy(Request $request, $projectID, $taskID, $commentID)
    {

        $comment = TaskComment::find($commentID);
        $comment->delete();

        return "true";
    }

    public function commentStore(Request $request, $projectID, $taskID)
    {
        // Validate
        $request->validate([
            'comment' => 'required|string|max:2000'
        ]);

        // Create comment
        $comment = TaskComment::create([
            'task_id' => $taskID,
            'user_id' => \Auth::id(),
            'comment' => $request->comment,
            'created_by' => \Auth::user()->creatorId(),
            'user_type' => \Auth::user()->type,
        ]);

        // Load related user
        $user = $comment->user;
        $gender = $user->employee->gender ?? null;

        // Build avatar URL (same logic as Blade)
        if ($gender === GENDER_MALE) {
            $avatarUrl = asset('assets/assestsnew/menimg.png');
        } elseif ($gender === GENDER_FEMALE) {
            $avatarUrl = asset('assets/assestsnew/femaile-report.svg');
        } else {
            $profile = \App\Models\Utility::get_file($user->avatar);

            $avatarUrl = $user->avatar
                ? $profile
                : asset('assets/assestsnew/menimg.png');
        }

        // Add custom values to return in AJAX
        $comment->current_time = $comment->created_at->diffForHumans();
        $comment->deleteUrl = route('organization.comment.destroy', [$projectID, $taskID, $comment->id]);
        $comment->avatar_url = $avatarUrl;
        $comment->user_name = $user->name;

        // Notifications
        $setting = Utility::settings(\Auth::user()->creatorId());
        $task = ProjectTask::find($taskID);
        $project = Project::find($projectID);

        $notificationData = [
            'task_name' => $task->name,
            'project_name' => $project->project_name,
            'user_name' => $user->name,
        ];

        if (!empty($setting['taskcomment_notification'])) {
            Utility::send_slack_msg('new_task_comment', $notificationData);
        }

        if (!empty($setting['telegram_taskcomment_notification'])) {
            Utility::send_telegram_msg('new_task_comment', $notificationData);
        }

        // Webhook
        $module = 'New Task Comment';
        $webhook = Utility::webhookSetting($module);

        if ($webhook) {
            $parameter = json_encode($comment);
            $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);

            if ($status) {
                return redirect()->back()->with('success', __('Comment added successfully.'));
            }

            return redirect()->back()->with('error', __('Comment added successfully, Webhook call failed.'));
        }

        return response()->json($comment);
    }

    public function updateTaskPriorityColor(Request $request)
    {
        $task_id = $request->input('task_id');
        $color = $request->input('color');

        $task = ProjectTask::find($task_id);

        if ($task && $color) {
            $task->priority_color = $color;
            $task->save();
        }
        echo json_encode(true);
    }

    public function taskOrderUpdate(Request $request, $project_id)
    {
        $user = \Auth::user();
        $project = Project::find($project_id);
        // Save data as per order

        if (isset($request->sort)) {
            foreach ($request->sort as $index => $taskID) {
                if (!empty($taskID)) {
                    echo $index . "-" . $taskID;
                    $task = ProjectTask::find($taskID);

                    $task->order = $index;
                    $task->save();

                }
            }
        }

        // Update Task Stage
        if ($request->new_stage != $request->old_stage) {

            $new_stage = TaskStage::find($request->new_stage);
            $old_stage = TaskStage::find($request->old_stage);
            $last_stage = TaskStage::where('created_by', \Auth::user()->creatorId())->orderBy('order', 'DESC')->first();
            $last_stage = $last_stage->id;

            $task = ProjectTask::find($request->id);

            $task->stage_id = $request->new_stage;

            if ($request->new_stage == $last_stage) {
                $task->is_complete = 1;
                $task->marked_at = date('Y-m-d');
            } else {
                $task->is_complete = 0;
                $task->marked_at = NULL;
            }
            $task->save();

            //For Notification
            $setting = Utility::settings(\Auth::user()->creatorId());
            $old_stage = TaskStage::find($request->old_stage);
            $new_stage = TaskStage::find($request->new_stage);
            $task = ProjectTask::find($request->id);
            $users = explode(',', $task->assign_to);

            if (isset($setting['task_stage_updated']) && $setting['task_stage_updated'] == 1) {
                foreach ($users as $key => $user) {
                    $user = User::find($user);
                    $projectArr = [
                        'task_user' => $user->name,
                        'task_name' => $task->name,
                        'old_stage_name' => $old_stage->name,
                        'new_stage_name' => $new_stage->name,
                    ];
                    $resp = Utility::sendEmailTemplate('task_stage_updated', [$user->id => $user->email], $projectArr);
                }
            }

            $projectTaskNotificationArr = [
                'task_name' => $task->name,
                'old_stage_name' => $old_stage->name,
                'new_stage_name' => $new_stage->name,
            ];
            //Slack Notification
            if (isset($setting['taskmove_notification']) && $setting['taskmove_notification'] == 1) {
                Utility::send_slack_msg('task_stage_updated', $projectTaskNotificationArr);
            }
            //Telegram Notification
            if (isset($setting['telegram_taskmove_notification']) && $setting['telegram_taskmove_notification'] == 1) {
                Utility::send_telegram_msg('task_stage_updated', $projectTaskNotificationArr);
            }

            //webhook
            $module = 'Task Stage Updated';
            $webhook = Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($task);
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status == true) {
                    return redirect()->back()->with('success', __('Task successfully updated!'));
                } else {
                    return redirect()->back()->with('error', __('Task successfully updated, Webhook call failed.'));
                }
            }


            // Make Entry in activity log
            ActivityLog::create(
                [
                    'user_id' => $user->id,
                    'project_id' => $project_id,
                    'task_id' => $request->id,
                    'log_type' => 'Move Task',
                    'remark' => json_encode(
                        [
                            'title' => $task->name,
                            'old_stage' => $old_stage->name,
                            'new_stage' => $new_stage->name,
                        ]
                    ),
                ]

            );

            return $task->toJson();
        }
    }

    public function taskGet($task_id)
    {
        $task = ProjectTask::find($task_id);
//            dd($task->taskProgress()['color']);

        $html = '';
        $html .= '<div class="card-body"><div class="row align-items-center mb-2">';
        $html .= '<div class="col-6">';
        $html .= '<span class="badge badge-pill badge-xs badge-' . ProjectTask::$priority_color[$task->priority] . '">' . ProjectTask::$priority[$task->priority] . '</span>';
        $html .= '</div>';
        $html .= '<div class="col-6 text-end">';
//            if(str_replace('%', '', $task->taskProgress()['percentage']) > 0)
//            {
//                $html .= '<span class="text-sm">' . $task->taskProgress()['percentage'] . '</span> <div class="progress">
//                                                    <div class="progress-bar bg-{{ $task->taskProgress()['color'] }}" role="progressbar"
//                                                         style="width: {{ $task->taskProgress()['percentage'] }};"></div>
//                                                </div>';
//            }
        if (\Auth::user()->can('view project task') || \Auth::user()->can('edit project task') || \Auth::user()->can('delete project task')) {
            $html .= '<div class="dropdown action-item">
                                                            <a href="#" class="action-item" data-toggle="dropdown"><i class="ti ti-ellipsis-h"></i></a>
                                                            <div class="dropdown-menu dropdown-menu-right">';
            if (\Auth::user()->can('view project task')) {
                $html .= '<a href="#" data-url="' . route(
                        'projects.tasks.show', [
                            $task->project_id,
                            $task->id,
                        ]
                    ) . '" data-ajax-popup="true" class="dropdown-item">' . __('View') . '</a>';
            }
            if (\Auth::user()->can('edit project task')) {
                $html .= '<a href="#" data-url="' . route(
                        "projects.tasks.edit", [
                            $task->project_id,
                            $task->id,
                        ]
                    ) . '" data-ajax-popup="true" data-size="lg" data-title="' . __("Edit ") . $task->name . '" class="dropdown-item">' . __('Edit') . '</a>';
            }
            if (\Auth::user()->can('delete project task')) {
                $html .= '<a href="#" class="dropdown-item del_task" data-url="' . route(
                        'projects.tasks.destroy', [
                            $task->project_id,
                            $task->id,
                        ]
                    ) . '">' . __('Delete') . '</a>';
            }
            $html .= '                                 </div>
                                                        </div>
                                                    </div>';
            $html .= '</div>';
        }
        $html .= '<a class="h6" href="#" data-url="' . route(
                "projects.tasks.show", [
                    $task->project_id,
                    $task->id,
                ]
            ) . '" data-ajax-popup="true">' . $task->name . '</a>';
        $html .= '<div class="row align-items-center">';
        $html .= '<div class="col-12">';
        $html .= '<div class="actions d-inline-block">';
        if (count($task->taskFiles) > 0) {
            $html .= '<div class="action-item mr-2"><i class="ti ti-file text-primary mr-2"></i>' . count($task->taskFiles) . '</div>';
        }
        if (count($task->comments) > 0) {
            $html .= '<div class="action-item mr-2"><i class="ti ti-message text-primary mr-2"></i>' . count($task->comments) . '</div>';
        }
        if ($task->checklist->count() > 0) {
            $html .= '<div class="action-item mr-2"><i class="ti ti-list text-primary mr-2"></i>' . $task->countTaskChecklist() . '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-5">';
        if (!empty($task->end_date) && $task->end_date != '0000-00-00') {
            $clr = (strtotime($task->end_date) < time()) ? 'text-danger' : '';
            $html .= '<small class="' . $clr . '">' . date("d M Y", strtotime($task->end_date)) . '</small>';
        }
        $html .= '</div>';
        $html .= '<div class="col-7 text-end">';

        if ($users = $task->users()) {
            $html .= '<div class="avatar-group">';
            foreach ($users as $key => $user) {
                if ($key < 3) {
                    $html .= ' <a href="#" class="avatar rounded-circle avatar-sm">';
                    $html .= '<img class="hweb" src="' . $user->getImgImageAttribute() . '" title="' . $user->name . '">';
                    $html .= '</a>';
                }
            }

            if (count($users) > 3) {
                $html .= '<a href="#" class="avatar rounded-circle avatar-sm"><img avatar="';
                $html .= count($users) - 3;
                $html .= '"></a>';
            }
            $html .= '</div>';
        }
        $html .= '</div></div></div>';

        print_r($html);
    }

    public function getDefaultTaskInfo(Request $request, $task_id)
    {

        if (\Auth::check()) {
            $response = [];
            $task = ProjectTask::find($task_id);
            if ($task) {
                $response['task_name'] = $task->name;
                $response['task_due_date'] = $task->due_date;
            }

            return json_encode($response);
        } else {
            $response = [];
            $task = ProjectTask::find($task_id);
            if ($task) {
                $response['task_name'] = $task->name;
                $response['task_due_date'] = $task->due_date;
            }

            return json_encode($response);
        }
    }

    // Calendar View
    public function calendarView($task_by, $project_id = NULL)
    {
        $usr = Auth::user();
        $transdate = date('Y-m-d', time());

        if ($usr->type != 'admin') {
            if (\Auth::user()->type == 'client') {
                $user_projects = Project::where('client_id', \Auth::user()->id)->pluck('id', 'id')->toArray();
            } else {
                $user_projects = $usr->projects()->pluck('project_id', 'project_id')->toArray();
            }
            $user_projects = (!empty($project_id) && $project_id > 0) ? [$project_id] : $user_projects;

            if (\Auth::user()->type == 'company') {
                $tasks = ProjectTask::whereIn('project_id', $user_projects);
            } elseif (\Auth::user()->type != 'company') {
                if (\Auth::user()->type == 'client') {

                    $tasks = ProjectTask::whereIn('project_id', $user_projects);
                } else {
                    $tasks = ProjectTask::whereIn('project_id', $user_projects)->whereRaw("find_in_set('" . \Auth::user()->id . "',assign_to)");
                }
            }
            if (\Auth::user()->type == 'client') {
                if ($task_by == 'all') {
                    $tasks->where('created_by', \Auth::user()->creatorId());
                }
            } else {
                if ($task_by == 'my') {
                    $tasks->whereRaw("find_in_set('" . $usr->id . "',assign_to)");
                }
            }
            $tasks = $tasks->get();
            $arrTasks = [];

            foreach ($tasks as $task) {
                $arTasks = [];
                if ((!empty($task->start_date) && $task->start_date != '0000-00-00') || !empty($task->end_date) && $task->end_date != '0000-00-00') {
                    $arTasks['id'] = $task->id;
                    $arTasks['title'] = $task->name;

                    if (!empty($task->start_date) && $task->start_date != '0000-00-00') {
                        $arTasks['start'] = $task->start_date;
                    } elseif (!empty($task->end_date) && $task->end_date != '0000-00-00') {
                        $arTasks['start'] = $task->end_date;
                    }
                    if (!empty($task->end_date) && $task->end_date != '0000-00-00') {
                        $arTasks['end'] = $task->end_date;
                    } elseif (!empty($task->start_date) && $task->start_date != '0000-00-00') {
                        $arTasks['end'] = $task->start_date;
                    }
                    $arTasks['allDay'] = !0;
                    $arTasks['className'] = 'event-' . ProjectTask::$priority_color[$task->priority];
                    $arTasks['description'] = $task->description;
                    $arTasks['url'] = route('task.calendar.show', $task->id);
                    $arTasks['resize_url'] = route('task.calendar.drag', $task->id);
                    $arrTasks[] = $arTasks;


                }
            }

            return view('company.tasks.calendar', compact('arrTasks', 'project_id', 'task_by', 'transdate'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // Calendar Show
    public function calendarShow($id)
    {
        $task = ProjectTask::find($id);

        return view('company.tasks.calendar_show', compact('task'));
    }

    // Calendar Drag
    public function calendarDrag(Request $request, $id)
    {
        $task = ProjectTask::find($id);
        $task->start_date = $request->start;
        $task->end_date = $request->end;
        $task->save();
    }

    //for Google Calendar
    public function get_task_data(Request $request)
    {
        if ($request->get('calender_type') == 'goggle_calender') {
            $type = 'task';
            $arrayJson = Utility::getCalendarData($type);
        } else {
            if (Auth::user()->type == 'client') {
                $user_projects = Project::where('client_id', \Auth::user()->id)->pluck('id', 'id')->toArray();
                $data = ProjectTask::whereIn('project_id', $user_projects)->get();
            } else {
                if (Auth::user()->type == 'company') {
                    $data = ProjectTask::where('created_by', \Auth::user()->creatorId())->get();
                } else {
                    $usr = Auth::user();
                    $user_projects = $usr->projects()->pluck('project_id', 'project_id')->toArray();
                    $data = ProjectTask::whereIn('project_id', $user_projects)
                        ->where('created_by', \Auth::user()->creatorId())
                        ->whereRaw("find_in_set('" . \Auth::user()->id . "',assign_to)")->get();
                }

            }

//            $data = ProjectTask::where('created_by', \Auth::user()->creatorId())->get();
            $arrayJson = [];
            foreach ($data as $val) {
                $end_date = date_create($val->end_date);
                date_add($end_date, date_interval_create_from_date_string("1 days"));
                $arrayJson[] = [
                    "id" => $val->id,
                    "title" => $val->name,
                    "start" => $val->start_date,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
                    "className" => 'event-primary',
                    "textColor" => '#51459d',
                    "allDay" => true,
                    'url' => route('task.calendar.show', $val->id),
                    'resize_url' => route('task.calendar.drag', $val->id),
                ];
            }
        }

        return $arrayJson;
    }
}
