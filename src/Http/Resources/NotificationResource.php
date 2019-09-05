<?php

namespace Sourcya\BoilerplateBox\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'notifiable_type' => $this->notifiable_type,
            'notifiable_id' => $this->notifiable_id,
            'notification' => $this['data'],
            'read_at' => optional($this->read_at)->toDateTimeString(),
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
