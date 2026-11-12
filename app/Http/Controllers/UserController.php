<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    // --- Public Registration/Login Views & Actions ---

    public function showregister()
    {
        return view('auth.register');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function addUser(Request $req)
    {
        $data = $req->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $data['password'] = Hash::make($data['password']);

        // role is NOT taken from the public form, so everyone who registers is a customer (role 0)
        User::create($data);

        return redirect('/auth/showLogin')->with('success', 'Registration successful. Please log in.');
    }

    public function login(Request $req)
    {
        $credentials = $req->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $req->boolean('remember'))) {
            $req->session()->regenerate();   // protects against session fixation

            // only role 1 is admin; everyone else is a customer
            return Auth::user()->role == 1
                ? redirect('/admin')
                : redirect('/user-dashboard/user');
        }

        return redirect('/auth/showLogin')
            ->withInput($req->only('email'))
            ->with('error', 'Invalid credentials.');
    }

    public function logout(Request $req)
    {
        Auth::logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();

        return redirect('/auth/showLogin')->with('success', 'You have been logged out.');
    }

    // --- Admin: customer management ---

    // List customers (search with ?q=)
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('admin.customers.index', compact('users'));
    }

    // Add and edit are pop-up forms on the index page, so these just go back to it
    public function create()
    {
        return redirect()->route('customers.index');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'nullable|in:0,1',
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['role']     = $data['role'] ?? 0;

        User::create($data);

        return redirect()->route('customers.index')->with('success', 'New customer created successfully.');
    }

    public function edit(int $id)
    {
        return redirect()->route('customers.index');
    }

    public function update(Request $req, int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->route('customers.index')->with('error', 'User not found.');
        }

        $data = $req->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users,email,' . $id,
            'role'     => 'nullable|in:0,1',
            'password' => 'nullable|min:6',
        ]);

        // optional password change: empty means keep the current one
        if ($req->filled('password')) {
            $data['password'] = Hash::make($req->password);
        } else {
            unset($data['password']);
        }

        // an admin can't change their own role (they could lock themselves out)
        if ($user->id === auth()->id()) {
            unset($data['role']);
        }

        $user->update($data);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(int $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'Customer not found.');
        }

        // an admin can't delete their own account from here
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // keep the order history: don't delete someone who has orders
        if (Order::where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', "{$user->name} has orders and cannot be deleted.");
        }

        $user->delete();

        return redirect()->back()->with('success', 'Customer deleted successfully.');
    }
}