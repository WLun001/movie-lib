<?php

namespace App\Http\Requests;


class MovieRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'rating' => 'required|integer',
            'year' => 'required|integer',
            'duration' => 'required|integer',
            'studio_id' => 'required|integer',
            'actors' => 'array',
            'actors.*' => 'integer'
        ];
    }
}
