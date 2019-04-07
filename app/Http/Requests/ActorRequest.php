<?php

namespace App\Http\Requests;


class ActorRequest extends ApiFormRequest
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
            'sex' => 'required|min:1|max:1',
            'age' => 'required|integer',
            'movies' => 'array',
            'movies.*' => 'integer'
        ];
    }
}
