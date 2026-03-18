<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search_name', 'search_email', 'search_phone', 'role', 'sort_field', 'sort_dir']);

        $query = User::query()
            ->when($filters['search_name'] ?? null, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($filters['search_email'] ?? null, function ($query, $search) {
                $query->where('email', 'like', '%' . $search . '%');
            })
            ->when($filters['search_phone'] ?? null, function ($query, $search) {
                $query->where('phone_number', 'like', '%' . $search . '%');
            })
            ->when($filters['role'] ?? null, function ($query, $role) {
                if ($role !== 'all') {
                    $query->where('role', $role);
                }
            });

        $sortField = $filters['sort_field'] ?? 'id';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        
        $allowedSorts = ['id', 'name', 'email', 'phone_number', 'role'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $users = $query->paginate(15)->withQueryString();
        
        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $filters
        ]);
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:255',
            'role' => 'required|string|in:user,admin',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
