<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:256',
            'description' => 'nullable',
            'completed' => 'sometimes|required',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required',
            'title.max' => 'Task cannot exceed 256 characters',
            'completed.required' => 'Completed is required',
        ];
    }
}
