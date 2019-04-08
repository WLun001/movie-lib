<?php

namespace App\Http\Controllers;

use App\Actor;
use App\Http\Requests\ActorRequest;
use App\Http\Resources\ActorCollection;
use App\Http\Resources\ActorResource;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
     * Search actors based on actor name or/and movie name
     *
     * @param Request $request
     * @return ActorCollection
     */
    public function search(Request $request)
    {
        $name = $request->input('name');
        $movie = $request->input('movie');

        $actors = Actor::with('movies')
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'like', "%$name%");
            })
            ->whereHas('movies', function ($query) use ($movie) {
                return $query->where('name', 'like', "%$movie%");
            })
            ->get();

        return new ActorCollection($actors);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param ActorRequest $request
     * @return Response
     */
    public function store(ActorRequest $request)
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
     * @return ActorResource
     */
    public function show($id)
    {
        try {
            $actor = Actor::with('movies')->find($id);
            if (!$actor) throw new ModelNotFoundException('model not found');
            return new ActorResource($actor);
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
     * Update the specified resource in storage.
     *
     * @param ActorRequest $request
     * @param int $id
     * @return Response
     */
    public function update(ActorRequest $request, $id)
    {
        try {
            $actor = Actor::find($id);
            $actor->movies()->sync($request->movies);
            if (!$actor) throw new ModelNotFoundException('model not found');
            $actor->update($request->all());
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
    public function destroy($id)
    {
        try {
            $actor = Actor::find($id);
            if (!$actor) throw new ModelNotFoundException('model not found');
            $actor->movies()->detach();
            $actor->delete();
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
}
