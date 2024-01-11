<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Store;
use App\Models\Warehouse;
use App\Models\UserStore;

class AuthController extends Controller
{
    public function login()
    {
        // Auth::logout();
        return Inertia::render('Auth/Login');
    }

    public function create_token()
    {
        
        // if(Auth::user()){
        //     $user = Auth::user();
        //     $token = $user->createToken('user-token')->plainTextToken;
        //     return response()->json(['token' => $token]);
        // }

        // Membuat token untuk pengguna
    
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if ($this->existstore() AND $this->existwarehouse()) {
                // return redirect()->route('dashboard');
                return;
            } else {
                return redirect()->route('welcome');
            }
        }
    
        return Inertia::render('Auth/Login', [
            'loginError' => 'Login gagal! Periksa kembali credentials anda!'
        ]);
    }
    
    public function existstore()
    {
        $existstore = UserStore::where('user_id', auth()->user()->id)->first();
        return $existstore;
    }

    public function existwarehouse(){
        $store=UserStore::where('user_id',auth()->user()->id)->first();
        return Warehouse::where('store_id',$store->store_id)->first();
    }
    

    public function register()
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'wa' => 'required|numeric',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'payment_method'=>'required'
        ]);
    
        $validatedData['password'] = Hash::make($validatedData['password']);
    
        unset($validatedData['password_confirmation']);

        $validatedData['permission']=$request->permission;
        $validatedData['image']=null;
        User::create($validatedData);
        

        $request->session()->regenerate();

        return redirect()->intended('register/success')->with('status','RegisterSuccess');
    }

    public function success()
    {
        if(session('status')=='RegisterSuccess'){
            return Inertia::render('Auth/Success');
        } else {
            return redirect()->route('login');
        }
    }

    public function me()
    {
        $id=auth()->user()->_id;
        $me=User::where('_id',$id)->with('user_store')->first();
        return response()->json(['status'=>'success','data'=>$me]);
    }

    public function destroy()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
