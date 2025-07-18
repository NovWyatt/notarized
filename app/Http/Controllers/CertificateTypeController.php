<?php

// app/Http/Controllers/CertificateTypeController.php
namespace App\Http\Controllers;

use App\Models\CertificateType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CertificateTypeController extends Controller
{
    /**
     * Search certificate types
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $certificateTypes = CertificateType::where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'description']);

        return response()->json($certificateTypes);
    }

    /**
     * Store a new certificate type
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:certificate_types,name',
            'description' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $certificateType = CertificateType::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo loại chứng chỉ thành công!',
                'certificateType' => $certificateType
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo loại chứng chỉ: ' . $e->getMessage()
            ], 500);
        }
    }
}
