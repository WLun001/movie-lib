<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Bouncer;

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
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);
            $role = $request['role'];
            Bouncer::assign($role)->to($user);
            return response()->json([
                'id' => $user->id,
                'created_at' => $user->created_at,
            ], 201);
        } catch (ValidationException $exception) {
            return response()->json([
                'errors' => $exception->errors()
            ], 422);
        } catch (Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
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
        try {
            $user = User::with('studios')->find($id);
            if (!$user) throw new ModelNotFoundException('user not found');
            return new UserResource($user);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'errors' => $exception->getMessage()
            ], 404);
        } catch (Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
        }
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
        try {
            $user = User::find($id);
            if (!$user) throw new ModelNotFoundException('user not found');
            $user->update([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);
            $role = $request['role'];
            $user->roles()->detach();
            Bouncer::assign($role)->to($user);
            return response()->json(null, 204);
        } catch (ValidationException $exception) {
            return response()->json([
                'errors' => $exception->errors()
            ], 422);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'errors' => $exception->getMessage()
            ], 404);
        } catch (Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
        }
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
        try {
            $user = User::find($id);
            if (!$user) throw new ModelNotFoundException('user not found');
            if ($user->studios) {
                foreach ($user->studios as $studio) {
                    $studio->user()->dissociate();
                    $studio->save();
                }
            }
            $user->roles()->detach();
            $user->delete();
            return response()->json(null, 204);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'errors' => $exception->getMessage()
            ], 404);
        } catch (Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
        }
    }
}
