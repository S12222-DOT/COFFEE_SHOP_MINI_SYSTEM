<?php

namespace App\Http\Controllers;

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
        $data=$req->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $data['password'] = Hash::make($data['password']);
        $insert=User::create($data);
        
        // Public registration flow redirects to login
        if($insert){
            return redirect('/auth/showLogin')->with('success', 'Registration successful. Please log in.');
        }else{
            return redirect('/auth/register')->with('error', 'Registration failed. Please try again.');
        }
    }

    public function login(Request $req)
    {
        $email=$req->email;
        $password=$req->password;
        if(Auth::attempt(['email'=>$email,'password'=>$password])){
            if(Auth::user()->role==0){
                return redirect('/user-dashboard/user');
            }else{
                return redirect('/admin');
            }
        }else{
            return redirect('/auth/showLogin')->with('error', 'Invalid credentials.');
        }
    }

    public function index()
    {
        $users = User::paginate(10); 
        return view('admin.customers.index', compact('users'));
    }

    public function create()
    {
        return view('admin.customers.index'); 
    }

    public function store(Request $req)
    {
        $data=$req->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'nullable|in:0,1', 
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['role'] = $data['role'] ?? 0;
        $insert=User::create($data);

        if($insert){
            return redirect()->route('customers.index')->with('success', 'New customer created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create new customer.');
        }
    }

    public function edit(int $id)
    {
        $user=User::find($id);
        return view('admin.customers.index',compact('user')); 
    }

    public function update(Request $req, int $id)
    {
        $user=User::find($id);
        if (!$user) {
            return redirect()->route('customers.index')->with('error', 'User not found.');
        }
        $data=$req->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'nullable|in:0,1', 
        ]);

        // Handle optional password update
        if ($req->filled('password')) {
             $req->validate(['password' => 'min:6']);
             $data['password'] = Hash::make($req->password);
        }
        
        $user->update($data);
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.'); 
    }

    public function destroy(int $id)
    {
        $user=User::find($id);
        if ($user) {
            $user->delete();
            return redirect()->back()->with('success', 'Customer deleted successfully.');
        }
        return redirect()->back()->with('error', 'Customer not found.');
    }
}
