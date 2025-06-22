<?php

namespace App\Http\Controllers\API;

use App\Models\TypeOfWork;
use App\Http\Requests\StoreTypeOfWorkRequest;
use App\Http\Requests\UpdateTypeOfWorkRequest;
use App\Http\Controllers\API\BaseController;
use Vinkla\Hashids\Facades\Hashids;


class TypeOfWorkController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $typeOfWorks = TypeOfWork::all()->map(function ($item) {
            return [
                'id' => $item->hashed_id, // hashed ID
                'name' => $item->name,
                'rate' => $item->rate,
                'code' => $item->code,
            ];
        });
        return $this->sendResponse($typeOfWorks, 'Type of works retrieved successfully');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('type_of_works.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTypeOfWorkRequest $request)
    {
        $typeOfWork = TypeOfWork::create($request->validated());
        return $this->sendResponse($typeOfWork, 'Type of work created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(TypeOfWork $typeOfWork)
    {
        return $this->sendResponse($typeOfWork, 'Type of work retrieved successfully.');

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeOfWork $typeOfWork)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTypeOfWorkRequest $request, $hashedId)
    {
        $id = Hashids::decode($hashedId)[0] ?? null;

        if (!$id) {
            return $this->sendError(['error' => 'Invalid ID'], 404);
        }

        $typeOfWork = TypeOfWork::findOrFail($id);

        $typeOfWork->update($request->validated());

        return $this->sendResponse($typeOfWork, 'Type of work updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeOfWork $typeOfWork, $hashedId)
    {
        $id = Hashids::decode($hashedId)[0] ?? null;

        if (!$id) {
            return $this->sendError(['error' => 'Invalid ID'], 404);
        }

        $typeOfWork = TypeOfWork::findOrFail($id);

        $typeOfWork->delete();
        return $this->sendResponse([], 'Type of work deleted successfully');
    }
}
