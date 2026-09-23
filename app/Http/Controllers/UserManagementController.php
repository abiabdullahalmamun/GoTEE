<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 500);
        $search = trim($request->get('search'));

        $user = Auth::user();
        // dd($user) ; 
        $query = User::query();

        $query = User::query()->where('name','!=','devcom')->whereHas('role', function ($q) use ($user) {
            $q->where('is_member','!=', 1);

            // Only add is_access = 0 for restricted users
            if (!($user && $user->role && $user->role->is_access == 1)) {
                $q->where('is_access', 0);
            }
        });

        // $centerId = auth()->user()->centerId;
        // if($centerId){
        //     $users = $query->where('centerId',$centerId)->paginate($perPage);
        // }
        // else{
            $users = $query->paginate($perPage);
        // }
        
        // dd($users) ; 
        return view('pages.users.index', compact('users'));
    }


    public function create(){
        $user = Auth::user();
        $centerId = auth()->user()->centerId;

        if ($user && $user->role && $user->role->is_access == 1) {
            $roles = Role::latest()->get();
        }else{
            $roles = Role::where('is_access',0)->latest()->get();
        }

        // dd($user) ; 
        // dd($centerId) ; 
        $role = Auth::user()->role_id;
        // dd($role) ; 

        if($role==1 || $role==5 ||  $role== 13){
            $center = Center::where('status',1)->latest()->get();
              return view('pages.users.create', compact('roles','center'));
        }
        else{
            $center = Center::where('status',1)->where('id', $centerId)->latest()->get();
              return view('pages.users.create', compact('roles','center'));
        }
       
    }

    public function store(Request $request)
    {
         // dd($request->all());

       $validated = $request->validate([
            'name' => 'required|string|max:255',
            'fullname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|min:6',
            'centerId' => [
                'nullable',  // allows centerId to be null
                function ($attribute, $value, $fail) {
                    if (is_null($value)) {
                        return; // null is OK
                    }
                    if (!Center::where('id', $value)->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                },
            ],
        ]);

  // dd($validated);
        $authId = Auth::id();
        User::create([
            'name' => $validated['name'],
            'FullName' => $validated['fullname'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'password' => bcrypt($validated['password']),
            'is_active' => 1,
            'is_approve' => 1,
            'created_by' => $authId,
            'created_at' => now(),
             'centerId' => $validated['centerId'],
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    public function show($id){
         // $centerId = auth()->user()->centerId;
        $user = User::where('id',$id)->first();
        if(!$user){
            return redirect()->route('users.index')->with('error', 'User not Found!');
        }
        return view('pages.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // dd($user) ; 
        $centerId = auth()->user()->centerId;
        $roles = Role::all();
        // if($centerId){
        //     $center = Center::where('status',1)->where('id', $centerId)->latest()->get();
        //     return view('pages.users.editcn', compact('user', 'roles', 'center'));
        // }
        // else{
        $center = Center::where('status',1)->latest()->get();  
        return view('pages.users.edit', compact('user', 'roles', 'center'));  
        // }
       
        
    }

    public function update(Request $request, User $user)
    {
            // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'fullname' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|boolean',
            // 'centerId' => 'required|exists:tbl_shopId,id',
            'centerId' => [
                'nullable',  // allows centerId to be null
                function ($attribute, $value, $fail) {
                    if (is_null($value)) {
                        return; // null is OK
                    }
                    if (!Center::where('id', $value)->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                },
            ],
        ]);

        $user->update([
            'name' => $request->name,
            'FullName' => $request->fullname,
            'role_id' => $request->role_id,
            'is_active' =>  $request->is_active ,
            'centerId' => $request->centerId, 
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function updateAssign(Request $request,User $user)
    {
     try {   
          $user->update([
            'login_attempts' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
         ]);
         return redirect()->route('users.index')->with('success', 'User Account Reset successfull');

      } catch (\Exception $e) {
            info('Shop deleted failed!', [$e]);
             dd($e->getMessage());
            return redirect()->route('counters.index')->with('error', 'operation failed!.');
        }
      
//
        // return redirect()->route('operators.index')->with('success', 'User updated successfully.');
    }
    public function destroy($id){
        $user = User::where('id',$id)->first();
        $authId = Auth::id();
        if(!$user){
            return redirect()->route('users.index')->with('error', 'User not Found!');
        }
        elseif($user->id == $authId){
            return redirect()->route('users.index')->with('error', 'Could not delete yourself!');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

}
