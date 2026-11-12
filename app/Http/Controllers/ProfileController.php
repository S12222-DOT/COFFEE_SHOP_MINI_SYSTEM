<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * ONE controller for both account pages. The logged-in user's role decides what happens:
 *
 *   role == 1  (admin)     -> view 'admin.profile.edit', shop stats, can NOT delete the account
 *   otherwise  (customer)  -> view 'profile.edit',       own stats,  can delete if no orders
 *
 *   GET    /profile    edit            show the page
 *   PATCH  /profile    update          save name + email
 *   PUT    /password   updatePassword  change password
 *   DELETE /profile    destroy         delete the account (customers only)
 *
 * Everything works on $request->user(), so nobody can touch another account,
 * and only name/email are read from the form, so nobody can change their own role.
 */
class ProfileController extends Controller
{
    /**
     * Show the account page for the current role.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        if ($this->isAdmin($user)) {
            return view('admin.profile.edit', [
                'user'  => $user,
                'stats' => [
                    'orders'     => Order::count(),
                    'pending'    => Order::whereRaw('LOWER(status) = ?', ['pending'])->count(),
                    'products'   => Product::count(),
                    'categories' => Category::count(),
                    'customers'  => User::where('role', '!=', 1)->count(),
                ],
            ]);
        }

        return view('profile.user-profile', [   // resources/views/profile/user-profile.blade.php
            'user' => $user,
        ]);
    }

    /**
     * Save the name and email (same rules for admin and customer).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Change the password (errors go in the "updatePassword" bag).
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Delete the account. Customers only.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // the shop must always keep an admin
        if ($this->isAdmin($user)) {
            return back()->withErrors(['password' => 'Admin accounts cannot be deleted.'], 'userDeletion');
        }

        // keep the order history
        if (Order::where('user_id', $user->id)->exists()) {
            return back()->withErrors(
                ['password' => 'Your account has orders and cannot be deleted. Please contact the shop.'],
                'userDeletion'
            );
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('home')->with('success', 'Your account was deleted.');
    }

    /**
     * Admin = role 1 (same rule as your AdminMiddleware).
     */
    private function isAdmin(User $user): bool
    {
        return (int) $user->role === 1;
    }
}