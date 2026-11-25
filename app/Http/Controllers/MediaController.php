<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MediaController extends Controller
{
    /**
     * Menampilkan daftar seluruh media (mendukung filter judul & kategori + pagination).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $titleKeyword = $request->query('title', $request->query('search'));
        $category = $request->query('category');

        $media = Media::with('users')
            ->searchTitle($titleKeyword)
            ->byCategory($category)
            ->latest('id')
            ->paginate(10);

        return MediaResource::collection($media);
    }

    /**
     * Menampilkan detail satu media beserta daftar pengguna yang mengoleksinya.
     */
    public function show(Media $media): MediaResource
    {
        return new MediaResource($media->load('users'));
    }

    /**
     * Menambahkan media baru ke dalam sistem.
     */
    public function store(StoreMediaRequest $request): JsonResponse
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Memperbarui data media berdasarkan ID.
     */
    public function update(StoreMediaRequest $request, Media $media): MediaResource
    {
        $media->update($request->validated());

        return new MediaResource($media->load('users'));
    }

    /**
     * Menghapus media dari sistem.
     */
    public function destroy(Media $media): JsonResponse
    {
        $media->delete();

        return response()->json([
            'message' => 'Media berhasil dihapus',
        ], 200);
    }
}
