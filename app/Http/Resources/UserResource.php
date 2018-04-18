<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\LoanResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'first_name'  => $this->first_name,
            'family_name' => $this->family_name,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'loans'       => LoanResource::collection($this->whenLoaded('loans')),
            'api_token'   => $this->when(! empty($this->api_token), $this->api_token),
            'created_at'  => $this->created_at,
            'links'       => [
                [
                    'rel'  => 'self',
                    'href' => "/api/v1/users/{$this->id}"
                ],
                [
                    'rel'  => 'loans',
                    'href' => "/api/v1/users/{$this->id}/loans"
                ],
            ]
        ];
    }
}
