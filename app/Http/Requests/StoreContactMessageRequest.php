<?php

namespace App\Http\Requests;

use App\Rules\Turnstile;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            // Honeypot: hidden on the form, so only bots fill it in.
            'website' => ['prohibited'],
            'turnstile_token' => [Turnstile::enabled() ? 'required' : 'nullable', 'string', new Turnstile($this->ip())],
        ];
    }
}
