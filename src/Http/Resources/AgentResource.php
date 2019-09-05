<?php

namespace Sourcya\BoilerplateBox\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Sourcya\AttributeModule\Models\AttributeProxy;
use Sourcya\StatusModule\Models\StatusProxy;

class AgentResource extends JsonResource
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
            'code' => $this->code,
            'status' => StatusProxy::where('statusable_id',$this->code)->where('statusable_type','Agent')
                ->orderBy('created_at', 'desc')->first()->name,
            'user' => new UserResource($this->user),
            'city' => $this->city,
            'agent_meta' => $this->agentMeta,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => 'success',
            'message' => $this->message
        ];
    }
}
