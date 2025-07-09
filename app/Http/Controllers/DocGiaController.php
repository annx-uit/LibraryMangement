<?php

namespace App\Http\Controllers;

use App\Models\DocGia;
use App\Models\LoaiDocGia;
use App\Models\QuyDinh;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DocGiaController extends Controller
{
    public function index(Request $request)
    {
        $query = DocGia::with('loaiDocGia');

        // Since we removed filters from the frontend, we'll get all readers
        // but still support search if needed for future use
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('HoTen', 'like', "%{$search}%")
                  ->orWhere('Email', 'like', "%{$search}%")
                  ->orWhere('DiaChi', 'like', "%{$search}%");
            });
        }

        // Default ordering
        $query->orderBy('HoTen', 'asc');

        // Get all readers (no pagination since we use client-side search)
        $docGias = $query->get();
        $loaiDocGias = LoaiDocGia::orderBy('TenLoaiDocGia')->get();

        // Nếu là AJAX request, trả về JSON
        if ($request->expectsJson()) {
            return response()->json([
                'docGias' => $docGias,
                'loaiDocGias' => $loaiDocGias
            ]);
        }

        return view('readers', compact('docGias', 'loaiDocGias'));
    }

    public function store(Request $request)
    {
        try {
        $request->validate([
            'HoTen' => 'required|string|max:255',
            'loaidocgia_id' => 'required|exists:LOAIDOCGIA,id',
            'NgaySinh' => [
                'required',
                'date',
                'before:today',
                function ($attribute, $value, $fail) {
                    $birthDate = Carbon::parse($value);
                    $age = $birthDate->diffInYears(Carbon::now());
                    
                    $minAge = QuyDinh::getMinAge();
                    $maxAge = QuyDinh::getMaxAge();
                    
                    if ($age < $minAge) {
                        $fail("Độc giả phải từ {$minAge} tuổi trở lên (hiện tại: {$age} tuổi)");
                    }
                    
                    if ($age > $maxAge) {
                        $fail("Độc giả không được quá {$maxAge} tuổi (hiện tại: {$age} tuổi)");
                    }
                }
            ],
            'DiaChi' => 'required|string|max:500',
            'Email' => 'required|email|unique:DOCGIA,Email',
            'NgayLapThe' => 'required|date',
            'NgayHetHan' => 'required|date|after:NgayLapThe',
            'TongNo' => 'nullable|integer|min:0|max:999999999',
        ], [
            'HoTen.required' => 'Họ tên là bắt buộc',
            'HoTen.max' => 'Họ tên không được quá 255 ký tự',
            'loaidocgia_id.required' => 'Loại độc giả là bắt buộc',
            'loaidocgia_id.exists' => 'Loại độc giả không hợp lệ',
            'NgaySinh.required' => 'Ngày sinh là bắt buộc',
            'NgaySinh.date' => 'Ngày sinh không hợp lệ',
            'NgaySinh.before' => 'Ngày sinh phải trước ngày hôm nay',
            'DiaChi.required' => 'Địa chỉ là bắt buộc',
            'DiaChi.max' => 'Địa chỉ không được quá 500 ký tự',
            'Email.required' => 'Email là bắt buộc',
            'Email.email' => 'Email không hợp lệ',
            'Email.unique' => 'Email đã tồn tại',
            'NgayLapThe.required' => 'Ngày lập thẻ là bắt buộc',
            'NgayLapThe.date' => 'Ngày lập thẻ không hợp lệ',
            'NgayHetHan.required' => 'Ngày hết hạn là bắt buộc',
            'NgayHetHan.date' => 'Ngày hết hạn không hợp lệ',
            'NgayHetHan.after' => 'Ngày hết hạn phải sau ngày lập thẻ',
            'TongNo.integer' => 'Tổng nợ phải là số nguyên',
            'TongNo.min' => 'Tổng nợ không được âm',
            'TongNo.max' => 'Tổng nợ quá lớn',
        ]);

            $docGia = DocGia::create([
                'HoTen' => $request->HoTen,
                'loaidocgia_id' => $request->loaidocgia_id,
                'NgaySinh' => $request->NgaySinh,
                'DiaChi' => $request->DiaChi,
                'Email' => $request->Email,
                'NgayLapThe' => $request->NgayLapThe,
                'NgayHetHan' => $request->NgayHetHan,
                'TongNo' => $request->TongNo ?? 0,
            ]);

            // Load relationship
            $docGia->load('loaiDocGia');

            // Check if it's an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thêm độc giả thành công',
                    'data' => $docGia
                ]);
            }

            // Traditional form submission - redirect with success message
            return redirect()->route('readers.index')->with('success', 'Thêm độc giả thành công');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                // Lấy thông báo lỗi đầu tiên từ validation errors
                $firstError = collect($e->errors())->first();
                $errorMessage = is_array($firstError) ? $firstError[0] : $firstError;
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage ?? 'Lỗi khi lưu độc giả',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('readers.index')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
        $docGia = DocGia::findOrFail($id);

        $request->validate([
            'HoTen' => 'required|string|max:255',
            'loaidocgia_id' => 'required|exists:LOAIDOCGIA,id',
            'NgaySinh' => [
                'required',
                'date',
                'before:today',
                function ($attribute, $value, $fail) {
                    $birthDate = Carbon::parse($value);
                    $age = $birthDate->diffInYears(Carbon::now());
                    
                    $minAge = QuyDinh::getMinAge();
                    $maxAge = QuyDinh::getMaxAge();
                    
                    if ($age < $minAge) {
                        $fail("Độc giả phải từ {$minAge} tuổi trở lên (hiện tại: {$age} tuổi)");
                    }
                    
                    if ($age > $maxAge) {
                        $fail("Độc giả không được quá {$maxAge} tuổi (hiện tại: {$age} tuổi)");
                    }
                }
            ],
            'DiaChi' => 'required|string|max:500',
            'Email' => 'required|email|unique:DOCGIA,Email,' . $id,
            'NgayLapThe' => 'required|date',
            'NgayHetHan' => 'required|date|after:NgayLapThe',
            'TongNo' => 'nullable|integer|min:0|max:999999999',
        ], [
            'HoTen.required' => 'Họ tên là bắt buộc',
            'HoTen.max' => 'Họ tên không được quá 255 ký tự',
            'loaidocgia_id.required' => 'Loại độc giả là bắt buộc',
            'loaidocgia_id.exists' => 'Loại độc giả không hợp lệ',
            'NgaySinh.required' => 'Ngày sinh là bắt buộc',
            'NgaySinh.date' => 'Ngày sinh không hợp lệ',
            'NgaySinh.before' => 'Ngày sinh phải trước ngày hôm nay',
            'DiaChi.required' => 'Địa chỉ là bắt buộc',
            'DiaChi.max' => 'Địa chỉ không được quá 500 ký tự',
            'Email.required' => 'Email là bắt buộc',
            'Email.email' => 'Email không hợp lệ',
            'Email.unique' => 'Email đã tồn tại',
            'NgayLapThe.required' => 'Ngày lập thẻ là bắt buộc',
            'NgayLapThe.date' => 'Ngày lập thẻ không hợp lệ',
            'NgayHetHan.required' => 'Ngày hết hạn là bắt buộc',
            'NgayHetHan.date' => 'Ngày hết hạn không hợp lệ',
            'NgayHetHan.after' => 'Ngày hết hạn phải sau ngày lập thẻ',
            'TongNo.integer' => 'Tổng nợ phải là số nguyên',
            'TongNo.min' => 'Tổng nợ không được âm',
            'TongNo.max' => 'Tổng nợ quá lớn',
        ]);

            $docGia->update([
                'HoTen' => $request->HoTen,
                'loaidocgia_id' => $request->loaidocgia_id,
                'NgaySinh' => $request->NgaySinh,
                'DiaChi' => $request->DiaChi,
                'Email' => $request->Email,
                'NgayLapThe' => $request->NgayLapThe,
                'NgayHetHan' => $request->NgayHetHan,
                'TongNo' => $request->TongNo ?? 0,
            ]);

            // Load relationship
            $docGia->load('loaiDocGia');

            // Check if it's an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật độc giả thành công',
                    'data' => $docGia
                ]);
            }

            // Traditional form submission - redirect with success message
            return redirect()->route('readers.index')->with('success', 'Cập nhật độc giả thành công');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                // Lấy thông báo lỗi đầu tiên từ validation errors
                $firstError = collect($e->errors())->first();
                $errorMessage = is_array($firstError) ? $firstError[0] : $firstError;
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage ?? 'Lỗi khi cập nhật độc giả',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('readers.index')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $docGia = DocGia::findOrFail($id);
            
            // Check if reader has any active borrows
            $activeBorrows = \App\Models\PhieuMuon::where('docgia_id', $docGia->id)
                ->whereHas('chiTietPhieuMuon', function($query) {
                    $query->whereNull('NgayTra');
                })->count();
            
            if ($activeBorrows > 0) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể xóa độc giả còn sách đang mượn'
                    ], 400);
                }
                
                return redirect()->route('readers.index')->with('error', 'Không thể xóa độc giả còn sách đang mượn');
            }

            $docGia->delete();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xóa độc giả thành công'
                ]);
            }

            return redirect()->route('readers.index')->with('success', 'Xóa độc giả thành công');
            
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('readers.index')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $docGia = DocGia::with('loaiDocGia')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $docGia
        ]);
    }
}
