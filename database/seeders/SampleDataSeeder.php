<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoaiDocGia;
use App\Models\TacGia;
use App\Models\TheLoai;
use App\Models\NhaXuatBan;
use App\Models\QuyDinh;
use App\Models\DocGia;
use App\Models\Sach;
use App\Models\PhieuMuon;
use App\Models\ChiTietPhieuMuon;
use App\Models\PhieuThuTienPhat;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Bắt đầu tạo dữ liệu mẫu...\n";
        
        // 1. Tạo loại độc giả
        $this->createReaderTypes();
        
        // 2. Tạo tác giả
        $this->createAuthors();
        
        // 3. Tạo thể loại
        $this->createGenres();
        
        // 4. Tạo nhà xuất bản
        $this->createPublishers();
        
        // 5. Tạo quy định
        $this->createRegulations();
        
        // 6. Tạo độc giả
        $this->createReaders();
        
        // 7. Tạo sách
        $this->createBooks();
        
        // 8. Tạo phiếu mượn
        $this->createBorrowRecords();
        
        // 9. Tạo phiếu thu tiền phạt
        $this->createFineRecords();
        
        echo "Hoàn thành tạo dữ liệu mẫu!\n";
    }
    
    /**
     * Tạo loại độc giả
     */
    private function createReaderTypes()
    {
        echo "Tạo loại độc giả...\n";
        
        $types = [
            'Sinh viên',
            'Giảng viên',
            'Cán bộ nhân viên',
            'Độc giả bên ngoài',
            'Học sinh',
            'Nghiên cứu sinh'
        ];
        
        foreach ($types as $type) {
            LoaiDocGia::create(['TenLoaiDocGia' => $type]);
        }
    }
    
    /**
     * Tạo tác giả
     */
    private function createAuthors()
    {
        echo "Tạo tác giả...\n";
        
        $authors = [
            'Nguyễn Du',
            'Dale Carnegie',
            'Paulo Coelho',
            'Stephen Hawking',
            'Tô Hoài',
            'Nam Cao',
            'Vũ Trọng Phụng',
            'Thạch Lam',
            'Nguyễn Tuân',
            'Hồ Chí Minh'
        ];
        
        foreach ($authors as $author) {
            TacGia::create(['TenTacGia' => $author]);
        }
    }
    
    /**
     * Tạo thể loại
     */
    private function createGenres()
    {
        echo "Tạo thể loại...\n";
        
        $genres = [
            'Văn học',
            'Khoa học',
            'Thiếu nhi',
            'Kỹ năng sống',
            'Lịch sử',
            'Kinh tế',
            'Công nghệ',
            'Y học',
            'Nghệ thuật',
            'Du lịch'
        ];
        
        foreach ($genres as $genre) {
            TheLoai::create(['TenTheLoai' => $genre]);
        }
    }
    
    /**
     * Tạo nhà xuất bản
     */
    private function createPublishers()
    {
        echo "Tạo nhà xuất bản...\n";
        
        $publishers = [
            'NXB Trẻ',
            'NXB Kim Đồng',
            'NXB Văn học',
            'NXB Khoa học tự nhiên và Công nghệ',
            'NXB Giáo dục',
            'NXB Chính trị Quốc gia',
            'NXB Thông tin và Truyền thông',
            'NXB Lao động',
            'NXB Thanh niên',
            'NXB Phụ nữ'
        ];
        
        foreach ($publishers as $publisher) {
            NhaXuatBan::create(['TenNXB' => $publisher]);
        }
    }
    
    /**
     * Tạo tham số
     */
    private function createRegulations()
    {
        echo "Tạo tham số...\n";
        
        $parameters = [
            ['TenThamSo' => 'TuoiToiThieu', 'GiaTri' => '18'],
            ['TenThamSo' => 'TuoiToiDa', 'GiaTri' => '55'],
            ['TenThamSo' => 'ThoiHanThe', 'GiaTri' => '6'],
            ['TenThamSo' => 'SoSachToiDa', 'GiaTri' => '5'],
            ['TenThamSo' => 'NgayMuonToiDa', 'GiaTri' => '14'],
            ['TenThamSo' => 'SoNamXuatBan', 'GiaTri' => '8'],
        ];
        
        foreach ($parameters as $parameter) {
            QuyDinh::create($parameter);
        }
    }
    
    /**
     * Tạo độc giả
     */
    private function createReaders()
    {
        echo "Tạo độc giả...\n";
        
        $readers = [
            [
                'HoTen' => 'Nguyễn Văn An',
                'loaidocgia_id' => 1, // Sinh viên
                'NgaySinh' => '2000-05-15',
                'DiaChi' => 'Hà Nội',
                'Email' => 'an@email.com',
                'NgayLapThe' => '2024-01-15',
                'NgayHetHan' => '2024-07-15',
                'TongNo' => 15000
            ],
            [
                'HoTen' => 'Trần Thị Bình',
                'loaidocgia_id' => 1, // Sinh viên
                'NgaySinh' => '2001-08-20',
                'DiaChi' => 'TP.HCM',
                'Email' => 'binh@email.com',
                'NgayLapThe' => '2024-02-10',
                'NgayHetHan' => '2024-08-10',
                'TongNo' => 25000
            ],
            [
                'HoTen' => 'Lê Minh Châu',
                'loaidocgia_id' => 2, // Giảng viên
                'NgaySinh' => '1985-12-10',
                'DiaChi' => 'Đà Nẵng',
                'Email' => 'chau@email.com',
                'NgayLapThe' => '2024-03-05',
                'NgayHetHan' => '2024-09-05',
                'TongNo' => 0
            ],
            [
                'HoTen' => 'Phạm Văn Dũng',
                'loaidocgia_id' => 1, // Sinh viên
                'NgaySinh' => '2002-03-25',
                'DiaChi' => 'Hải Phòng',
                'Email' => 'dung@email.com',
                'NgayLapThe' => '2024-04-01',
                'NgayHetHan' => '2024-10-01',
                'TongNo' => 5000
            ],
            [
                'HoTen' => 'Hoàng Thị Em',
                'loaidocgia_id' => 3, // Cán bộ nhân viên
                'NgaySinh' => '1990-07-18',
                'DiaChi' => 'Cần Thơ',
                'Email' => 'em@email.com',
                'NgayLapThe' => '2024-05-12',
                'NgayHetHan' => '2024-11-12',
                'TongNo' => 0
            ]
        ];
        
        foreach ($readers as $reader) {
            DocGia::create($reader);
        }
    }
    
    /**
     * Tạo sách
     */
    private function createBooks()
    {
        echo "Tạo sách...\n";
        
        $books = [
            [
                'MaSach' => 'S001',
                'TenSach' => 'Đắc nhân tâm',
                'MaTacGia' => 2,
                'MaNhaXuatBan' => 1,
                'NamXuatBan' => 2020,
                'NgayNhap' => '2024-01-15',
                'TriGia' => 89000,
                'TinhTrang' => 1
            ],
            [
                'MaSach' => 'S002',
                'TenSach' => 'Nhà giả kim',
                'MaTacGia' => 3,
                'MaNhaXuatBan' => 1,
                'NamXuatBan' => 2019,
                'NgayNhap' => '2024-01-20',
                'TriGia' => 79000,
                'TinhTrang' => 1
            ],
            [
                'MaSach' => 'S003',
                'TenSach' => 'Vũ trụ trong vỏ hạt dẻ',
                'MaTacGia' => 4,
                'MaNhaXuatBan' => 4,
                'NamXuatBan' => 2018,
                'NgayNhap' => '2024-02-01',
                'TriGia' => 120000,
                'TinhTrang' => 1
            ],
            [
                'MaSach' => 'S004',
                'TenSach' => 'Dế Mèn phiêu lưu ký',
                'MaTacGia' => 5,
                'MaNhaXuatBan' => 2,
                'NamXuatBan' => 2021,
                'NgayNhap' => '2024-02-10',
                'TriGia' => 65000,
                'TinhTrang' => 1
            ],
            [
                'MaSach' => 'S005',
                'TenSach' => 'Truyện Kiều',
                'MaTacGia' => 1,
                'MaNhaXuatBan' => 3,
                'NamXuatBan' => 2020,
                'NgayNhap' => '2024-02-15',
                'TriGia' => 55000,
                'TinhTrang' => 1
            ],
            [
                'MaSach' => 'S006',
                'TenSach' => 'Chí Phèo',
                'MaTacGia' => 6,
                'MaNhaXuatBan' => 3,
                'NamXuatBan' => 2021,
                'NgayNhap' => '2024-03-01',
                'TriGia' => 45000,
                'TinhTrang' => 1
            ],
            [
                'MaSach' => 'S007',
                'TenSach' => 'Số đỏ',
                'MaTacGia' => 7,
                'MaNhaXuatBan' => 3,
                'NamXuatBan' => 2020,
                'NgayNhap' => '2024-03-05',
                'TriGia' => 68000,
                'TinhTrang' => 1
            ],
            [
                'MaSach' => 'S008',
                'TenSach' => 'Hai đứa trẻ',
                'MaTacGia' => 8,
                'MaNhaXuatBan' => 3,
                'NamXuatBan' => 2021,
                'NgayNhap' => '2024-03-10',
                'TriGia' => 35000,
                'TinhTrang' => 1
            ],
            [
                'MaSach' => 'S009',
                'TenSach' => 'Vang bóng một thời',
                'MaTacGia' => 9,
                'MaNhaXuatBan' => 3,
                'NamXuatBan' => 2020,
                'NgayNhap' => '2024-03-15',
                'TriGia' => 75000,
                'TinhTrang' => 1
            ],
            [
                'MaSach' => 'S010',
                'TenSach' => 'Nhật ký trong tù',
                'MaTacGia' => 10,
                'MaNhaXuatBan' => 5,
                'NamXuatBan' => 2021,
                'NgayNhap' => '2024-03-20',
                'TriGia' => 42000,
                'TinhTrang' => 1
            ]
        ];
        
        foreach ($books as $book) {
            $sach = Sach::create($book);
            // Gán ngẫu nhiên 1-2 thể loại cho mỗi sách
            $theLoaiIds = \App\Models\TheLoai::inRandomOrder()->limit(rand(1,2))->pluck('id')->toArray();
            $sach->theLoais()->attach($theLoaiIds);
        }
    }
    
    /**
     * Tạo phiếu mượn
     */
    private function createBorrowRecords()
    {
        echo "Tạo phiếu mượn...\n";
        
        // Sử dụng seeder PhieuMuonSeeder đã có
        $this->call(PhieuMuonSeeder::class);
    }
    
    /**
     * Tạo phiếu thu tiền phạt
     */
    private function createFineRecords()
    {
        echo "Tạo phiếu thu tiền phạt...\n";
        
        // Sử dụng seeder FineRecordsSeeder đã có
        $this->call(FineRecordsSeeder::class);
    }
}
