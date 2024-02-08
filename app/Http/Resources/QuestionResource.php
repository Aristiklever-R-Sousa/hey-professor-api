<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Question $this */

        return [
            'id'         => $this->id,
            'question'   => $this->question,
            'status'     => $this->status,
            'created_by' => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'created_at' => $this->created_at->format('Y-m-d h:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d h:i:s'),
        ];
    }
}
