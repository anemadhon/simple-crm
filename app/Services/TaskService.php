<?php

namespace App\Services;

use App\Models\ProjectUser;
use App\Models\Task;
use App\Models\User;

class TaskService
{
    public function lists(User $user)
    {
        if ($user->can('manage-products') || $user->can('sale-products') || $user->can('develop-products')) {
            $ownTasks = $user->tasks()->with(['project:id,name', 'project.users', 'level:id,name', 'state:id,name', 'user:id,name'])
                            ->withCount('subs')->orderBy('assigned_to')->paginate(4);

            if ($user->can('manage-products')) {
                $pm = ProjectUser::where('status', ProjectUser::ON_START)->where('pm_id', $user->id)
                        ->distinct()->get(['project_id']);
                        
                if ($user->id == $user->projects->first()?->pivot->pm_id) {
                    $teamTasks = Task::with(['project:id,name', 'project.users', 'level:id,name', 'state:id,name', 'user:id,name'])
                                    ->withCount('subs')
                                    ->whereIn('project_id', $pm->pluck('project_id'))
                                    ->orderBy('assigned_to')->get();
    
                    return [
                        'own_tasks' => $ownTasks,
                        'team_tasks' => $teamTasks
                    ];
                }
            } 
            
            return [
                'own_tasks' => $ownTasks,
                'team_tasks' => null
            ];
        }

        $ownTasks = Task::with(['project:id,name', 'project.users', 'level:id,name', 'state:id,name', 'user:id,name'])->withCount('subs')
                        ->orderBy('assigned_to')->paginate(4);
        return [
            'own_tasks' => $ownTasks,
            'team_tasks' => null
        ];
    }
}
