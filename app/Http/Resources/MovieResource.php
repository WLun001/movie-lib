<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'year' => $this->year,
            'duration' => $this->duration,
            'studio' => new StudioResource($this->whenLoaded('studio')),
            'actors' => new ActorCollection($this->whenLoaded('actors')),
        ];
    }
}
