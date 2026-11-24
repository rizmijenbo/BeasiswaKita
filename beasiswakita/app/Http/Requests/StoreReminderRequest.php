<?php

namespace App\Http\Requests;

use App\Models\Reminder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $email = $this->input('email');

        $this->merge([
            'email' => is_string($email) ? Str::lower(trim($email)) : $email,
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'days_before' => ['required', 'integer', Rule::in(Reminder::DAYS_OPTIONS)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid. Contoh: nama@email.com.',
            'email.max' => 'Alamat email terlalu panjang (maksimal 255 karakter).',
            'days_before.required' => 'Pilih kapan Anda ingin diingatkan.',
            'days_before.integer' => 'Pilihan waktu pengingat tidak valid.',
            'days_before.in' => 'Pilihan waktu pengingat tidak valid.',
        ];
    }

    /** Kembali ke form pengingat (anchor #pengingat) jika validasi gagal. */
    protected function getRedirectUrl(): string
    {
        return route('scholarships.show', $this->route('scholarship')).'#pengingat';
    }
}
