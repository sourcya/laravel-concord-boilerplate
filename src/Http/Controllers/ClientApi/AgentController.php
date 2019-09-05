<?php

namespace Sourcya\BoilerplateBox\Http\Controllers\ClientApi;

use Sourcya\AgentModule\Models\AgentMetaProxy;
use Sourcya\AgentModule\Models\AgentProxy;;
use Sourcya\AttributeModule\Models\AttributeProxy;
use Sourcya\AttributeModule\Models\AttributeStaticOptionProxy;
use Sourcya\BoilerplateBox\Http\Controllers\Controller;
use Sourcya\CoreBox\Http\Resources\AttributeCollection;
use Sourcya\StatusModule\Models\StatusProxy;

class AgentController extends Controller
{

 /**
 * AgentsController constructor.
 */
    public function __construct()
    {
        $this->middleware('role:Client');
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1');
    }

  public function register()
  {
      $attributes = request()->validate([
          'city_id' => 'required|exists:cities,id',
          'attributes_name' => 'required|exists:attributes,name',
          'keys' => 'required',
          'values' => 'nullable',
      ]);

      if (auth()->user()->agent()->count()){
          $agent = auth()->user()->agent()->first();
          $agent_status = StatusProxy::where('statusable_id',$agent->code)->where('statusable_type','Agent')
              ->orderBy('created_at', 'desc')->first()->name;
          $message = 'Your already have been created an Agent account and your account is '.$agent_status;
          return $this->errorResponseHandler($message, 404);
      }

      $agent_meta_validator = false;
      $z = 0;
      foreach ($attributes['attributes_name'] as $agentAttribute) {
          if (AttributeProxy::where('name', $agentAttribute)->exists()) {
              $attribute = AttributeProxy::where('name', $agentAttribute)->first();
              if ($attribute->type == 'Selectable') {
                  $agent_meta_validator = false;
                  break;
              } else {
                  if (AttributeStaticOptionProxy::where('attribute_id', $attribute->id)
                      ->where('name', $attributes['keys'][$z])->exists()) {
                      $agent_meta_validator = true;
                  } else {
                      $agent_meta_validator = false;
                      break;
                  }
              }
          }
          $z++;
      }

      if ($agent_meta_validator == false) {
          $message = 'Invalid attributes data';
          return $this->errorResponseHandler($message, 422);
      }

      $city_id = $attributes['city_id'];
          $agent = AgentProxy::create([
              'user_id' => auth()->id(),
              'city_id' => $city_id,
              'code' => $this->getRandomUniqueAgentCode(),
          ]);
          StatusProxy::create([
              'statusable_id' => $agent->code,
              'statusable_type' => 'Agent',
              'name' => 'Pending'
          ]);

          $i = 0;
          foreach ($attributes['attributes_name'] as $attribute_name) {
              if ($attributes['keys'][$i]) {
                  $data = [
                      'key' => $attributes['keys'][$i],
                      'value' => $attributes['values'][$i],
                      'attribute' => $attribute_name,
                      'agent_code' => $agent->code
                  ];
                  AgentMetaProxy::create($data);
              }
              $i++;
          }

          return response()->json(['status' => 'success', 'message' => 'Your request has been submitted and pending']);
  }

    /**
     * @return AttributeCollection
     */
  public function getAgentAttributes(){
        $agent_attributes = AttributeProxy::where('assigned_for','Agent')->get();
        if ($agent_attributes->count()){
            $message = 'All Agent attributes has been fetched successfully';
            return new AttributeCollection($agent_attributes, $message);
        } else {
            $message = 'No required Agent attributes yet';
            return $this->errorResponseHandler($message, 404);
        }
  }

    /**
     * @return int
     */
    public function getRandomUniqueAgentCode()
    {
        $ids = AgentProxy::pluck('code')->toArray();

        do {
            $id = rand(1000000000, 9999999999);
        } while (in_array($id, $ids));

        return $id;
    }

}
