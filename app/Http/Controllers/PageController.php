<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function registerpet()
    {
        return view('registerpet');
    }

        public function registered()
    {
        return view('registered');
    }

    public function inventory()
    {
        return view('inventory');
    }

    public function pos()
    {
        return view('pos');
    }

    public function schedule()
    {
        return view('schedule');
    }

    public function transaction()
    {
        return view('transaction');
    }

        public function settings()
    {
        return view('settings');
    }

}
