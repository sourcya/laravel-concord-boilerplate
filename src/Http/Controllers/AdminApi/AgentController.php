<?php

namespace Sourcya\BoilerplateBox\Http\Controllers\AdminApi;

use Sourcya\BoilerplateBox\Http\Controllers\Controller;
use Sourcya\AgentModule\Models\AgentProxy;
use Sourcya\BoilerplateBox\Http\Resources\AgentCollection;
use Sourcya\StatusModule\Models\StatusProxy;
use Sourcya\UserModule\Models\UserProxy;
use Spatie\Permission\Models\Role;


class AgentController extends Controller
{
    /**
     * AgentController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1');
        $this->middleware('permission:Handling Agent Requests', ['only' => ['pending','approve','decline']]);
    }

    public function index()
    {
        if (AgentProxy::first()) {
            $agents = AgentProxy::latest()->paginate(12);
            $message = 'All agents has been fetched successfully';
            return new AgentCollection($agents,$message);
        } else {
            $message = 'No registered agents yet';
            return $this->errorResponseHandler($message,404);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|AgentCollection
     */
    public function pending()
    {
        $pending_agents = collect();
        $agent_statuses = StatusProxy::where('statusable_type','Agent')->where('name','Pending')->get();
        foreach ($agent_statuses as $key => $agent_status){
            $agent_other_status_checker = StatusProxy::where('statusable_id',$agent_status->statusable_id)
                ->where('name','Approved')
                ->orWhere('name','Declined')
                ->orWhere('name','Suspended')->exists();
            if ($agent_other_status_checker){
                $agent_statuses->forget($key);
            } else {
                if (AgentProxy::where('code',$agent_status->statusable_id)->exists()){
                    $pending_agents->push(AgentProxy::where('code',$agent_status->statusable_id)->first());
                }
            }
        }

        foreach ($pending_agents as $pending_agent){
            $pending_agent->status = StatusProxy::where('statusable_id',$pending_agent->code)->orderBy('created_at', 'desc')->first()->name;
        }
        $pending_agents = $pending_agents->paginate(12);

        if ($pending_agents->count()){
            $message = 'All pending agents has been fetched successfully';
            return new AgentCollection($pending_agents, $message);
        } else {
            $message = 'No pending agents yet';
            return $this->errorResponseHandler($message,404);
        }
    }

    /**
     * @param $agentCode
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($agentCode){
        $agent = AgentProxy::where('code',$agentCode)->first();
        $status = StatusProxy::where('statusable_id',$agent->code)->where('statusable_type','Agent')->orderBy('created_at', 'desc')->first();
        if ($status['name'] != 'Approved'){
            if (Role::where('name','Agent')->exists()) {
                $role = Role::where('name','Agent')->first();
                $user = UserProxy::find($agent->user_id);
                $user->assignRole($role);
            } else {
                $message = 'Agent role not found, please create Agent role';
                return $this->errorResponseHandler($message,404);
            }
            StatusProxy::create([
                'statusable_id' => $agent->code,
                'statusable_type' => 'Agent',
                'name' => 'Approved',
                'message' => 'Approved by user_id = '.auth()->id(),
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Agent has been approved successfully'
            ]);
        } else {
            $message = 'Agent already has been approved';
            return $this->errorResponseHandler($message,422);
        }
    }

    /**
     * @param $agentCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function decline($agentCode){
        $agent = AgentProxy::where('code',$agentCode)->first();
        $status = StatusProxy::where('statusable_id',$agent->code)->where('statusable_type','Agent')->orderBy('created_at', 'desc')->first();
        if ($status['name'] != 'Declined'){
            StatusProxy::create([
                'statusable_id' => $agent->code,
                'statusable_type' => 'Agent',
                'name' => 'Declined',
                'message' => 'Declined by user_id = '.auth()->id(),
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Agent has been declined successfully'
            ]);
        } else {
            $message = 'Agent already has been approved'.$status['name'];
            return $this->errorResponseHandler($message,422);
        }
    }
}
