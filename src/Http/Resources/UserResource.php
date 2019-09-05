<?php

namespace Sourcya\BoilerplateBox\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'default_address' => optional($this)->address->where('id',$this->address_id)->first(),
            'default_contact' => optional($this)->contact->where('id',$this->contact_id)->first(),
            'roles' => optional($this)->roles()->pluck('name'),
            'email_verified_at' => $this->email_verified_at,
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
