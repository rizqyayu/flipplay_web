<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isPatch = $this->isMethod('PATCH') || $this->isMethod('PUT');
        $routeMedia = $this->route('media');
        $mediaId = is_object($routeMedia) ? $routeMedia->id : $routeMedia;

        $titleRules = [
            'required',
            'string',
            'max:255',
            Rule::unique('media', 'title')->ignore($mediaId),
        ];

        if ($isPatch) {
            array_unshift($titleRules, 'sometimes');
        }

        return [
            'title' => $titleRules,
            'category' => $isPatch ? 'sometimes|required|string|max:100' : 'required|string|max:100',
            'genre' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string',
            'episodes' => 'sometimes|nullable|integer|min:1',
            'format' => 'sometimes|nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi',
            'title.string' => 'Judul media harus berupa teks',
            'title.max' => 'Judul media maksimal 255 karakter',
            'title.unique' => 'Judul media sudah terdaftar dan tidak boleh duplikat',
            'category.required' => 'Kategori media wajib diisi',
            'category.string' => 'Kategori media harus berupa teks',
            'episodes.integer' => 'Jumlah episode harus berupa angka',
            'episodes.min' => 'Jumlah episode minimal 1',
        ];
    }
}
