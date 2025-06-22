<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\Models\JobOrder;
use App\Models\TypeOfWork;
use App\Models\Contractor;
use App\Models\Conductor;
use App\Models\JobOrderStatement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Vinkla\Hashids\Facades\Hashids;

class JobOrderStatementController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $year = $request->query('year', now()->year);
        $month = $request->query('month', now()->month);

        $statements = JobOrderStatement::with(['contractor', 'conductor'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest()
            ->get();

        $data = $statements->map(function ($jos) {
            return [
                'id' => $jos->hashed_id,
                'reference_number' => $jos->reference_number,
                'contractor' => $jos->contractor?->name,
                'conductor' => $jos->conductor?->full_name,
                'total_job_orders' => $jos->total_job_orders,
                'total_amount' => number_format($jos->total_amount, 2),
                'paid_amount' => number_format($jos->paid_amount, 2),
                'balance_amount' => number_format($jos->balance_amount, 2),
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('job_order_statements.index');
    }

    /**
     * Store a newly created resource in storage.
     * create JOS
     */
    public function store(Request $request)
    {
        $request->validate([
            'contractor_id' => 'required',
            'conductor_id' => 'required',
            'job_order_ids' => 'required|array|min:1',
            'paid_amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $contractorId = Hashids::decode($request->contractor_id)[0] ?? null;
        $conductorId = Hashids::decode($request->conductor_id)[0] ?? null;
        $jobOrderIds = collect($request->job_order_ids)->map(fn($id) => Hashids::decode($id)[0]);

        DB::beginTransaction();

        try {
            $jobOrders = JobOrder::whereIn('id', $jobOrderIds)
                ->with('typeOfWork')
                ->get();

            $totalAmount = $jobOrders->sum(fn($jo) =>
                $jo->actual_work_completed * ($jo->typeOfWork->rate ?? 0)
            );

            $prefix = 'JOS-' . now()->format('Ym');
            do {
                $count = JobOrderStatement::where('reference_number', 'like', $prefix . '%')->count() + 1;
                $referenceNumber = $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            } while (JobOrderStatement::where('reference_number', $referenceNumber)->exists());

            // Save to DB inside a transaction
            DB::transaction(function () use ($contractorId, $conductorId, $jobOrders, $totalAmount, $request, $referenceNumber, $jobOrderIds) {
                $jos = JobOrderStatement::create([
                    'reference_number' => $referenceNumber,
                    'contractor_id' => $contractorId,
                    'conductor_id' => $conductorId,
                    'total_job_orders' => $jobOrders->count(),
                    'total_amount' => $totalAmount,
                    'paid_amount' => $request->paid_amount,
                    'balance_amount' => $totalAmount - $request->paid_amount,
                    'remarks' => $request->remarks,
                ]);

                $jos->jobOrders()->attach($jobOrderIds); // through pivot
                JobOrder::whereIn('id', $jobOrderIds)->update(['job_order_statement_id' => $jos->id]);
            });

            return response()->json([
                'success' => true,  
                'message' => 'Job Order Statement created successfully.',
                'id' => Hashids::encode($jos->id),
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create Job Order Statement.'], 500);
        }
    }
    /* {
        $request->validate([
            'contractor_id' => 'required',
            'conductor_id' => 'required',
            'job_order_ids' => 'required|array|min:1',
            'paid_amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $contractorId = Hashids::decode($request->contractor_id)[0] ?? null;
        $conductorId = Hashids::decode($request->conductor_id)[0] ?? null;
        $jobOrderIds = collect($request->job_order_ids)->map(fn($id) => Hashids::decode($id)[0]);

        DB::beginTransaction();

        try {
            // Calculate total amount
            $jobOrders = JobOrder::whereIn('id', $jobOrderIds)
                ->with('typeOfWork')
                ->get();

            $totalAmount = $jobOrders->sum(fn($jo) =>
                $jo->actual_work_completed * ($jo->typeOfWork->rate ?? 0)
            );

            // Generate unique reference number
            $prefix = 'JOS-' . now()->format('Ym');
            $count = JobOrderStatement::where('reference_number', 'like', $prefix . '%')->count() + 1;
            $referenceNumber = $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            // Create JOS
            $jos = JobOrderStatement::create([
                'reference_number' => $referenceNumber,
                'contractor_id' => $contractorId,
                'conductor_id' => $conductorId,
                'total_job_orders' => $jobOrders->count(),
                'total_amount' => $totalAmount,
                'paid_amount' => $request->paid_amount,
                'balance_amount' => $totalAmount - $request->paid_amount,
                'remarks' => $request->remarks,
            ]);

            // Attach Job Orders (via pivot)
            $jos->jobOrders()->attach($jobOrderIds);

            // Update job_orders table to mark them as assigned
            JobOrder::whereIn('id', $jobOrderIds)->update(['job_order_statement_id' => $jos->id]);

            DB::commit();

            return $this->sendResponse([
                'id' => $jos->hashed_id,
                'message' => 'Job Order Statement created successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to create Job Order Statement.', 500);
        }
    } */



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = Hashids::decode($id) ?? null;
        $jos = JobOrderStatement::with(['contractor', 'conductor', 'jobOrders.typeOfWork'])->findOrFail($id);
        return $this->sendResponse(['success' => true, 'data' => $jos]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Group Job Orders by contractor, conductor, and jos_date (month)
     */
    public function groupedJobOrders(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));

        $query = JobOrder::whereNull('job_order_statement_id')
            ->whereMonth('jos_date', Carbon::parse($month)->month)
            ->whereYear('jos_date', Carbon::parse($month)->year)
            ->with(['contractor', 'conductor', 'typeOfWork']);

        $grouped = $query->get()->groupBy(function ($jo) {
            return $jo->contractor_id . '-' . $jo->conductor_id;
        });

        $result = [];

        foreach ($grouped as $key => $orders) {
            $totalAmount = $orders->sum(function ($jo) {
                return $jo->actual_work_completed * ($jo->typeOfWork->rate ?? 0);
            });

            $result[] = [
                'contractor_id' => Hashids::encode($orders->first()->contractor_id),
                'contractor_name' => $orders->first()->contractor->name,
                'conductor_id' => Hashids::encode($orders->first()->conductor_id),
                'conductor_name' => $orders->first()->conductor->full_name,
                'month' => $month,
                'job_order_count' => $orders->count(),
                'total_amount' => round($totalAmount, 2),
                'job_orders' => JobOrderResource::collection($orders),
            ];
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function jobOrders($id)
    {
        $jos = JobOrderStatement::with(['jobOrders.typeOfWork'])->findOrFail(Hashids::decode($id)[0]);

        $data = $jos->jobOrders->map(function ($jo) {
            return [
                'id' => $jo->hashed_id,
                'name' => $jo->name,
                'date' => $jo->date,
                'actual_work_completed' => $jo->actual_work_completed,
                'type_of_work' => $jo->typeOfWork->name,
                'type_of_work_rate' => $jo->typeOfWork->rate, // Assuming rate field exists
            ];
        });

        return $this->sendResponse($data, 'Associated Job Orders retrieved successfully.');
    }


}
