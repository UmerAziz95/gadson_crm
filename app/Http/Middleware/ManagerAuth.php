<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ManagerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
    	if(!$request->session()->has('user')){
    		 
    		$request->session()->flash('error', 'Access Denied');
    		return redirect('/manager');
    		 
    	}
    	else if(session('user')->type != 'Manager'){
    		 
            $request->session()->flash('error', 'Access Denied');
    		return redirect('/manager');
    	
    	}
        else if(Auth::user()->status == 0 || Auth::user()->status == '0'){
            $request->session()->flash('error', 'Your account is inactive, contact admin for more details');
    		return redirect('/manager');
        }
        return $next($request);
    }
}
