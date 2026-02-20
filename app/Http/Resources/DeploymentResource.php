<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeploymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'name' => $this->resource['attributes']['name'] ?? 'Sans nom',
            'service_name' => $this->resource['service'] ?? null,
            'service_pack_id' => $this->resource['pack'] ?? null,
            'offer' => $this->resource['offer'] ?? null,
            'status' => strtolower($this->resource['status'] ?? 'pending'),
            'datacenter' => $this->resource['infra']['datacenter'] ?? $this->resource['datacenter'] ?? null,
            'ip' => $this->resource['infra']['ip'] ?? $this->resource['ip'] ?? null,
            'specs' => $this->resource['performances'] ?? [],
            'created_at' => $this->resource['created_at'] ?? null,
        ];
    }
}
