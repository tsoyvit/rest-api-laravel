<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'user_id' => $this->user_id,
            'categoryName' => optional($this->category)->title,
            'title' => $this->title,
            'content' => $this->when(
                $request->routeIs('posts.show', 'posts.store'),
                $this->content
            ),
            'created' => $this->created_at->format('Y-m-d H:i:s'),
            'updated' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
