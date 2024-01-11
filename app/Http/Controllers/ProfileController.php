<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\UserStore;
use App\Models\Store;
use App\Models\Bank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller
{
    public function index()
    {
        $auth=auth()->user();
        $profile=User::where('id',$auth->id)->with('user_store.store')->first();
        $store=UserStore::where('user_id',$auth->id)->first();
        $bank=Bank::where('store_id',$store->store_id)->get();
        $message=session('message');
        $params=request();
        return Inertia::render('Profile/Index',[
            'profile'=>$profile,
            'message'=>$message,
            'bank'=>$bank,
            'params'=>$params
        ]);
    }

    public function update(Request $request)
    {
        $store=Store::where('id',$request->user_store['store_id'])->first();
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'wa' => 'required|numeric'
        ]);

        if($store->username!==$request->user_store['store']['username']){
            $exists=Store::where('username',$request->user_store['store']['username'])->exists();
            if($exists){

            } else {
                Store::where('id',$request->user_store['store_id'])->update([
                    'username'=>$request->user_store['store']['username']
                ]);
            }
        }

        if($request->image){
            $validatedData['image']=$request->image;
        }

        $validatedData['image']=$request['image'];

        User::where('id',auth()->user()->id)->update($validatedData);

        return back()->with('message',['status'=>'success','message'=> 'Data berhasil di ubah']);
    }

    
    public function change_password(Request $request)
    {
        $validatedData = $request->validate([
            'old_password' => 'required|min:6',
            'new_password' => 'required|min:6',
        ]);
    
        $user = Auth::user();
    
        if (!Hash::check($validatedData['old_password'], $user->password)) {
            return back()->with('message', ['status' => 'error', 'message' => 'Kata sandi lama tidak sesuai']);
        }
    
        // Mengganti kata sandi dengan yang baru
        $user->update([
            'password' => Hash::make($validatedData['new_password']),
        ]);
    
        return back()->with('message', ['status' => 'success', 'message' => 'Password berhasil diubah']);
    }
}
