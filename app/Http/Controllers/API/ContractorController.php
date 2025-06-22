<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreContractorRequest;
use App\Http\Requests\UpdateContractorRequest;
use App\Http\Controllers\API\BaseController;
use App\Models\User;
use App\Models\Contractor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Vinkla\Hashids\Facades\Hashids;

class ContractorController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contractors = Contractor::whereNull('deleted_at')
            ->whereHas('user', fn($q) => $q->whereNull('deleted_at'))
            ->with('user')
            ->get()
            ->map(function ($contractor) {
                return [
                    'id' => Hashids::encode($contractor->id),
                    'name' => $contractor->name,
                    'code' => $contractor->code,
                    'email' => $contractor->email,
                    'phone_number' => $contractor->phone_number,
                    'company_name' => $contractor->company_name,
                    'balance' => $contractor->balance,
                ];
            });

        return $this->sendResponse($contractors, 'Contractors retrieved successfully');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('contractors.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContractorRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = User::where('email', $request->email)->first();

            if ($user) {
                // Already has a contractor record?
                if (Contractor::where('user_id', $user->id)->exists()) {
                    return $this->sendError(['error' => 'A contractor with this email already exists.'], 422);
                }
            } else {
                // New user
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make('password'),
                    'role' => 'contractor',
                ]);
            }

            // Ensure user has the contractor role
            if (!$user->hasRole('contractor')) {
                $user->assignRole('contractor');
            }

            $contractor = Contractor::create([
                'name' => $request->name,
                'code' => $request->code,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'company_name' => $request->company_name,
                'balance' => $request->balance,
                'user_id' => $user->id,
            ]);

            DB::commit();
            return $this->sendResponse($contractor, 'Contractor created successfully');
        } catch (\Exception $e) { dd($e);
            DB::rollBack();
            return response()->json(['error' => 'Failed to create contractor'], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Contractor $contractor, $hashedId)
    {
        $id = Hashids::decode($hashedId)[0] ?? null;
        if (!$id) return $this->sendError(['error' => 'Invalid ID'], 404);

        $contractor = Contractor::whereNull('deleted_at')
            ->whereHas('user', fn($q) => $q->whereNull('deleted_at'))
            ->findOrFail($id);

        return $this->sendResponse($contractor, 'Contractor retrieved successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contractor $contractor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractorRequest $request, $hashedId)
    {
        $id = Hashids::decode($hashedId)[0] ?? null;
        if (!$id) return $this->sendError(['error' => 'Invalid ID'], 404);

        DB::beginTransaction();
        try {
            $contractor = Contractor::whereNull('deleted_at')
                ->whereHas('user', fn($q) => $q->whereNull('deleted_at'))
                ->findOrFail($id);

            $contractor->update([
                'name' => $request->name,
                'code' => $contractor->code, //keep existing
                'phone_number' => $contractor->phone_number,
                'company_name' => $request->company_name,
                'balance' => $request->balance,
            ]);

            $contractor->user->name = $request->name;
            $contractor->user->save();

            DB::commit();
            return $this->sendResponse($contractor, 'Contractor updated successfully');
        } catch (\Exception $e) {dd($e);
            DB::rollBack();
            return $this->sendError(['error' => 'Failed to update contractor'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($hashedId)
    {
        $id = Hashids::decode($hashedId)[0] ?? null;
        if (!$id) return $this->sendError(['error' => 'Invalid ID'], 404);

        $contractor = Contractor::with('user')->findOrFail($id);

        $contractor->user->delete();
        $contractor->delete();
        return $this->sendResponse([], 'Contractor deleted successfully');
    }
}
