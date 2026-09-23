<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AgentProfileController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $agents = User::where('role', 'agent')
            ->when($search, fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('department', 'like', "%{$search}%")->orWhere('job_title', 'like', "%{$search}%")))
            ->latest()->paginate(15)->withQueryString();

        return view('agent-profiles.index', compact('agents', 'search'));
    }
}
