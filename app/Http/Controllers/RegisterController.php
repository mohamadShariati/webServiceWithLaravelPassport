<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use ApiResponser;

    public function register(Request $request)
    {
       

        $validator=Validator::make($request->all(),[
            'name'=>'required|string',
            'email'=>'required|unique:users,email',
            'password'=>'required|string',
            'c_password'=>'required|same:password'
        ]);
        
        

        if($validator->fails())
        {
            return $this->errorResponse($validator->messages(),422);
        }
        
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);

        $token=$user->createToken('myApp')->accessToken;

        return $this->successResponse([
            'user'=> $user,
            'token'=>$token
        ],200);
    }
}
