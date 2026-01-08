<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvatarRequestUpdate extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:3072', // max 3MB
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Pilih foto profil terlebih dahulu.',
            'avatar.image'    => 'File harus berupa gambar.',
            'avatar.mimes'    => 'Format gambar: JPG, PNG, GIF, WebP.',
            'avatar.max'      => 'Ukuran maksimal 3MB.',
        ];
    }
}
