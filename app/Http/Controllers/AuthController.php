<?php

namespace App\Http\Controllers;  
use App\Models\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
  
class AuthController extends Controller
{
 
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            // 'c_password' => 'required|same:password',
        ]);
     
     
     
       if($validator->fails()){
        return response()->json([
            "error" =>$validator->errors(),
        ],404);
       }
    
      $user =User::create(array_merge(
        $validator->validated(),
        ['password'=> bcrypt($request->password)],
      ));

        return response()->json([
            'status' => true,
            'message' => 'User successfully registered. Please check your email to verify your account.',
            'user' => $user
        ], 201);

    }
  

    public function login(Request $request){
        $validator =Validator::Make($request->all(),[
            'email'=>'required',
            'password'=>'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),404);
        }

        if(!$token = auth()->attempt($validator->validated())){
            return response()->json(['error' => 'Unauthorized'],401);
        }

        return $this->createNewToken($token);
    }

    public function createNewToken($token){

        return response()->json([
            'access_token'=> $token,
            'token_type' =>'bearer',
            'expires_in' => auth()->factory()->getTTL()*60,
            'user' => auth()->user()
        ]);
    }
  
    public function refresh(){
        return $this->createNewToken(auth()->refresh());
    }


    public function logOut(){

    }
  }
