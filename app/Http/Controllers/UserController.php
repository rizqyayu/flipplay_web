<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCollectionRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna beserta koleksi medianya.
     */
    public function index(): AnonymousResourceCollection
    {
        $users = User::with('media')->paginate(10);

        return UserResource::collection($users);
    }

    /**
     * Menampilkan detail pengguna beserta koleksi medianya.
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user->load('media'));
    }

    /**
     * Menambahkan pengguna baru.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = $data['password'] ?? 'password';

        $user = User::create($data);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(StoreUserRequest $request, User $user): UserResource
    {
        $data = $request->validated();
        if (!array_key_exists('password', $data) || $data['password'] === null) {
            unset($data['password']);
        }

        $user->update($data);

        return new UserResource($user->load('media'));
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus',
        ], 200);
    }

    /**
     * Menampilkan daftar koleksi media milik seorang pengguna.
     */
    public function collection(User $user): AnonymousResourceCollection
    {
        $collection = $user->media()->paginate(10);

        return MediaResource::collection($collection);
    }

    /**
     * Custom action: Menambahkan media ke dalam koleksi pribadi pengguna.
     */
    public function addToCollection(AddToCollectionRequest $request, User $user): JsonResponse
    {
        return DB::transaction(function () use ($request, $user): JsonResponse {
            $mediaId = (int) $request->validated('media_id');

            $alreadyExists = $user->media()
                ->where('media_id', $mediaId)
                ->lockForUpdate()
                ->exists();

            if ($alreadyExists) {
                return response()->json([
                    'message' => 'Media sudah ada di koleksi pengguna',
                ], 409);
            }

            $user->media()->attach($mediaId);

            return response()->json([
                'message' => 'Media berhasil ditambahkan ke koleksi',
                'data' => new UserResource($user->load('media')),
            ], 201);
        });
    }

    /**
     * Custom action: Menghapus media dari koleksi pribadi pengguna.
     */
    public function removeFromCollection(User $user, Media $media): JsonResponse
    {
        return DB::transaction(function () use ($user, $media): JsonResponse {
            $exists = $user->media()
                ->where('media_id', $media->id)
                ->lockForUpdate()
                ->exists();

            if (!$exists) {
                return response()->json([
                    'message' => 'Media tidak ditemukan di koleksi pengguna',
                ], 404);
            }

            $user->media()->detach($media->id);

            return response()->json([
                'message' => 'Media berhasil dihapus dari koleksi',
                'data' => new UserResource($user->load('media')),
            ], 200);
        });
    }
}
