<?php

namespace App\Http\Controllers\API;

use App\Models\JobOrder;
use App\Models\Contractor;
use App\Models\Conductor;
use App\Models\TypeOfWork;
use App\Http\Requests\StoreJobOrderRequest;
use App\Http\Requests\UpdateJobOrderRequest;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

class JobOrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobOrders = JobOrder::with(['contractor', 'conductor', 'typeOfWork'])
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($jo) {
                return [
                    'id' => $jo->hashed_id,
                    'name' => $jo->name,
                    'date' => $jo->date,
                    'jos_date' => $jo->jos_date,
                    'actual_work_completed' => $jo->actual_work_completed,
                    'remarks' => $jo->remarks,
                    'reference_number' => $jo->reference_number,
                    'contractor_id' => Hashids::encode($jo->contractor_id),
                    'contractor' => optional($jo->contractor)->name,
                    'conductor_id' => Hashids::encode($jo->conductor_id),
                    'conductor' => optional($jo->conductor)->first_name,
                    'type_of_work_id' => Hashids::encode($jo->type_of_work_id),
                    'type_of_work' => optional($jo->typeOfWork)->name,
                ];
            });

        return $this->sendResponse($jobOrders, 'Job Orders retrieved successfully');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('job_orders.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobOrderRequest $request)
    {
        $data = $request->validated();

        $data['contractor_id'] = Hashids::decode($data['contractor_id'])[0] ?? null;
        $data['conductor_id'] = Hashids::decode($data['conductor_id'])[0] ?? null;
        $data['type_of_work_id'] = Hashids::decode($data['type_of_work_id'])[0] ?? null;

        if (in_array(null, [$data['contractor_id'], $data['conductor_id'], $data['type_of_work_id']])) {
            return $this->sendError(['error' => 'Invalid hashed ID(s)'], 422);
        }

        $jobOrder = JobOrder::create($data);

        return $this->sendResponse($jobOrder, 'Job Order created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId)
    {
        $id = Hashids::decode($hashedId)[0] ?? null;

        if (!$id) {
            return $this->sendError(['error' => 'Invalid Job Order ID'], 404);
        }

        $jobOrder = JobOrder::with(['contractor.user', 'conductor.user', 'typeOfWork'])
            ->whereNull('deleted_at')
            ->findOrFail($id);

        return $this->sendResponse([
            'id' => $jobOrder->hashed_id,
            'name' => $jobOrder->name,
            'date' => $jobOrder->date,
            'jos_date' => $jobOrder->jos_date,
            'reference_number' => $jobOrder->reference_number,
            'actual_work_completed' => $jobOrder->actual_work_completed,
            'remarks' => $jobOrder->remarks,
            'type_of_work' => [
                'id' => $jobOrder->typeOfWork->hashed_id,
                'name' => $jobOrder->typeOfWork->name,
            ],
            'contractor' => [
                'id' => $jobOrder->contractor->hashed_id,
                'name' => $jobOrder->contractor->name,
            ],
            'conductor' => [
                'id' => $jobOrder->conductor->hashed_id,
                'name' => $jobOrder->conductor->user->name ?? '',
            ],
        ], 'Job order retrieved successfully.');
    }

    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobOrder $jobOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobOrderRequest $request, $hashedId)
    {
        $id = Hashids::decode($hashedId)[0] ?? null;
        if (!$id) return $this->sendError(['error' => 'Invalid ID'], 404);

        $data = $request->validated();
        $data['contractor_id'] = Hashids::decode($data['contractor_id'])[0] ?? null;
        $data['conductor_id'] = Hashids::decode($data['conductor_id'])[0] ?? null;
        $data['type_of_work_id'] = Hashids::decode($data['type_of_work_id'])[0] ?? null;

        $jobOrder = JobOrder::findOrFail($id);
        $jobOrder->update($data);

        return $this->sendResponse($jobOrder, 'Job Order updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($hashedId)
    {
        $id = Hashids::decode($hashedId)[0] ?? null;
        if (!$id) return $this->sendError(['error' => 'Invalid ID'], 404);

        $jo = JobOrder::findOrFail($id);
        $jo->delete();

        return $this->sendResponse([], 'Job Order deleted successfully');
    }
}
