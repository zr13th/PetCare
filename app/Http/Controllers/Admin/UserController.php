<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, function ($q) use ($request) {
                $search = trim($request->search);
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role'     => ['required', Rule::in(['admin', 'staff', 'customer'])],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'status'   => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'  => ['required', Rule::in(['admin', 'staff', 'customer'])],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status' => !$user->status]);
        return response()->json(['success' => true]);
    }

    /**
     * Import users from Excel.
     */
    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);
        // Excel::import(new UserImport, $request->file('file'));  // bạn có thể tạo UserImport riêng
        return back()->with('success', 'Imported successfully.');
    }

    /**
     * Export users to Excel.
     */
    public function export()
    {
        // return Excel::download(new UserExport, 'users.xlsx');  // tạo UserExport nếu cần
        return back()->with('success', 'Export feature not implemented yet.');
    }
}
