<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'book'        => [
                'id'     => $this->book->id,
                'title'  => $this->book->title,
                'author' => $this->book->author,
            ],
            'user'        => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'rented_at'   => $this->rented_at?->toDateString(),
            'due_at'      => $this->due_at?->toDateString(),
            'returned_at' => $this->returned_at?->toDateString(),
            'fine_amount' => $this->fine_amount,
        ];
    }
}
