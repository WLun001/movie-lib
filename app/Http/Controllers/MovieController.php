<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieRequest;
use App\Http\Resources\MovieCollection;
use App\Http\Resources\MovieResource;
use App\Movie;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
     * Search movies based on movie name or/and actor name or/and studio name
     *
     * @param Request $request
     * @return MovieCollection
     */
    public function search(Request $request)
    {
        $name = $request->input('name');
        $studio = $request->input('studio');
        $actor = $request->input('actor');

        $movies = Movie::with(['actors', 'studio'])
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'like', "%$name%");
            })
            ->whereHas('studio', function ($query) use ($studio) {
                return $query->where('name', 'like', "%$studio%");
            })
            ->whereHas('actors', function ($query) use ($actor) {
                return $query->where('name', 'like', "%$actor%");
            })
            ->get();

        return new MovieCollection($movies);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param MovieRequest $request
     * @return Response
     */
    public function store(MovieRequest $request)
    {
        try {
            $movie = Movie::create($request->all());
            $movie->actors()->sync($request->actors);
            return response()->json([
                'id' => $movie->id,
                'created_at' => $movie->created_at,
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
     * @return MovieResource
     */
    public function show($id)
    {
        try {
            $movie = Movie::with(['actors', 'studio'])->find($id);
            if (!$movie) throw new ModelNotFoundException('model not found');
            return new MovieResource($movie);
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
     * @param MovieRequest $request
     * @param int $id
     * @return Response
     */
    public function update(MovieRequest $request, $id)
    {
        try {
            $movie = Movie::find($id);
            $movie->actors()->sync($request->actors);
            if (!$movie) throw new ModelNotFoundException('model not found');
            $movie->update($request->all());
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
            $movie = Movie::find($id);
            if (!$movie) throw new ModelNotFoundException('model not found');
            $movie->actors()->detach();
            $movie->delete();
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
