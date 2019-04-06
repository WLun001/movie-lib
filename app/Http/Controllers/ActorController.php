<?php

namespace App\Http\Controllers;

use App\Actor;
use App\Http\Resources\ActorCollection;
use App\Http\Resources\ActorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ActorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ActorCollection
     */
    public function index()
    {
        $actors = Actor::with('movies')->get();
        return new ActorCollection($actors);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $actor = Actor::create($request->all());
            $actor->movies()->sync($request->movies);
            return response()->json([
                'id' => $actor->id,
                'created_at' => $actor->created_at,
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
     * @return ActorResource
     */
    public function show($id)
    {
        $actor = Actor::with('movies')->find($id);
        if (!$actor) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found',
            ], 404);
        }
        return new ActorResource($actor);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $actor = Actor::find($id);
        $actor->movies()->sync($request->movies);
        if (!$actor) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found',
            ], 404);
        }
        $actor->update($request->all());
        return response()->json(null, 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $actor = Actor::find($id);
        if (!$actor) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found',
            ], 404);
        }
        $actor->delete();
        return response()->json(null, 204);
    }
}
