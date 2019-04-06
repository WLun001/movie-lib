<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieCollection;
use App\Http\Resources\MovieResource;
use App\Movie;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return MovieCollection
     */
    public function index()
    {
        $movies = Movie::with(['actors', 'studio'])->get();
        return new MovieCollection($movies);
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
            $movie = Movie::create($request->all());
            $movie->studio()->sync($request->studio);
            $movie->actors()->sync($request->actors);
            return response()->json([
                'id' => $movie->id,
                'created_at' => $movie->created_at,
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
     * @return MovieResource
     */
    public function show($id)
    {
        $movie = Movie::with(['actors', 'studio'])->find($id);
        if (!$movie) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found',
            ], 404);
        }
        return new MovieResource($movie);
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
        $movie = Movie::find($id);
        $movie->studio()->sync($request->studio);
        $movie->actors()->sync($request->actors);
        if (!$movie) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found',
            ], 404);
        }
        $movie->update($request->all());
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
        $movie = Movie::find($id);
        if (!$movie) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found',
            ], 404);
        }
        $movie->delete();
        return response()->json(null, 204);
    }
}
