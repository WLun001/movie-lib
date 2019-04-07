<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return UserCollection
     */
    public function index()
    {
        $users = User::with('studios')->get();
        return new UserCollection($users);
    }

    /**
     * Search users based on name or/and studio name
     *
     * @param Request $request
     * @return UserCollection
     */
    public function search(Request $request)
    {
        $name = $request->input('name');
        $studio = $request->input('studio');

        $user = User::with('studios')
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'like', "%$name%");
            })
            ->whereHas('studios', function ($query) use ($studio) {
                return $query->where('name', 'like', "%$studio%");
            })
            ->get();

        return new UserCollection($user);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     * @return Response
     */
    public function store(UserRequest $request)
    {
        try {
            $user = Studio::create($request->all());
            return response()->json([
                'id' => $user->id,
                'created_at' => $user->created_at,
            ], 201);
        } catch (ValidationException $exception) {
            return response()->json([
                'errors' => $exception->errors()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return UserResource
     */
    public function show($id)
    {
        $user = User::with('studios')->find($id);
        if (!$user) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found',
            ], 404);
        }
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found',
            ], 404);
        }
        $user->update($request->all());
        return response()->json(null, 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public
    function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found',
            ], 404);
        }
        $user->delete();
        return response()->json(null, 204);
    }
}
