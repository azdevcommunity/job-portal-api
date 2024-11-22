<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BlogsResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->whenNotNull($this->id),
            'title' => $this->title,
            'content' => $this->content,
            'isActive' => $this->is_active,
            'imageUrl' => $this->image ? asset('storage/' . $this->image) : null,
            'categoryId' => $this->category_id,
            'categories_name' => $this->categories_name,
            'createdAt' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'updatedAt' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
        ];
    }
}
