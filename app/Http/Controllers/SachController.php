<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\TacGia;
use App\Models\TheLoai;
use App\Models\NhaXuatBan;
use App\Models\QuyDinh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SachController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Sach::with(['tacGia', 'theLoais', 'nhaXuatBan']);
            
            // Tìm kiếm theo tên sách hoặc tác giả
            if ($request->has('search') && $request->search) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('TenSach', 'LIKE', "%{$searchTerm}%")
                      ->orWhereHas('tacGia', function ($tacGiaQuery) use ($searchTerm) {
                          $tacGiaQuery->where('TenTacGia', 'LIKE', "%{$searchTerm}%");
                      });
                });
            }
            
            // Lọc theo thể loại  
            if ($request->has('genre') && $request->genre && $request->genre !== 'all') {
                $query->whereHas('theLoais', function ($theLoaiQuery) use ($request) {
                    $theLoaiQuery->where('id', $request->genre);
                });
            }
            
            $danhSachSach = $query->get();
        } catch (\Exception $e) {
            // Nếu database chưa có dữ liệu, tạo collection rỗng
            $danhSachSach = collect([]);
        }
        
        // Thống kê
        try {
            $totalBooks = Sach::count();
            $totalGenres = TheLoai::count();
            $totalAuthors = TacGia::count();
            $totalProblemBooks = Sach::whereIn('TinhTrang', [Sach::TINH_TRANG_HONG, Sach::TINH_TRANG_BI_MAT])->count();
        } catch (\Exception $e) {
            $totalBooks = 0;
            $totalGenres = 0;
            $totalAuthors = 0;
            $totalProblemBooks = 0;
        }
        
        // Danh sách thể loại cho filter
        try {
            $genresForFilter = TheLoai::all();
            $tacGias = TacGia::all();
            $theLoais = TheLoai::all();
            $nhaXuatBans = NhaXuatBan::all();
        } catch (\Exception $e) {
            $genresForFilter = collect([]);
            $tacGias = collect([]);
            $theLoais = collect([]);
            $nhaXuatBans = collect([]);
        }
        
        return view('books', compact(
            'danhSachSach', 
            'totalBooks', 
            'totalGenres', 
            'totalAuthors', 
            'totalProblemBooks',
            'genresForFilter',
            'tacGias',
            'theLoais', 
            'nhaXuatBans'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $currentYear = date('Y');
        $bookPublicationYears = QuyDinh::getBookPublicationYears();
        $minPublicationYear = $currentYear - $bookPublicationYears;
        
        $request->validate([
            'TenSach' => 'required|string|max:255',
            'NamXuatBan' => [
                'required',
                'integer',
                'min:' . $minPublicationYear,
                'max:' . $currentYear
            ],
            'TriGia' => 'required|numeric|min:0|max:999999999.99',
            'SoLuong' => 'required|integer|min:1|max:1000',
            'tacGias' => 'required|integer|exists:TACGIA,id',
            'theLoais' => 'required|string',
            'nhaXuatBans' => 'required|integer|exists:NHAXUATBAN,id',
        ], [
            'TenSach.required' => 'Tên sách là bắt buộc',
            'NamXuatBan.required' => 'Năm xuất bản là bắt buộc',
            'NamXuatBan.integer' => 'Năm xuất bản phải là số',
            'NamXuatBan.min' => "Chỉ nhận sách xuất bản trong vòng {$bookPublicationYears} năm (từ {$minPublicationYear} trở lại đây)",
            'NamXuatBan.max' => 'Năm xuất bản không được lớn hơn năm hiện tại',
            'TriGia.required' => 'Trị giá sách là bắt buộc',
            'TriGia.numeric' => 'Trị giá sách phải là số',
            'TriGia.min' => 'Trị giá sách phải lớn hơn hoặc bằng 0',
            'TriGia.max' => 'Trị giá sách quá lớn',
            'SoLuong.required' => 'Số lượng là bắt buộc',
            'SoLuong.integer' => 'Số lượng phải là số nguyên',
            'SoLuong.min' => 'Số lượng phải ít nhất là 1',
            'SoLuong.max' => 'Số lượng không được quá 1000',
            'tacGias.required' => 'Phải chọn tác giả',
            'tacGias.integer' => 'Tác giả không hợp lệ',
            'tacGias.exists' => 'Tác giả không tồn tại',
            'theLoais.required' => 'Phải chọn ít nhất 1 thể loại',
            'nhaXuatBans.required' => 'Phải chọn nhà xuất bản',
            'nhaXuatBans.integer' => 'Nhà xuất bản không hợp lệ',
            'nhaXuatBans.exists' => 'Nhà xuất bản không tồn tại',
        ]);

        try {
            DB::beginTransaction();
            
            $createdBooks = [];
            $soLuong = (int)$request->SoLuong;
            
            // Parse thể loại từ JSON string
            $theLoaiIds = json_decode($request->theLoais, true);
            
            if (!is_array($theLoaiIds) || empty($theLoaiIds)) {
                \Log::error('Invalid theLoaiIds:', [
                    'is_array' => is_array($theLoaiIds),
                    'empty' => empty($theLoaiIds),
                    'value' => $theLoaiIds
                ]);
                throw new \Exception('Phải chọn ít nhất 1 thể loại');
            }
            
            // Tạo nhiều sách theo số lượng
            for ($i = 0; $i < $soLuong; $i++) {
                // Tạo mã sách tự động
                $maSach = $this->generateMaSach();
                
                $sach = Sach::create([
                    'MaSach' => $maSach,
                    'TenSach' => $request->TenSach,
                    'NamXuatBan' => $request->NamXuatBan,
                    'NgayNhap' => Carbon::now(),
                    'TriGia' => $request->TriGia,
                    'TinhTrang' => Sach::TINH_TRANG_CO_SAN, // Mặc định là có sẵn
                    'MaTacGia' => $request->tacGias,
                    'MaNhaXuatBan' => $request->nhaXuatBans,
                ]);
                
                // Gắn nhiều thể loại (many-to-many)
                $sach->theLoais()->attach($theLoaiIds);
                
                $createdBooks[] = $sach;
            }
            
            DB::commit();
            
            return redirect()->route('books.index')->with('success', 
                "Đã thêm thành công {$soLuong} sách với mã: " . 
                implode(', ', array_map(function($book) { return $book->MaSach; }, $createdBooks))
            );
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique book code
     */
    private function generateMaSach()
    {
        $prefix = 'S';
        $year = date('Y');
        
        // Lấy số thứ tự cao nhất trong năm hiện tại
        $latestBook = Sach::where('MaSach', 'LIKE', $prefix . $year . '%')
                         ->orderBy('MaSach', 'desc')
                         ->first();
        
        if ($latestBook) {
            // Lấy số thứ tự từ mã sách cuối cùng
            $lastNumber = (int)substr($latestBook->MaSach, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        // Format: S2025-0001, S2025-0002, ...
        return $prefix . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sach = Sach::with(['tacGia', 'theLoais', 'nhaXuatBan'])->findOrFail($id);
        
        return response()->json([
            'id' => $sach->id,
            'TenSach' => $sach->TenSach,
            'NamXuatBan' => $sach->NamXuatBan,
            'TriGia' => $sach->TriGia,
            'TinhTrang' => $sach->TinhTrang,
            'tacGias' => $sach->tacGia ? $sach->tacGia->id : null,
            'theLoais' => $sach->theLoais->pluck('id')->toArray(),
            'nhaXuatBans' => $sach->nhaXuatBan ? $sach->nhaXuatBan->id : null,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sach = Sach::findOrFail($id);
        $currentYear = date('Y');
        $bookPublicationYears = QuyDinh::getBookPublicationYears();
        $minPublicationYear = $currentYear - $bookPublicationYears;
        
        $request->validate([
            'TenSach' => 'required|string|max:255',
            'NamXuatBan' => [
                'required',
                'integer',
                'min:' . $minPublicationYear,
                'max:' . $currentYear
            ],
            'TriGia' => 'required|numeric|min:0|max:999999999.99',
            'tacGias' => 'required|integer|exists:TACGIA,id',
            'theLoais' => 'required|string',
            'nhaXuatBans' => 'required|integer|exists:NHAXUATBAN,id',
        ], [
            'TenSach.required' => 'Tên sách là bắt buộc',
            'NamXuatBan.required' => 'Năm xuất bản là bắt buộc',
            'NamXuatBan.integer' => 'Năm xuất bản phải là số',
            'NamXuatBan.min' => "Chỉ nhận sách xuất bản trong vòng {$bookPublicationYears} năm (từ {$minPublicationYear} trở lại đây)",
            'NamXuatBan.max' => 'Năm xuất bản không được lớn hơn năm hiện tại',
            'TriGia.required' => 'Trị giá sách là bắt buộc',
            'TriGia.numeric' => 'Trị giá sách phải là số',
            'TriGia.min' => 'Trị giá sách phải lớn hơn hoặc bằng 0',
            'TriGia.max' => 'Trị giá sách quá lớn',
            'tacGias.required' => 'Phải chọn tác giả',
            'tacGias.integer' => 'Tác giả không hợp lệ',
            'tacGias.exists' => 'Tác giả không tồn tại',
            'theLoais.required' => 'Phải chọn ít nhất 1 thể loại',
            'nhaXuatBans.required' => 'Phải chọn nhà xuất bản',
            'nhaXuatBans.integer' => 'Nhà xuất bản không hợp lệ',
            'nhaXuatBans.exists' => 'Nhà xuất bản không tồn tại',
        ]);

        try {
            DB::beginTransaction();
            
            // Parse thể loại từ JSON string
            $theLoaiIds = json_decode($request->theLoais, true);
            if (!is_array($theLoaiIds) || empty($theLoaiIds)) {
                throw new \Exception('Phải chọn ít nhất 1 thể loại');
            }
            
            $sach->update([
                'TenSach' => $request->TenSach,
                'NamXuatBan' => $request->NamXuatBan,
                'TriGia' => $request->TriGia,
                'MaTacGia' => $request->tacGias,
                'MaNhaXuatBan' => $request->nhaXuatBans,
                'TinhTrang' => $request->TinhTrang, // Cho phép cập nhật tình trạng sách
            ]);
            
            // Cập nhật nhiều thể loại (many-to-many)
            $sach->theLoais()->sync($theLoaiIds);
            
            DB::commit();
            return redirect()->route('books.index')->with('success', 'Cập nhật sách thành công!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('books.index')->with('error', 'Có lỗi xảy ra khi cập nhật sách: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            $sach = Sach::findOrFail($id);
            
            // Kiểm tra xem sách có bị hỏng hoặc mất không
            if ($sach->isDamaged()) {
                DB::rollback();
                $errorMessage = 'Không thể xóa sách đã hỏng! Sách này cần được xử lý riêng.';
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }
                
                return redirect()->route('books.index')->with('error', $errorMessage);
            }
            
            if ($sach->isLost()) {
                DB::rollback();
                $errorMessage = 'Không thể xóa sách đã mất! Sách này cần được xử lý riêng.';
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }
                
                return redirect()->route('books.index')->with('error', $errorMessage);
            }
            
            // Nếu sách đang được mượn, cập nhật tình trạng về "Có sẵn" trước khi xóa
            if ($sach->isBorrowed()) {
                $sach->markAsAvailable();
            }
            
            // Kiểm tra xem sách có trong phiếu mượn nào không (kể cả đã trả)
            $hasActiveLoans = DB::table('CHITIETPHIEUMUON')
                ->where('sach_id', $id)
                ->exists();
                
            if ($hasActiveLoans) {
                DB::rollback();
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể xóa sách đã từng được mượn! Sách này có lịch sử mượn trả.'
                    ], 400);
                }
                
                return redirect()->route('books.index')->with('error', 'Không thể xóa sách đã từng được mượn! Sách này có lịch sử mượn trả.');
            }
            
            // Chỉ cần xóa quan hệ many-to-many với thể loại
            $sach->theLoais()->detach();
            
            // Xóa sách (các quan hệ one-to-many tự động null)
            $sach->delete();
            
            DB::commit();
            
            $successMessage = 'Xóa sách thành công!';
            if ($sach->isBorrowed()) {
                $successMessage = 'Xóa sách thành công! Tình trạng sách đã được cập nhật về "Có sẵn" trước khi xóa.';
            }
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage
                ]);
            }
            
            return redirect()->route('books.index')->with('success', $successMessage);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xóa sách: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('books.index')->with('error', 'Có lỗi xảy ra khi xóa sách: ' . $e->getMessage());
        }
    }
}
