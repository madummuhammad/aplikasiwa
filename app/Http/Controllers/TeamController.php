<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\UserStore;
use Illuminate\Support\Facades\Hash;
class TeamController extends Controller
{
    public function index()
    {
        $permission=auth()->user();
        if($permission->permission['team']['read']==true){
        $user=UserStore::where('user_id',auth()->user()->id)->first();
        $team=UserStore::where('store_id',$user->store_id)->with('user')->where('is_owner',false)->get();
        return Inertia::render('Team/Index',[
            'team'=>$team,
            'user'=>$permission
        ]);
    }
    }

    public function create()
    {
        return Inertia::render('Team/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'wa' => 'required|numeric',
            'email' => 'required|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);
        
        $role=$validatedData['role'];

        unset($validatedData['role']);
        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['permission']=$request->permission;
        $validatedData['image']=null;
        $user=User::create($validatedData);

        $store=UserStore::where('user_id',auth()->user()->id)->first();

        UserStore::create([
            'store_id'=>$store->store_id,
            'user_id'=>$user->id,
            'is_owner'=>false,
            'role'=>$role
        ]);
        return redirect()->route('team');
    }

    public function edit($id)
    {
        if($permission->permission['team']['update']==true){
        $user=User::where('id',$id)->with('user_store')->first();
        return Inertia::render('Team/Edit',[
            'user'=>$user
        ]);
    }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'wa' => 'required|numeric',
            'email' => 'required|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'user_store.role' => 'required',
        ]);
    
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $validatedData['permission']=$request->permission;
        $role = $validatedData['user_store']['role'];
        unset($validatedData['user_store']);
        $user = User::where('id',$id)->first();
        $user->update($validatedData);
    
        $store = UserStore::where('user_id', auth()->user()->id)->first();
    
        UserStore::where('user_id',$id)->update(['role'=>$role]);
    
        return redirect()->route('team');
    }

    public function destroy(Request $request)
    {
        foreach ($request->teamData as $key => $value) {
            if($value['check']==true){
                User::where('id',$value['user']['id'])->delete();
                UserStore::where('user_id',$value['user']['id'])->delete();
            }
        }

        return back();
    }
    
}
