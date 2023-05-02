<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class EventController extends Controller
{
    //
    public function allEvents(Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $events = Event::where('status', 1)->get();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('events'), 201);
    }



    public function createEvent (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255'
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

            $event = Event::create([
                'title' => $request->get('title'),
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
        $success = 'Event Created Successfully!';

        return response()->json(compact('success'), 201);
    }


    public function getAllEvents(Request $request)
    {
        try {

            $events = Event::where('status', 1)->get();

        }  catch (Exception $e) {

            return response()->json(['an error occured'], $e->getStatusCode());

        }

        return response()->json(compact('events'), 201);
    }


    public function updateEvent (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255'
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

            $event = Event::find($request->get('id'));

            $check_if_in_use = Donation::where('event', $event->title)->first();

            if($check_if_in_use){
                return response()->json([
                    "error" => "You cannot update ".$event->title."! It is being used by others. However, you can create an event with your preferred title."
                ], 401);
            }

            $event->title = $request->title;

            $event->save();

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


    public function deleteEvent (Request $request){
    
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            if($user->role !== 'admin'){
                return response()->json([
                    "error" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $event = Event::find($request->id);

            $check_if_in_use = Donation::where('event', $event->title)->first();

            if($check_if_in_use){
                return response()->json([
                    "error" => "You cannot delete ".$event->title."! It is being used by others. However, you can create an event with your preferred title."
                ], 401);
            }

            $event->delete();

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
