<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudioCollection;
use App\Http\Resources\StudioResource;
use App\Studio;
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
        $studios = Studio::with('movies')->get();
        return new StudioCollection($studios);
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
            $studio = Studio::create($request->all());
            $studio->movies()->sync($request->movies);
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
        $studio = Studio::with('movies')->find($id);
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
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $studio = Studio::find($id);
        $studio->movies()->sync($request->movies);
        if (!$studio) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found',
            ], 404);
        }
        $studio->update($request->all());
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
