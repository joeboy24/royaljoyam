<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Code80Controller extends Controller
{
    //
    public function code80(){

        // $user = new User;
        // $user->name = 'code80';
        // $user->email = 'code80@pivoapps';
        // $user->password = Hash::make('code.8080');
        // $user->save();

        $backup = User::firstOrCreate(
            ['name' => 'Jay4',
            'email' => 'code80@pivoapps4.net', 
            'password' => Hash::make('code.80804')]
        );

        $backup = User::firstOrCreate(
            ['name' => 'Admin',
            'email' => 'admin@pivoapps.net', 
            'password' => Hash::make('admin1234')]
        );

        return redirect('/dashboard');
    }
}
