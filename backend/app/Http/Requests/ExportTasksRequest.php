<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportTasksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|in:pending,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high',
            'user_id' => 'nullable|integer|exists:users,id',
            'search' => 'nullable|string|max:255',
            'due_date_from' => 'nullable|date',
            'due_date_to' => 'nullable|date|after_or_equal:due_date_from',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'O status deve ser: pending, in_progress ou completed',
            'priority.in' => 'A prioridade deve ser: low, medium ou high',
            'user_id.exists' => 'O usuário selecionado não existe',
            'due_date_to.after_or_equal' => 'A data final deve ser posterior ou igual à data inicial',
        ];
    }
}
