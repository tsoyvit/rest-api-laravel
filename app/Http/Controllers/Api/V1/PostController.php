<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\V1\PostResource;
use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class PostController extends BaseApiController
{
    public function index(): AnonymousResourceCollection
    {
        return PostResource::collection(Post::paginate(3));
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();
            $post = Post::create($data);

            return $this->successResponse(PostResource::make($post), "Post created successfully.", 201);
        } catch (\Throwable $e) {
            Log::error('PostController:store error: ' . $e->getMessage());
            return $this->errorResponse('Failed to create post.', 500, $e);
        }
    }

    public function show(Post $post): JsonResponse
    {
        return $this->successResponse(PostResource::make($post), "Post retrieved  successfully.");
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        try {
            $post->update($request->validated());
            return $this->successResponse(PostResource::make($post), "Post updated successfully.");
        } catch (\Throwable $e) {
            Log::error('PostController:update error: ' . $e->getMessage());
            return $this->errorResponse('Failed to update post.', 500, $e);
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        try {
            $post->delete();
            return $this->successResponse(null, "Post deleted successfully.");
        } catch (\Throwable $e) {
            Log::error('PostController:delete error: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete post.', 500, $e);
        }
    }
}
