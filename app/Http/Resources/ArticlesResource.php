<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticlesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        if(is_null($this->resource))
        {
            return [];
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'externalLink' => $this->sub_title,
            'desc' => $this->desc,
            'image' => $this->pathImage(),
            'type' => $this->type,
            'pin' => $this->pin,
        ];
    }
}
