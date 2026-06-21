<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'website_name' => ['required', 'string', 'max:255'],
            'page_url' => ['required', 'url', 'max:2048'],
            'form_name' => ['required', 'string', 'max:255'],
            'form_identifier' => ['nullable', 'string', 'max:255'],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*' => ['nullable'],
            'honeypot' => ['nullable', 'string', 'max:255'],
            'recaptcha_token' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['new', 'read', 'spam'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'website_name' => is_string($this->website_name) ? trim($this->website_name) : $this->website_name,
            'page_url' => is_string($this->page_url) ? trim($this->page_url) : $this->page_url,
            'form_name' => is_string($this->form_name) ? trim($this->form_name) : $this->form_name,
            'form_identifier' => is_string($this->form_identifier) ? trim($this->form_identifier) : $this->form_identifier,
            'honeypot' => is_string($this->honeypot) ? trim($this->honeypot) : $this->honeypot,
        ]);
    }
}
