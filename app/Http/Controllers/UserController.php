<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Mail\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {

            if(! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }

            $token = JWTAuth::attempt($credentials);

        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $role = JWTAuth::user()->role;
        $email = JWTAuth::user()->email;
        $user = JWTAuth::user();

        //return response()->json(compact('token'));
        return response()->json(compact('role', 'token', 'user'), 201);
    }


    public function createUser (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|string|max:10',
            'username' => 'required|string|max:255|unique:users',
            'groupid' => 'required|integer',
            'mobile' => 'required|string|max:14',
            'email' => 'required|string|email|max:255|unique:users'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $usertype = $user->role;
            $email = $user->email;

            if($usertype !== 'admin' || $user->groupid != 0){
                return response()->json([
                    "error" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $groupname = Group::find($request->get('groupid'));

            $user = User::create([
                'username' => $request->get('username'),
                'mobile' => $request->get('mobile'),
                'groupid' => $request->get('groupid'),
                'groupname' => $groupname->title,
                'role' => $request->get('role'),
                'email' => $request->get('email'),
                'password' => Hash::make('1234567'),
                'created_by' => $email,
                'status' => 1,
            ]);

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        //$users = User::all();
        $success = 'User Created Successfully!';

        return response()->json(compact('success'), 201);
    }


    public function allUsers (Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $role = $user->role;

            if($role !== 'admin' && $role !== 'auditor'){
                return response()->json([
                    "error" => "You do not have the permission to access this resource!"
                ], 401);
            }

            if($user->groupid !== 0){
                $users = User::where('groupid', $user->groupid)->orderBy('id', 'desc')->get();
            }
            else{
                $users = User::orderBy('id', 'desc')->get();
            }


        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('users'), 201);
    }


    public function deactivate (Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $usertype = $user->usertype;

            if($usertype !== 'admin'){
                return response()->json([
                    "message" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $userinfo = User::find($request->id);
            $pass = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 9);

            $userinfo->password = Hash::make($pass);
            $userinfo->status = 0;
            $userinfo->save();

            $users = User::all();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $message = 'User Account Deactivated!';
        return response()->json(compact('users', 'message'), 201);
    }


    public function activate (Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $usertype = $user->usertype;

            if($usertype !== 'admin'){
                return response()->json([
                    "message" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $userinfo = User::find($request->id);
            $email = $userinfo->email;

            // SET USER TO ACTIVE
            $userinfo->status = 1;
            $userinfo->save();

            // SEND PASSWORD RESET LINK TO USER EMAIL
            $status = Password::sendResetLink(
                ['email' => $email]
            );
    
            if($status == Password::RESET_LINK_SENT) {
                return [
                    'status' => __($status)
                ];
            }
    
            throw Exception::withMessages([
                'email' => [trans($status)],
            ]);

            $users = User::all();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $message = 'User Account Activated! Password Resent link sent to user email.';
        return response()->json(compact('users', 'message'), 201);
    }


    public function updateUser (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|string|max:10',
            'username' => 'required|string|max:255',
            'groupid' => 'required|integer',
            'mobile' => 'required|string|max:14',
            'email' => 'required|string|email|max:255'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $usertype = $user->role;
            $user_to_edit = User::find($request->id);
            
            if($user->role !== 'admin' || $user->groupid != 0){
                return response()->json([
                    "error" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $group = Group::find($request->groupid);

            $user_to_edit->role = $request->role;
            $user_to_edit->username = $request->username;
            $user_to_edit->mobile = $request->mobile;
            $user_to_edit->email = $request->email;
            $user_to_edit->groupid = $request->groupid;
            $user_to_edit->groupname = $group->title;

            $user_to_edit->save();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $success = 'User Updated Successfully!';

        return response()->json(compact('success'), 201);
    }


    public function deleteUser (Request $request){
    
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $usertype = $user->role;

            // restrict non admin roles from deleting a user
            if($usertype !== 'admin' || $user->groupid != 0){
                return response()->json([
                    "error" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $userinfo = User::find($request->id);

            $userinfo->delete();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $success = 'User Deleted Successfully!';

        return response()->json(compact('success'), 201);
    }


    public function updatePassword (Request $request){

        $validator = Validator::make($request->all(), [
            'curr' => 'required|string|max:255',
            'newpass' => 'required|string|max:255',
            'confirm' => 'required|string|max:255'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $user_to_update = User::find($user->id);
            
            if (!(Hash::check($request->get('curr'), JWTAuth::user()->password))) {
                // The passwords matches
                return response()->json([
                    "error" => "Incorrect current password!"
                ], 401);
            }

            if(!$this->passwordRegex($request->newpass)){
                return response()->json([
                    "error" => "Password criteria not met!"
                ], 401);
            }

            if($request->newpass !== $request->confirm){
                return response()->json([
                    "error" => "Password confirmation mismatch!"
                ], 401);
            }

            $user_to_update->password = Hash::make($request->newpass);

            $user_to_update->save();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $success = 'Password Updated Successfully!';

        return response()->json(compact('success'), 201);
    }


    public function passwordRegex ($string)
    {
        $uppercase = preg_match('@[A-Z]@', $string);
        $number    = preg_match('@[0-9]@', $string);
        $specialChars = preg_match('@[^\w]@', $string);
        
        if(strlen($string) >= 8 && $uppercase && $number && $specialChars) {
            return true;
        } else {
            return false;
        }
    }


}
