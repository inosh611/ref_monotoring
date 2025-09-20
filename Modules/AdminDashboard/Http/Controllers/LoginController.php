<?php

namespace Modules\AdminDashboard\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function login(){
        return Inertia::render('Modules/AdminDashBoard/Dashboard');
    }

    public function index()
    {
        
    }

    public function create()
    {
       
    }

    public function store(Request $request)
    {
        //
    }

   
    public function show($id)
    {
        return view('admindashboard::show');
    }

   
    public function edit($id)
    {
        return view('admindashboard::edit');
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
