<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\UserStore;
use App\Models\Bank;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $store=UserStore::where('user_id',auth()->user()->id)->first();
        $bank=Bank::where('store_id',$store->store_id)->get();
        return response()->json(['status'=>'success','data'=>$bank]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $store=UserStore::where('user_id',auth()->user()->id)->first();
        $data=[
            'atas_nama'=>$request->atas_nama,
            'bank'=>$request->bank,
            'rekening'=>$request->rekening,
            'store_id'=>$store->store_id
        ];
        $validationData=Validator::make($data,[
            'atas_nama'=>'required',
            'bank'=>'required',
            'rekening'=>'required'
        ]);

        if($validationData->fails()){
            return response()->json(['status'=>'error','errors'=>$validationData->errors()],422);
        }

        Bank::create($data);
        return response()->json($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */

     public function destory($id)
     {
        //  Bank::where('id',$id)->delete();
 
         return response()->json(['status'=>'success','message'=>'Bank berhasil dihapus']);
     }
}
