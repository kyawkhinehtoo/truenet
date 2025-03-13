<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ReceiptRecord;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $users = User::join('roles', 'users.role', 'roles.id')->when($request->user, function ($query, $tsp) {
            $query->where('users.name', 'LIKE', '%' . $tsp . '%')
                ->orWhere('roles.name', 'LIKE', '%' . $tsp . '%')
                ->orWhere('users.phone', 'LIKE', '%' . $tsp . '%');
        })
            ->select('users.*')
            ->orderby('users.role', 'asc')
            ->paginate(10);
        $roles = Role::get();
        return Inertia::render('Setup/User', ['users' => $users, 'roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required'],
            'role' => ['required'],
            'password' => ['required', 'string'],
        ])->validate();

        User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'role' => $request['role'],
            'disabled' => $request['disabled'],
            'password' => Hash::make($request['password']),
        ]);

        return redirect()->route('user.index')->with('message', 'User Created Successfully.');
        // return redirect()->route('posts.index') 
        // ->with('message', 'Post Created Successfully.');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255',  Rule::unique('users')->ignore($request->id)],
            'phone' => ['required'],
            'role' => ['required'],
        ])->validate();

        if ($request->has('id')) {

            $user = User::find($request->input('id'));
            $user->name = $request['name'];
            $user->email = $request['email'];
            $user->phone = $request['phone'];
            $user->role = $request['role'];
            $user->disabled = $request['disabled'];
            if (!empty($request['password'])) {
                $user->password = Hash::make($request['password']);
            }
            $user->update();

            return redirect()->back()
                ->with('message', 'User Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        if ($request->has('id')) {
            $bill = ReceiptRecord::where('collected_person', $request->id)->first();
            if ($bill)
                return redirect()->back()->with('message', 'User cannot delete due to foreign key constraint in Receipt Database.');
            $customer = Customer::where('sale_person_id', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'User cannot delete due to foreign key constraint in Customer Database.');
            User::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'User deleted successfully.');
        }
    }
}
