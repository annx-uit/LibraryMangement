<?php

namespace App\Http\Controllers;

use App\Models\QuyDinh;
use Illuminate\Http\Request;

class QuyDinhController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $quyDinhs = QuyDinh::orderBy('TenQuyDinh')->get();

        // Nếu là AJAX request, trả về JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $quyDinhs
            ]);
        }

        return view('regulations', compact('quyDinhs'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $quyDinh = QuyDinh::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $quyDinh
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy quy định'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $quyDinh = QuyDinh::findOrFail($id);

        $request->validate([
            'GiaTri' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) use ($quyDinh) {
                    // Validation dựa trên loại quy định
                    $tenQuyDinh = $quyDinh->TenQuyDinh;
                    
                    if (str_contains($tenQuyDinh, 'tuổi')) {
                        if ($value < 1 || $value > 100) {
                            $fail('Tuổi phải từ 1 đến 100');
                        }
                    } elseif (str_contains($tenQuyDinh, 'tháng')) {
                        if ($value < 1 || $value > 120) {
                            $fail('Số tháng phải từ 1 đến 120');
                        }
                    } elseif (str_contains($tenQuyDinh, 'ngày')) {
                        if ($value < 1 || $value > 365) {
                            $fail('Số ngày phải từ 1 đến 365');
                        }
                    } elseif (str_contains($tenQuyDinh, 'sách')) {
                        if ($value < 1 || $value > 50) {
                            $fail('Số sách phải từ 1 đến 50');
                        }
                    } elseif (str_contains($tenQuyDinh, 'năm')) {
                        if ($value < 1 || $value > 50) {
                            $fail('Số năm phải từ 1 đến 50');
                        }
                    }
                }
            ]
        ], [
            'GiaTri.required' => 'Giá trị quy định là bắt buộc',
            'GiaTri.numeric' => 'Giá trị phải là số',
            'GiaTri.min' => 'Giá trị phải lớn hơn 0',
        ]);

        try {
            $quyDinh->update([
                'GiaTri' => $request->GiaTri,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật quy định thành công',
                    'data' => $quyDinh->fresh()
                ]);
            }

            return redirect()->route('regulations.index')->with('success', 'Cập nhật quy định thành công');
            
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('regulations.index')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Get validation info for frontend
     */
    public function getValidationInfo($id)
    {
        $quyDinh = QuyDinh::findOrFail($id);
        $tenQuyDinh = $quyDinh->TenQuyDinh;
        
        $validationInfo = [
            'min' => 1,
            'max' => 100,
            'unit' => '',
            'description' => ''
        ];

        if (str_contains($tenQuyDinh, 'tuổi')) {
            $validationInfo['max'] = 100;
            $validationInfo['unit'] = 'tuổi';
            $validationInfo['description'] = 'Độ tuổi hợp lệ (1-100)';
        } elseif (str_contains($tenQuyDinh, 'tháng')) {
            $validationInfo['max'] = 120;
            $validationInfo['unit'] = 'tháng';
            $validationInfo['description'] = 'Số tháng hợp lệ (1-120)';
        } elseif (str_contains($tenQuyDinh, 'ngày')) {
            $validationInfo['max'] = 365;
            $validationInfo['unit'] = 'ngày';
            $validationInfo['description'] = 'Số ngày hợp lệ (1-365)';
        } elseif (str_contains($tenQuyDinh, 'sách')) {
            $validationInfo['max'] = 50;
            $validationInfo['unit'] = 'cuốn';
            $validationInfo['description'] = 'Số sách hợp lệ (1-50)';
        } elseif (str_contains($tenQuyDinh, 'năm')) {
            $validationInfo['max'] = 50;
            $validationInfo['unit'] = 'năm';
            $validationInfo['description'] = 'Số năm hợp lệ (1-50)';
        }

        return response()->json([
            'success' => true,
            'data' => $validationInfo
        ]);
    }
}
