<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', [
            'users' => User::notAdmin()->with(['role', 'skills', 'projects', 'tasks'])->paginate(6),
            'is_mgr' => User::IS_MGR,
            'is_sales' => User::IS_SALES
        ]);
    }

    public function projects(User $user)
    {
        return view('users.project', [
            'user' => $user,
            'projects' => $user->projects()->with(['state', 'level'])->withCount(['tasks' => function($query) use ($user)
            {
                return $query->where('assigned_to', $user->id);
            }])->paginate(4)
        ]);
    }
}
