<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Meeting;
use App\Use;
use JWTAuth;
class RegistrationController extends Controller
{
	public function __construct()
	{
		$this->middleware('jwt.auth');
	}
    public function store(Request $request)
    {
         $this->validate($request, [
            'meeting_id' => 'required',
            'user_id' => 'required',
        ]);
        $meeting_id = $request->input('meeting_id');
        $user_id = $request->input('user_id');        
        try {
                $meeting = Meeting::findOrFail($meeting_id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['msg' => 'Could not find meeting with id = '.$meeting_id], 500);
        }
        $user = User::findOrFail($user_id);
        $message = [
            'msg' => 'User is already registered for meeting',
            'user' => $user,
            'meeting' => $meeting,
            'unregister' => [
                'href' => 'api/v1/meeting/registration/' . $meeting->id,
                'method' => 'DELETE',
            ]
        ];
        if ($meeting->users()->where('users.id', $user->id)->first()) {
            return response()->json($message, 404);
        };
        $user->meetings()->attach($meeting);
        $response = [
            'msg' => 'User registered for meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregister' => [
                'href' => 'api/v1/meeting/registration/' . $meeting->id,
                'method' => 'DELETE'
            ]
        ];
        return response()->json($response, 201);
    }

    public function destroy($id)
    {
        //
    }
}
