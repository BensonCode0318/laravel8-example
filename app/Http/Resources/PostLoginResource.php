<?php


namespace App\Http\Resources;

class PostLoginResource extends BaseJsonResource
{
    public function toArray($request)
    {
        return [
            'access_token' => $this['access_token'],
            'user'         => new GetUserResource($this['user']),
        ];
    }
}
