<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest('Created_At')->paginate(15);

        return CustomerResource::collection($customers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Company_Name'  => 'required|string|max:255',
            'Company_Email' => 'required|email|unique:company,Company_Email',
            'Company_No'    => 'nullable|string|max:20',
            'Status'        => 'required|in:Active,Lead,Inactive',
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'success' => true,
            'data'    => new CustomerResource($customer),
            'message' => 'Customer created successfully',
        ], 201);
    }

    public function show($id)
    {
        $customer = Customer::find($id);

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        return new CustomerResource($customer);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $validated = $request->validate([
            'Company_Name'  => 'sometimes|required|string|max:255',
            'Company_Email' => 'sometimes|required|email|unique:company,Company_Email,' . $id . ',Company_ID',
            'Company_No'    => 'nullable|string|max:20',
            'Status'        => 'sometimes|required|in:Active,Lead,Inactive',
        ]);

        $customer->update($validated);

        return response()->json([
            'success' => true,
            'data'    => new CustomerResource($customer),
            'message' => 'Customer updated successfully',
        ]);
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
        ]);
    }
}