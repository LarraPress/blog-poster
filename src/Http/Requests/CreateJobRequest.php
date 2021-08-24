<?php

namespace LarraPress\BlogPoster\Http\Requests;

use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;

class CreateJobRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:1', 'max:128'],
            'email' => ['required', 'min:1', 'max:128', 'email'],
            'body' => ['required', 'min:1', 'max:5000'],
            'g-recaptcha-response' => ['required', new Recaptcha()]
        ];
    }
}
