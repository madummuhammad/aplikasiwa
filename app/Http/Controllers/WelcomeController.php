<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Store;
use App\Models\UserStore;
use App\Models\Warehouse;

class WelcomeController extends Controller
{

    public function index()
    {
        $auth=New AuthController;

        if ($auth->existstore() AND $auth->existwarehouse()) {
            return redirect()->route('dashboard');
        } else {
            $store=Store::where('user_id',auth()->user()->id)->first();
            return Inertia::render('Auth/Welcome/Welcome',[
                'store'=>$store
            ]);
        }
    }

    public function create()
    {
        return Inertia::render('Auth/Welcome/Store');
    }

    public function save_store(Request $request)
    {
        $validatedData=$request->validate([
            'username'=>'required',
            'email'=>'required|email',
            'name'=>'required',
            'phone'=>'required',
            'web'=>'required',
            'foto'=>'required:mimes:jpg,jpeg,png|file',
            'address'=>'required',
            'description'=>'required'

        ]);

        unset($validatedData['foto']);
        $validatedData['logo']=$this->foto($request);
        $validatedData['plan']='personal';
        $validatedData['user_id']=auth()->user()->id;
        $validatedData['expired_at']=$this->dateAdd(date('Y-m-d'));
        $validatedData['sequence']=$this->sequence();

        $store=Store::create($validatedData);

        UserStore::create([
            'store_id'=>$store->id,
            'user_id'=>auth()->user()->id,
            'is_owner'=>true,
            'role'=>'Owner'
        ]);
        
        return redirect()->route('welcome');
        

    }


    public function save_warehouse(Request $request)
    {
        $validatedData=$request->validate([
            'name'=>'required',
            'pic'=>'required',
            'address'=>'required',
            'province'=>'required',
            'city'=>'required',
            'subdistrict'=>'required',
            'postcode'=>'required|numeric',
            'phone'=>'required|numeric',
            'second_phone'=>'required|numeric'

        ]);

        $validatedData['store_id']=$this->getStore()->store_id;

        Warehouse::create($validatedData);

        
        return redirect()->route('dashboard');
        

    }

    public function getStore(){
        return $store=UserStore::where('user_id',auth()->user()->id)->with('store')->first();
    }

    public function dateAdd($tanggal_awal)
    {
        $tanggal_objek = date_create($tanggal_awal);
        $interval = date_interval_create_from_date_string('1 year');
        date_add($tanggal_objek, $interval);
        return $tanggal_hasil = date_format($tanggal_objek, 'Y-m-d');
    }

    public function sequence()
    {
        return $store=Store::get()->count()+1;
    }

    public function foto($request){
        return url('/').'/'.str_replace('public/','storage/',$request->file('foto')->store('public/assets/foto'));
    }
    
    public function warehouse()
    {
        return Inertia::render('Auth/Welcome/Warehouse');
    }

    private function existstore()
    {
        $existstore=UserStore::where('user_id',auth()->user()->id)->exist();
        return $existstore;
    }
}
