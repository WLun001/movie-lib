<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudioRequest;
use App\Http\Resources\StudioCollection;
use App\Http\Resources\StudioResource;
use App\Studio;
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
            ]);
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
        $studio = Studio::with(['movies', 'user'])->find($id);
        if (!$studio) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found',
            ], 404);
        }
        return new StudioResource($studio);
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
        $studio = Studio::find($id);
        if (Gate::allows('update-studio', $studio)) {
            if (!$studio) {
                return response()->json([
                    'error' => 404,
                    'message' => 'Not found',
                ], 404);
            }
            $studio->update($request->all());
            return response()->json(null, 204);
        } else {
            return response()->json([
                'error' => 403,
                'message' => "You are forbidden to edit $studio->name" ,
            ], 404);
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
        $studio = Studio::find($id);
        if (!$studio) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found',
            ], 404);
        }
        $studio->delete();
        return response()->json(null, 204);
    }
}
