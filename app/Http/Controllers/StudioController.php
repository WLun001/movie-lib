<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudioRequest;
use App\Http\Resources\StudioCollection;
use App\Http\Resources\StudioResource;
use App\Studio;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class StudioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return StudioCollection
     */
    public function index()
    {
        $studios = Studio::with(['movies', 'user'])->get();
        return new StudioCollection($studios);
    }

    /**
     * Search studios based on studio name or/and movie name
     *
     * @param Request $request
     * @return StudioCollection
     */
    public function search(Request $request)
    {
        $name = $request->input('name');
        $movie = $request->input('movie');

        $studio = Studio::with(['movies', 'user'])
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'like', "%$name%");
            })
            ->whereHas('movies', function ($query) use ($movie) {
                return $query->where('name', 'like', "%$movie%");
            })
            ->get();

        return new StudioCollection($studio);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StudioRequest $request
     * @return Response
     */
    public function store(StudioRequest $request)
    {
        try {
            $studio = Studio::create($request->all());
            return response()->json([
                'id' => $studio->id,
                'created_at' => $studio->created_at,
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
     * @return StudioResource
     */
    public function show($id)
    {
        try {
            $studio = Studio::with(['movies', 'user'])->find($id);
            if (!$studio) throw new ModelNotFoundException('model not found');
            return new StudioResource($studio);
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
     * @param StudioRequest $request
     * @param int $id
     * @return Response
     */
    public function update(StudioRequest $request, $id)
    {
        try {
            $studio = Studio::find($id);
            if (Gate::allows('update-studio', $studio)) {
                if (!$studio) throw new ModelNotFoundException('model not found');
                $studio->update($request->all());
                return response()->json(null, 204);
            } else {
                throw new Exception("You are forbidden to edit $studio->name", 403);
            }
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
            ], $ex->getCode() ? $ex->getCode() : 500);
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
            $studio = Studio::find($id);
            if (!$studio) throw new ModelNotFoundException('model not found');
            if ($studio->movies) {
                foreach ($studio->movies as $movie) {
                    $movie->studio()->dissociate();
                    $movie->save();
                }
            }
            $studio->delete();
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
