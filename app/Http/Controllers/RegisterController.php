<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function login(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'email'=>'required',
            'password'=>'required'
        ]);

        if($validator->fails())
        {
            return $this->errorResponse($validator->messages(),403);
        }

        $user=User::where('email',$request->email)->first();

        if (!$user)
        {
            return $this->errorResponse('user not found',404);
        }

        if (!Hash::check($request->password, $user->password))
        {
            return $this->errorResponse('password is incorect',400);
        }

        $token=$user->createToken('myApp')->accessToken;

        return $this->successResponse([
            'user'=>$user,
            'token'=>$token
        ],200);
    }

    // public function logout()
    // {
    //     Auth::user()->tokens->each(function($token,$key){
    //         $token->delete();
    //     });
    //     return $this->successResponse('logedd out!!!',200);
    // }
    public function logout() 
        {
            // Auth::user()->tokens->each(function($token, $key) {
            //     $token->delete();
            // });

            auth()->user()->tokens->each(function($token,$key){
                $token->delete();
            });
            
            return $this->successResponse('Logged out successfully!',200);

            // return response()->json([
            //     'message' => 'Logged out successfully!',
            //     'status_code' => 200
            // ], 200);
        }
}
