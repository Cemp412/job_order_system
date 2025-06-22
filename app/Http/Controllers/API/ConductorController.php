<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreConductorRequest;
use App\Http\Requests\UpdateConductorRequest;
use App\Http\Controllers\API\BaseController;
use App\Models\User;
use App\Models\Conductor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Vinkla\Hashids\Facades\Hashids;


class ConductorController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $conductors = Conductor::whereNull('deleted_at')
            ->with('user')
            ->get()
            ->map(function ($conductor) {
                return [
                    'id' => $conductor->hashed_id,
                    'first_name' => $conductor->first_name,
                    'middle_name' => $conductor->middle_name,
                    'last_name' => $conductor->last_name,
                    'staff_id' => $conductor->staff_id,
                    'email' => $conductor->user->email ?? null,
                    'phone_number' => $conductor->phone_number,
                    'department_name' => $conductor->department_name,
                ];
            });

        return $this->sendResponse($conductors, 'Conductors retrieved successfully');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('conductors.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConductorRequest $request)
    { 
        DB::beginTransaction();

        try {
            $user = User::where('email', $request->email)->first();

            if ($user && $user->hasRole('conductor')) {
                return $this->sendError('This email already belongs to a conductor.');
            }

            if (!$user) {
                $user = User::create([
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'conductor',
                ]);

                $user->assignRole('conductor');
            }

            $conductor = Conductor::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'staff_id' => $request->staff_id,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'department_name' => $request->department_name,
            ]);

            DB::commit();
            return $this->sendResponse($conductor, 'Conductor created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Failed to create conductor.', [$e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Conductor $conductor, $hashedId)
    {
        $id = Hashids::decode($hashedId)[0] ?? null;
        if (!$id) return $this->sendError(['error' => 'Invalid ID'], 404);

        $conductor = Conductor::whereNull('deleted_at')
            ->whereHas('user', fn($q) => $q->whereNull('deleted_at'))
            ->findOrFail($id);

        return $this->sendResponse($conductor, 'Conductor retrieved successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Conductor $conductor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConductorRequest $request, $hashedId)
    {
        $id = Hashids::decode($hashedId)[0] ?? null;
        if (!$id) return $this->sendError(['error' => 'Invalid ID'], 404);

        DB::beginTransaction();
        try {
            $conductor = Conductor::whereNull('deleted_at')
                ->whereHas('user', fn($q) => $q->whereNull('deleted_at'))
                ->findOrFail($id);

            $conductor->update([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'staff_id' => $conductor->staff_id, // keep existing
                'phone_number' => $conductor->phone_number, // keep existing
                'department_name' => $request->department_name,
            ]);

            // Update user name
            $conductor->user->name = $request->first_name . ' ' . $request->last_name;

            // Optional: Update password if provided
            // if ($request->filled('password')) {
            //     $conductor->user->password = \Hash::make($request->password);
            // }

            $conductor->user->save();

            DB::commit();
            return $this->sendResponse($conductor, 'Conductor updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(['error' => 'Failed to update conductor'], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($hashedId)
    {
        $id = Hashids::decode($hashedId)[0] ?? null;
        if (!$id) return $this->sendError(['error' => 'Invalid ID'], 404);

        $conductor = Conductor::with('user')->findOrFail($id);

        $conductor->user->delete();
        $conductor->delete();
        return $this->sendResponse([], 'Conductor deleted successfully');
    }
}
