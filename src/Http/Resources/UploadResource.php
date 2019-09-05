<?php

namespace Sourcya\BoilerplateBox\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UploadResource extends JsonResource
{
    private $message;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource,$message = null)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->message = $message;

    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'url' => Storage::disk('public')->url($this->path.'/'.$this->name.'.'.$this->extension),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),

        ];
    }
    public function with($request)
    {
        return [
            'status' => 'success',
            'message' => $this->message
        ];
    }
}
