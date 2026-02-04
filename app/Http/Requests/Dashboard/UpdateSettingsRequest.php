<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'notifications_enabled' => ['sometimes', 'boolean'],
            'subscription_reminders' => ['sometimes', 'boolean'],
            'license_expiry_alerts' => ['sometimes', 'boolean'],
            'timezone' => ['sometimes', 'nullable', 'string', 'timezone'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'timezone.timezone' => 'Please select a valid timezone.',
        ];
    }
}
