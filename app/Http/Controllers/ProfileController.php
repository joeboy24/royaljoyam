<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\CompanyBranch;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'load_auth']);
    }

    public function edit()
    {
        $user = auth()->user();
        $branch = CompanyBranch::find($user->company_branch_id);

        return view('pages.dash.user_profile', compact('user', 'branch'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('user_profile')
            ->with('success', 'Profile updated successfully.');
    }
}
