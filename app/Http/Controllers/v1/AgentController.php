<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreAgentRequest;
use App\Http\Requests\v1\UpdateAgentRequest;
use App\Http\Resources\v1\AgentResource;
use App\Models\User;
use App\Services\v1\AgentService;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    protected AgentService $agentService;

    public function __construct(AgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return $this->agentService->fetch();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAgentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreAgentRequest $request)
    {
        return $this->agentService->create($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show($id)
    {
        return $this->agentService->fetch($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAgentRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAgentRequest $request, int $id)
    {
        return $this->agentService->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
