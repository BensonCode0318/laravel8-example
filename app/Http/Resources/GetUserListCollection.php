<?php


namespace App\Http\Resources;

class GetUserListCollection extends BaseResourceCollection
{
    public function toArray($request)
    {
        return GetUserResource::collection($this->collection);
    }
}
