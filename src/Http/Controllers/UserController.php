<?php

namespace Local\CMS\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    public function index()
    {
        $hiddenFields = [
            'Billing Address' => 7,
            'Shipping Address' => 8,
        ];
        $users = User::all();
        return view('modules::users.index', compact('users','hiddenFields'));
    }

    public function edit(User $user)
    {
        $countries = Country::orderBy('display_name', 'asc')->get()->map(function ($country) {
            return ['id' => $country->phone_code, 'name' => "+" . $country->phone_code . " (" . $country->display_name . ")"];
        })->pluck('name', 'id');

        $nationalities = Country::orderBy('display_name', 'asc')->get()->map(function ($country) {
            return ['id' => $country->display_name, 'name' =>  $country->display_name];
        })->pluck('name', 'id');

        return view('modules::users.edit', compact('user', 'countries', 'nationalities'));
    }

    public function update(Request $request, User $user)
    {
        // dd($request->all());
        $this->validate($request, [
            'name' => "required|string",
            'email' => "required|email|unique:users,email," . $user->id,
            'nationality' => "required",
            'phone_code' => "required",
            'phone' => "required",
            'dob' => "required|before:today",
            'gender' => "required",
        ]);

        $dateob = Carbon::createFromFormat('d/m/Y', $request->get('dob'))->format('Y-m-d');

        $user->update([
            "name" => $request->get('name'),
            "email" => $request->get('email'),
            "nationality" => $request->get('nationality'),
            "phone_code" => $request->get('phone_code'),
            "phone" => $request->get('phone'),
            "dob" => $dateob,
            "gender" => $request->get('gender'),
            "active" => $request->get('active'),
        ]);

        return redirect()->route('admin.users.index')->withSuccess('User updated.');
    }

    public function create()
    {
        $countries = Country::orderBy('display_name', 'asc')->get()->map(function ($country) {
            return ['id' => $country->phone_code, 'name' => "+" . $country->phone_code . " (" . $country->display_name . ")"];
        })->pluck('name', 'id');

        $nationalities = Country::orderBy('display_name', 'asc')->get()->map(function ($country) {
            return ['id' => $country->display_name, 'name' =>  $country->display_name];
        })->pluck('name', 'id');

        return view('modules::users.create', compact('countries','nationalities'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => "required|string",
            'email' => "required|email|unique:users,email",
            'nationality' => "required",
            'phone_code' => "required",
            'phone' => "required",
            'dob' => "required|before:today",
            'gender' => "required",
            'password' => "required|min:8",
            'confirm_password' => "required_with:password|same:password",
        ]);

        $dateOB = Carbon::createFromFormat('d/m/Y', $request->get('dob'));
        User::create([
            "name" => $request->get('name'),
            "email" => $request->get('email'),
            "nationality" => $request->get('nationality'),
            "phone_code" => $request->get('phone_code'),
            "phone" => $request->get('phone'),
            "dob" => $dateOB,
            "gender" => $request->get('gender'),
            "active" => $request->get('active'),
            "password" => bcrypt($request->get('password')),
        ]);

        return redirect()->route('admin.users.index')->withSuccess('User created.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->withSuccess('User deleted.');
    }

    public function resetPassword(User $user)
    {
        $random_password = (Str::random(8));
        $user->update(['password' => bcrypt($random_password)]);

        $details = [
            'name' => $user->name,
            'new_password' => $random_password
        ];

        Mail::to($user->email)->send(new ResetPasswordMail($details));

        return redirect()->back()->withSuccess('User password has been reset.');
    }
}
