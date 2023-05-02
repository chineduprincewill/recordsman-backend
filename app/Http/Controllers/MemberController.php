<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Group;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class MemberController extends Controller
{
    //
    public function allMembers (Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            if($user->groupid > 0){
                $members = Member::where('status', 1)->where('branchid', $user->groupid)->orderBy('id', 'desc')->get();
            }
            else{
                $members = Member::where('status', 1)->orderBy('id', 'desc')->get();
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('members'), 201);
    }


    public function getMembers (Request $request)
    {
        try {

            $members = Member::where('status', 1)->orderBy('id', 'desc')->get();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('members'), 201);
    }


    public function createMember (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lastname' => 'required|string|max:255',
            'branchid' => 'required|integer',
            'mobile' => 'required|string|max:14',
            'gender' => 'required|string|max:255',
            'wing' => 'required|string|max:255'
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

            if($usertype !== 'admin'){
                return response()->json([
                    "error" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $branchname = Group::find($request->get('branchid'));
            $uid = mt_rand(1000000000,9999999999);

            $member = Member::create([
                'category'=> $request->get('category'),
                'uid' => $uid,
                'lastname' => $request->get('lastname'),
                'firstname' => $request->get('firstname'),
                'othernames' => $request->get('othernames'),
                'fullname' => $request->get('lastname').' '.$request->get('firstname').' '.$request->get('othernames'),
                'mobile' => $request->get('mobile'),
                'gender' => $request->get('gender'),
                'branchid' => $request->get('branchid'),
                'branch' => $branchname->title,
                'wing' => $request->get('wing'),
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
        $success = 'Member Created Successfully!';

        return response()->json(compact('success'), 201);
    }



    public function updateMember (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lastname' => 'required|string|max:255',
            'branchid' => 'required|integer',
            'mobile' => 'required|string|max:14',
            'gender' => 'required|string|max:255',
            'wing' => 'required|string|max:255'
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

            if($usertype !== 'admin'){
                return response()->json([
                    "error" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $member = Member::find($request->get('id'));
            $branchname = Group::find($request->get('branchid'));

            $member->category = $request->category;
            $member->lastname = $request->lastname;
            $member->firstname = $request->firstname;
            $member->othernames = $request->othernames;
            $member->fullname = $request->lastname.' '.$request->firstname.' '.$request->othernames;
            $member->mobile = $request->mobile;
            $member->email = $request->email;
            $member->branchid = $request->branchid;
            $member->branch = $branchname->title;
            $member->gender = $request->gender;
            $member->wing = $request->wing;

            $member->save();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $success = 'Member Updated Successfully!';

        return response()->json(compact('success'), 201);
    }



    public function deleteMember (Request $request){
    
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            if($user->role !== 'admin'){
                return response()->json([
                    "error" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $member = Member::find($request->id);
            $member->delete();

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
}
