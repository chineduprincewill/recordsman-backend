<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Mail\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class GroupController extends Controller
{
    //
    public function allGroups(Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $groups = Group::where('status', 1)->get();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('groups'), 201);
    }


    public function updateGroup (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'mobile' => 'required|string|max:14'
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

            if($usertype !== 'admin' || $user->groupid !== 0){
                return response()->json([
                    "error" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $group = Group::find($request->get('id'));

            $group->category = $request->category;
            $group->title = $request->title;
            $group->mobile = $request->mobile;
            $group->email = $request->email;

            $group->save();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        //$users = User::all();
        $success = $request->title.' Updated Successfully!';

        return response()->json(compact('success'), 201);
    }


    public function createGroup (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'mobile' => 'required|string|max:14'
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

            if($usertype !== 'admin' || $user->groupid !== 0){
                return response()->json([
                    "error" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $groupname = Group::find($request->get('groupid'));

            $group = Group::create([
                'category' => $request->get('category'),
                'title' => $request->get('title'),
                'mobile' => $request->get('mobile'),
                'email' => $request->get('email'),
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
        $success = $request->category.' Created Successfully!';

        return response()->json(compact('success'), 201);
    }


    public function deleteGroup (Request $request){
    
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            if($user->role !== 'admin' || $user->groupid !== 0){
                return response()->json([
                    "error" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $group = Group::find($request->id);
            $group->delete();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $success = 'Deleted Successfully!';

        return response()->json(compact('success'), 201);
    }


    public function listBranches (Request $request)
    {
        $branches = Group::where('category', 'Branch')->get();

        return response()->json(compact('branches'), 201);
    }


    public function listGroups (Request $request)
    {
        $groups = Group::where('status', 1)->get();

        return response()->json(compact('groups'), 201);
    }
}
