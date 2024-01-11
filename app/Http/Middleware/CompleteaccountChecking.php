<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserStore;
use App\Models\Warehouse;
class CompleteaccountChecking
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->existstore() AND $this->existwarehouse()) {
            // return redirect()->route('dashboard');
        } else {
            return redirect()->route('welcome');
        }
        return $next($request);
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
}
