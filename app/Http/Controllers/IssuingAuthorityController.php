<?php

// app/Http/Controllers/IssuingAuthorityController.php
namespace App\Http\Controllers;

use App\Models\IssuingAuthority;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IssuingAuthorityController extends Controller
{
    /**
     * Search issuing authorities
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $authorities = IssuingAuthority::where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'address']);

        // Format for display
        $formatted = $authorities->map(function($authority) {
            return [
                'id' => $authority->id,
                'name' => $authority->name,
                'description' => $authority->address ? "Địa chỉ: {$authority->address}" : null
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Store a new issuing authority
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:issuing_authorities,name',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $authority = IssuingAuthority::create([
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo cơ quan cấp phát thành công!',
                'issuingAuthority' => $authority
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo cơ quan cấp phát: ' . $e->getMessage()
            ], 500);
        }
    }
}
