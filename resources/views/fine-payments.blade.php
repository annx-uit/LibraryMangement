@extends('layouts.app')

@section('title', 'Quản lý phiếu thu tiền phạt - Hệ thống thư viện')

@push('styles')
<style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      color: #2d3748;
      line-height: 1.6;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      text-align: center;
      margin-bottom: 40px;
      animation: fadeInDown 1s ease-out;
    }

    .header h1 {
      color: #fff;
      font-size: 2.5rem;
      margin-bottom: 10px;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .header p {
      color: rgba(255, 255, 255, 0.9);
      font-size: 1.1rem;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
      animation: fadeInUp 1s ease-out 0.2s both;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.95);
      padding: 20px;
      border-radius: 15px;
      text-align: center;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: bold;
      color: #4299e1;
      margin-bottom: 5px;
    }

    .stat-label {
      color: #718096;
      font-weight: 500;
    }

    .controls {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      animation: fadeInUp 1s ease-out 0.4s both;
      flex-wrap: wrap;
      gap: 15px;
    }

    .search-box {
      position: relative;
      flex: 1;
      max-width: 400px;
    }

    .search-box input {
      width: 100%;
      padding: 12px 45px 12px 15px;
      border: none;
      border-radius: 25px;
      font-size: 16px;
      background: rgba(255, 255, 255, 0.95);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .search-box input:focus {
      outline: none;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      background: #fff;
    }

    .search-icon {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #a0aec0;
    }

    .add-btn {
      padding: 12px 20px;
      border: none;
      border-radius: 25px;
      font-weight: 600;
      font-size: 14px;
      text-decoration: none;
      color: white;
      background: linear-gradient(135deg, #4299e1, #3182ce);
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 4px 15px rgba(66, 153, 225, 0.2);
      white-space: nowrap;
    }
    
    .add-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .add-btn:active {
      transform: translateY(0);
    }
    
    .add-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }
    
    .add-btn:hover::before {
      left: 100%;
    }
    
    .button-group {
      display: flex;
      gap: 12px;
      align-items: center;
      flex-wrap: wrap;
    }
    
    .nav-home {
      background: linear-gradient(135deg, #f39c12, #e67e22) !important;
      box-shadow: 0 4px 15px rgba(243, 156, 18, 0.2) !important;
    }
    
    .nav-home:hover {
      background: linear-gradient(135deg, #e67e22, #d35400) !important;
      box-shadow: 0 8px 25px rgba(230, 126, 34, 0.3) !important;
    }
    
    .add-payment-btn {
      background: linear-gradient(135deg, #48bb78, #38a169) !important;
      box-shadow: 0 4px 15px rgba(72, 187, 120, 0.2) !important;
    }
    
    .add-payment-btn:hover {
      background: linear-gradient(135deg, #38a169, #2f855a) !important;
      box-shadow: 0 8px 25px rgba(56, 161, 105, 0.3) !important;
    }

    .table-container {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      animation: fadeInUp 1s ease-out 0.6s both;
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #e2e8f0;
    }

    th {
      background: linear-gradient(135deg, #f7fafc, #edf2f7);
      color: #4a5568;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
    }

    tbody tr:hover {
      background: #f8fafc;
      transform: scale(1.01);
    }

    .actions {
      display: flex;
      gap: 10px;
      justify-content: center;
      align-items: center;
    }

    .btn {
      padding: 8px 16px;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      font-weight: 500;
      font-size: 14px;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
    }

    .delete-btn {
      background: linear-gradient(135deg, #e53e3e, #c53030);
      color: white;
      box-shadow: 0 3px 10px rgba(229, 62, 62, 0.2);
    }

    .delete-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(229, 62, 62, 0.4);
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(5px);
      z-index: 1000;
      animation: fadeIn 0.3s ease-out;
    }

    .modal-content {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 30px;
      border-radius: 20px;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
      animation: slideIn 0.3s ease-out;
    }

    .modal h2 {
      margin-bottom: 20px;
      color: #2d3748;
      text-align: center;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: #4a5568;
      font-weight: 600;
    }

    .form-group input, .form-group select {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e2e8f0;
      border-radius: 10px;
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .form-group input:focus, .form-group select:focus {
      outline: none;
      border-color: #4299e1;
      box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }

    .debt-info {
      background: #f7fafc;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
      border-left: 4px solid #4299e1;
    }

    .debt-amount {
      font-size: 1.2rem;
      font-weight: bold;
      color: #e53e3e;
      margin-bottom: 5px;
    }

    .remaining-amount {
      font-size: 1.1rem;
      font-weight: bold;
      color: #38a169;
    }

    .modal-actions {
      display: flex;
      gap: 15px;
      justify-content: flex-end;
      margin-top: 25px;
    }

    .cancel-btn {
      background: #e2e8f0;
      color: #4a5568;
      border: none;
      padding: 12px 24px;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .cancel-btn:hover {
      background: #cbd5e0;
    }

    .save-btn {
      background: linear-gradient(135deg, #48bb78, #38a169);
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .save-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(72, 187, 120, 0.4);
    }

    .save-btn:disabled {
      background: #cbd5e0;
      color: #a0aec0;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .toast {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #48bb78;
      color: white;
      padding: 15px 20px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      z-index: 1001;
      display: none;
      animation: slideInRight 0.3s ease-out;
    }

    .toast.error {
      background: #e53e3e;
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #718096;
    }

    .money-input {
      text-align: right;
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes fadeInDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideIn { from { opacity: 0; transform: translate(-50%, -50%) scale(0.8); } to { opacity: 1; transform: translate(-50%, -50%) scale(1); } }
    @keyframes slideInRight { from { opacity: 0; transform: translateX(100px); } to { opacity: 1; transform: translateX(0); } }
</style>
@endpush

@section('content')
  <div class="container">
    <div class="header">
      <h1>💰 Hệ thống quản lý phiếu thu tiền phạt</h1>
      <p>Dành cho nhân viên thư viện</p>
    </div>

    <!-- Toast notifications -->
    <div id="toast" class="toast">
      <span id="toast-message"></span>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-number" id="totalPayments">0</div>
        <div class="stat-label">Tổng số phiếu thu</div>
      </div>
      <div class="stat-card">
        <div class="stat-number" id="totalAmount">0đ</div>
        <div class="stat-label">Tổng số tiền đã thu</div>
      </div>
      <div class="stat-card">
        <div class="stat-number" id="totalDebt">0đ</div>
        <div class="stat-label">Tổng nợ còn lại</div>
      </div>
    </div>

    <div class="controls">
      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Tìm kiếm theo mã phiếu, tên độc giả..." />
        <span class="search-icon">🔍</span>
      </div>
      <div class="button-group">
        <a href="{{ route('home') }}" class="add-btn nav-home">
          🏠 Trang chủ
        </a>
        <button class="add-btn add-payment-btn" onclick="openModal()">➕ Thêm phiếu thu mới</button>
      </div>
    </div>

    <div class="table-container">
      <table id="paymentsTable">
        <thead>
          <tr>
            <th>Mã phiếu</th>
            <th>Độc giả</th>
            <th>Số tiền nộp</th>
            <th>Ngày thu</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody id="paymentsTableBody"></tbody>
      </table>
      <div class="empty-state" id="emptyState" style="display: none;">
        <div style="font-size: 4rem; margin-bottom: 20px;">💰</div>
        <h3>Chưa có phiếu thu nào</h3>
        <p>Hãy thêm phiếu thu đầu tiên!</p>
      </div>
    </div>
  </div>

  <!-- Modal thêm phiếu thu -->
  <div class="modal" id="paymentModal">
    <div class="modal-content">
      <h2 id="modalTitle">Thêm phiếu thu mới</h2>
      <form id="paymentForm">
        <div class="form-group">
          <label for="docgia_id">Độc giả *</label>
          <select id="docgia_id" required>
            <option value="">-- Chọn độc giả --</option>
            @foreach ($docGias as $docGia)
              <option value="{{ $docGia->id }}" data-debt="{{ $docGia->TongNo }}">
                {{ $docGia->HoTen }}
              </option>
            @endforeach
          </select>
        </div>

        <div id="debtInfo" class="debt-info" style="display: none;">
          <div class="debt-amount">Tổng nợ <span id="debtLabel">hiện tại</span>: <span id="currentDebt">0đ</span></div>
          <div class="remaining-amount">Số tiền còn lại sau khi thu: <span id="remainingDebt">0đ</span></div>
        </div>

        <div class="form-group">
          <label for="SoTienNop">Số tiền nộp *</label>
          <input type="number" id="SoTienNop" class="money-input" step="1000" min="0" required placeholder="0" />
        </div>

        <div class="modal-actions">
          <button type="button" class="btn cancel-btn" onclick="closeModal()">Hủy</button>
          <button type="submit" class="btn save-btn" id="saveBtn">Lưu</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  // Lấy CSRF token từ meta
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // Khởi tạo dữ liệu từ server
  let payments = [
    @foreach ($phieuThus as $phieuThu)
      {
        id: {{ $phieuThu->id }},
        MaPhieu: '{{ addslashes($phieuThu->MaPhieu) }}',
        docgia_id: {{ $phieuThu->docgia_id }},
        docGiaName: '{{ addslashes($phieuThu->docGia->HoTen) }}',
        docGiaCode: '{{ addslashes($phieuThu->docGia->MaDocGia) }}',
        SoTienNop: {{ $phieuThu->SoTienNop }},
                        created_at: '{{ $phieuThu->NgayThu ? $phieuThu->NgayThu->format("Y-m-d") : date("Y-m-d") }}',
      },
    @endforeach
  ];

  let docGias = [
    @foreach ($docGias as $docGia)
      {
        id: {{ $docGia->id }},
        name: '{{ addslashes($docGia->HoTen) }}',
        code: '{{ addslashes($docGia->MaDocGia) }}',
        debt: {{ $docGia->TongNo }},
      },
    @endforeach
  ];

  let filteredPayments = [...payments];

  // Hàm AJAX chung dùng fetch
  async function goApi(url, method, data = null) {
    const opts = {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      }
    };
    if (data) opts.body = JSON.stringify(data);
    
    const res = await fetch(url, opts);
    const responseData = await res.json();
    
    if (!res.ok) {
      throw new Error(responseData.message || 'Có lỗi xảy ra');
    }
    
    return responseData;
  }

  // Hiển thị thông báo toast
  function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    
    toastMessage.textContent = message;
    toast.style.background = type === 'success' ? '#48bb78' : '#e53e3e';
    toast.style.display = 'block';
    
    setTimeout(() => {
      toast.style.display = 'none';
    }, 3000);
  }

  // Định dạng số tiền
  function formatMoney(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
  }

  // Cập nhật số liệu
  function updateStats() {
    const totalPayments = payments.length;
    const totalAmount = payments.reduce((sum, p) => sum + parseFloat(p.SoTienNop), 0);
    const totalDebt = docGias.reduce((sum, d) => sum + parseFloat(d.debt), 0);

    document.getElementById('totalPayments').textContent = totalPayments;
    document.getElementById('totalAmount').textContent = formatMoney(totalAmount);
    document.getElementById('totalDebt').textContent = formatMoney(totalDebt);
  }

  // Vẽ lại bảng
  function renderPayments() {
    const tbody = document.getElementById('paymentsTableBody');
    const emptyState = document.getElementById('emptyState');
    
    if (filteredPayments.length === 0) {
      tbody.innerHTML = '';
      emptyState.style.display = 'block';
    } else {
      emptyState.style.display = 'none';
      tbody.innerHTML = '';
      
      filteredPayments.forEach((p) => {
        const tr = document.createElement('tr');
                        const createdDate = new Date(p.created_at).toLocaleDateString('vi-VN');
        
        tr.innerHTML = `
          <td><strong>${p.MaPhieu}</strong></td>
          <td>
            <div><strong>${p.docGiaName}</strong></div>
            <div style="color: #718096; font-size: 0.9rem;">${p.docGiaCode}</div>
          </td>
          <td><strong style="color: #e53e3e;">${formatMoney(p.SoTienNop)}</strong></td>
          <td>${createdDate}</td>
          <td class="actions">
            <button class="btn delete-btn" onclick="deletePayment(${p.id})">🗑️ Xóa</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }
    updateStats();
  }

  // Tìm kiếm
  function searchPayments() {
    const term = document.getElementById('searchInput').value.toLowerCase();
    filteredPayments = term
      ? payments.filter(p => 
          p.MaPhieu.toLowerCase().includes(term) ||
          p.docGiaName.toLowerCase().includes(term) ||
          p.docGiaCode.toLowerCase().includes(term)
        )
      : [...payments];
    renderPayments();
  }

  // Mở modal (chỉ thêm mới)
  function openModal() {
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('paymentForm');
    const debtInfo = document.getElementById('debtInfo');
    
    title.textContent = 'Thêm phiếu thu mới';
    form.reset();
    debtInfo.style.display = 'none';
    
    document.getElementById('paymentModal').style.display = 'block';
  }

  // Đóng modal
  function closeModal() {
    document.getElementById('paymentModal').style.display = 'none';
  }

  // Cập nhật thông tin nợ khi chọn độc giả
  function updateDebtInfo() {
    const docgia_id = document.getElementById('docgia_id').value;
    const soTienNop = parseFloat(document.getElementById('SoTienNop').value) || 0;
    const debtInfo = document.getElementById('debtInfo');
    
    if (docgia_id) {
      const docGia = docGias.find(d => d.id == docgia_id);
      if (docGia) {
        const currentDebt = docGia.debt;
        const remainingDebt = Math.max(0, currentDebt - soTienNop);
        
        // Luôn hiển thị "hiện tại" vì chỉ có thêm mới
        document.getElementById('debtLabel').textContent = 'hiện tại';
        document.getElementById('currentDebt').textContent = formatMoney(currentDebt);
        document.getElementById('remainingDebt').textContent = formatMoney(remainingDebt);
        debtInfo.style.display = 'block';
        
        // Validate số tiền nộp - quan trọng: phải dùng currentDebt để validate
        const soTienNopInput = document.getElementById('SoTienNop');
        const saveBtn = document.getElementById('saveBtn');
        
        if (soTienNop > currentDebt) {
          soTienNopInput.style.borderColor = '#e53e3e';
          saveBtn.disabled = true;
          showToast('Số tiền nộp không được vượt quá tổng nợ', 'error');
        } else {
          soTienNopInput.style.borderColor = '#e2e8f0';
          saveBtn.disabled = false;
        }
      }
    } else {
      debtInfo.style.display = 'none';
    }
  }

  // Lưu phiếu thu (chỉ thêm mới)
  async function savePayment(e) {
    e.preventDefault();
    
    const docgia_id = document.getElementById('docgia_id').value;
    const SoTienNop = parseFloat(document.getElementById('SoTienNop').value);
    
    if (!docgia_id || !SoTienNop) {
      showToast('Vui lòng điền đầy đủ thông tin', 'error');
      return;
    }

    try {
      const data = { docgia_id, SoTienNop };
      
      // Chỉ tạo mới
      const response = await goApi('/api/fine-payments', 'POST', data);
      if (response.success) {
        payments.push({
          id: response.data.id,
          MaPhieu: response.data.MaPhieu,
          docgia_id: response.data.docgia_id,
          docGiaName: response.data.doc_gia?.HoTen || '',
          docGiaCode: response.data.doc_gia?.MaDocGia || '',
          SoTienNop: response.data.SoTienNop,
                          created_at: response.data.NgayThu ? response.data.NgayThu : new Date().toISOString().split('T')[0]
        });
        // Cập nhật nợ của độc giả
        const docGiaIndex = docGias.findIndex(d => d.id == response.data.docgia_id);
        if (docGiaIndex !== -1) {
          docGias[docGiaIndex].debt -= SoTienNop;
        }
        showToast('Thêm phiếu thu thành công!');
      }
      
      searchPayments();
      closeModal();
    } catch (err) {
      console.error('Error:', err);
      showToast('Lỗi khi lưu phiếu thu: ' + err.message, 'error');
    }
  }

  // Xóa phiếu thu
  async function deletePayment(id) {
    const payment = payments.find(p => p.id === id);
    if (!payment) return;
    
    if (!confirm(`Bạn có chắc chắn xóa phiếu thu "${payment.MaPhieu}"?\nSố tiền ${formatMoney(payment.SoTienNop)} sẽ được hoàn lại cho độc giả.`)) return;
    
    try {
      const response = await goApi(`/api/fine-payments/${id}`, 'DELETE');
      if (response.success) {
        // Xóa khỏi mảng payments
        payments = payments.filter(p => p.id !== id);
        
        // Hoàn lại nợ cho độc giả
        const docGiaIndex = docGias.findIndex(d => d.id == payment.docgia_id);
        if (docGiaIndex !== -1) {
          docGias[docGiaIndex].debt += payment.SoTienNop;
        }
        
        searchPayments();
        showToast('Xóa phiếu thu thành công!');
      } else {
        showToast(response.message || 'Không thể xóa phiếu thu', 'error');
      }
    } catch (err) {
      console.error('Error:', err);
      showToast('Lỗi khi xóa: ' + err.message, 'error');
    }
  }

  // Error handling for uncaught errors
  window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    showToast('Có lỗi JavaScript xảy ra', 'error');
  });

  // Khởi chạy khi DOM sẵn sàng
  document.addEventListener('DOMContentLoaded', () => {
    console.log('Payments loaded:', payments.length);
    console.log('Readers loaded:', docGias.length);
    console.log('CSRF Token:', csrfToken);
    
    // Gán sự kiện
    document.getElementById('searchInput').addEventListener('input', searchPayments);
    document.getElementById('paymentForm').addEventListener('submit', savePayment);
    document.getElementById('paymentModal').addEventListener('click', e => {
      if (e.target === e.currentTarget) closeModal();
    });
    
    // Sự kiện thay đổi độc giả và số tiền
    document.getElementById('docgia_id').addEventListener('change', updateDebtInfo);
    document.getElementById('SoTienNop').addEventListener('input', updateDebtInfo);
    
    renderPayments();
  });
</script>
@endpush
