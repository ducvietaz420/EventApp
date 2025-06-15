@extends('layouts.app')

@section('title', 'Chi tiết ngân sách')
@section('page-title', $budget->description)

@section('page-actions')
    <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-warning">
        <i class="fas fa-edit me-2"></i>Chỉnh sửa
    </a>
    <a href="{{ route('budgets.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBudgetModal">
        <i class="fas fa-trash me-2"></i>Xóa
    </button>
@endsection

@section('content')
<div class="row">
    <!-- Thông tin chính -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin ngân sách</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted" width="40%">Sự kiện:</td>
                                <td>
                                    <a href="{{ route('events.show', $budget->event) }}" class="text-decoration-none">
                                        {{ $budget->event->name }}
                                    </a>
                                    <span class="badge bg-secondary ms-2">{{ ucfirst($budget->event->type) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Danh mục:</td>
                                <td>
                                    <span class="badge bg-primary">{{ $budget->category_display }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Mô tả:</td>
                                <td>{{ $budget->description }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Ngày phân bổ:</td>
                                <td>
                                    @if($budget->allocated_date)
                                        {{ $budget->allocated_date->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">Chưa xác định</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Hạn sử dụng:</td>
                                <td>
                                    @if($budget->deadline)
                                        {{ $budget->deadline->format('d/m/Y') }}
                                        @if($budget->deadline->isPast())
                                            <span class="badge bg-danger ms-2">Đã hết hạn</span>
                                        @elseif($budget->deadline->diffInDays() <= 7)
                                            <span class="badge bg-warning ms-2">Sắp hết hạn</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Không giới hạn</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted" width="40%">Tạo bởi:</td>
                                <td>{{ $budget->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Cập nhật:</td>
                                <td>{{ $budget->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Trạng thái:</td>
                                <td>
                                    @php
                                        $percentage = $budget->estimated_cost > 0 ? ($budget->actual_cost / $budget->estimated_cost) * 100 : 0;
                                    @endphp
                                    @if($percentage == 0)
                                        <span class="badge bg-secondary">Chưa sử dụng</span>
                                    @elseif($percentage < 50)
                                        <span class="badge bg-success">Đang sử dụng</span>
                                    @elseif($percentage < 90)
                                        <span class="badge bg-warning">Gần hết</span>
                                    @elseif($percentage < 100)
                                        <span class="badge bg-danger">Sắp hết</span>
                                    @else
                                        <span class="badge bg-dark">Đã hết</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Độ ưu tiên:</td>
                                <td>
                                    @if($budget->deadline && $budget->deadline->diffInDays() <= 7)
                                        <span class="badge bg-danger">Cao</span>
                                    @elseif($percentage > 80)
                                        <span class="badge bg-warning">Trung bình</span>
                                    @else
                                        <span class="badge bg-success">Thấp</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- @if($budget->notes)
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted mb-2">Ghi chú:</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $budget->notes }}
                        </div>
                    </div>
                </div>
                @endif -->
            </div>
        </div>
        
        <!-- Biểu đồ chi tiêu -->
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Phân tích chi tiêu</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="budgetChart" width="300" height="300"></canvas>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex flex-column justify-content-center h-100">
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Hiệu quả sử dụng ngân sách</h6>
                                @if($percentage <= 100)
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar 
                                            @if($percentage < 50) bg-success
                                            @elseif($percentage < 80) bg-warning
                                            @else bg-danger
                                            @endif" 
                                             style="width: {{ min($percentage, 100) }}%">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                @else
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-danger" style="width: 100%">
                                            Vượt {{ number_format($percentage - 100, 1) }}%
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <div class="h6 mb-0 text-primary">{{ number_format($budget->estimated_cost) }}</div>
                                        <small class="text-muted">Ngân sách</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <div class="h6 mb-0 text-danger">{{ number_format($budget->actual_cost) }}</div>
                                        <small class="text-muted">Đã chi</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <div class="h6 mb-0 {{ $budget->estimated_cost - $budget->actual_cost >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($budget->estimated_cost - $budget->actual_cost) }}
                                        </div>
                                        <small class="text-muted">Còn lại</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Thống kê nhanh -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Thống kê</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                            <div class="h5 mb-1 text-primary">{{ number_format($budget->estimated_cost) }}</div>
                            <div class="small text-muted">Ngân sách (VNĐ)</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-danger bg-opacity-10 rounded">
                            <div class="h5 mb-1 text-danger">{{ number_format($budget->actual_cost) }}</div>
                            <div class="small text-muted">Đã chi (VNĐ)</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                            <div class="h5 mb-1 text-success">{{ number_format($budget->estimated_cost - $budget->actual_cost) }}</div>
                            <div class="small text-muted">Còn lại (VNĐ)</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                            <div class="h5 mb-1 text-info">{{ number_format($percentage, 1) }}%</div>
                            <div class="small text-muted">Đã sử dụng</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hành động nhanh -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Hành động nhanh</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-outline-warning">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa ngân sách
                    </a>
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                        <i class="fas fa-plus me-2"></i>Thêm chi tiêu
                    </button>
                    <a href="{{ route('events.show', $budget->event) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>Xem sự kiện
                    </a>
                    <button type="button" class="btn btn-outline-primary" onclick="exportBudgetReport()">
                        <i class="fas fa-download me-2"></i>Xuất báo cáo
                    </button>
                </div>
            </div>
        </div>

        <!-- Chi tiết chi tiêu -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-receipt me-2"></i>Chi tiết chi tiêu</h6>
            </div>
            <div class="card-body">
                @if($budget->expenseLogs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="15%">Ngày giờ</th>
                                    <th width="20%">Số tiền</th>
                                    <th>Mô tả</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($budget->expenseLogs as $log)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $log->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $log->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger fs-6">{{ number_format($log->amount) }} VNĐ</span>
                                    </td>
                                    <td>{{ $log->description }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td><strong>Tổng cộng:</strong></td>
                                    <td><strong class="text-danger">{{ number_format($budget->expenseLogs->sum('amount')) }} VNĐ</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Chưa có chi tiêu nào</h6>
                        <p class="text-muted">Nhấn "Thêm chi tiêu" để ghi lại các khoản chi tiêu cho ngân sách này.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Ngân sách liên quan -->
        @if($relatedBudgets->count() > 0)
        <div class="card shadow">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-link me-2"></i>Ngân sách liên quan</h6>
            </div>
            <div class="card-body">
                @foreach($relatedBudgets as $related)
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <div>
                        <div class="fw-bold">{{ $related->description }}</div>
                                                        <small class="text-muted">{{ $related->category_display }}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">{{ number_format($related->estimated_cost) }}</div>
                        <small class="text-muted">VNĐ</small>
                    </div>
                </div>
                @endforeach
                <div class="text-center mt-3">
                    <a href="{{ route('budgets.index', ['event_id' => $budget->event_id]) }}" class="btn btn-sm btn-outline-primary">
                        Xem tất cả
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal thêm chi tiêu -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm chi tiêu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addExpenseForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="expenseAmount" class="form-label">Số tiền chi thêm (VNĐ)</label>
                        <input type="number" class="form-control" id="expenseAmount" min="0" step="1000" required>
                        <div class="form-text">Số tiền sẽ được cộng vào chi tiêu hiện tại</div>
                    </div>
                    <div class="mb-3">
                        <label for="expenseNote" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="expenseNote" rows="3" placeholder="Mô tả chi tiêu này..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <strong>Chi tiêu hiện tại:</strong> <span id="currentSpentModal">{{ number_format($budget->actual_cost) }}</span> VNĐ<br>
                        <strong>Ngân sách còn lại:</strong> <span id="remainingBudgetModal">{{ number_format($budget->estimated_cost - $budget->actual_cost) }}</span> VNĐ
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Thêm chi tiêu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal xác nhận xóa -->
<div class="modal fade" id="deleteBudgetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa ngân sách này?</p>
                <div class="alert alert-warning">
                    <strong>Cảnh báo:</strong> Hành động này không thể hoàn tác!
                </div>
                <div class="bg-light p-3 rounded">
                    <strong>Ngân sách:</strong> {{ $budget->description }}<br>
                    <strong>Số tiền:</strong> {{ number_format($budget->estimated_cost) }} VNĐ<br>
                    <strong>Đã chi:</strong> {{ number_format($budget->actual_cost) }} VNĐ
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa ngân sách</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Biểu đồ ngân sách
const ctx = document.getElementById('budgetChart').getContext('2d');
let budgetAmount = {{ $budget->estimated_cost }};
let spentAmount = {{ $budget->actual_cost }};
let remainingAmount = budgetAmount - spentAmount;

// Khởi tạo biểu đồ
const budgetChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Đã chi', 'Còn lại'],
        datasets: [{
            data: [spentAmount, Math.max(0, remainingAmount)],
            backgroundColor: [
                '#dc3545',
                remainingAmount >= 0 ? '#28a745' : '#ffc107' // Màu vàng nếu chi vượt
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const value = context.parsed;
                        const total = budgetChart.data.datasets[0].data.reduce((a, b) => a + b, 0); // Tính lại tổng từ data
                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return context.label + ': ' + value.toLocaleString('vi-VN') + ' VNĐ (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Hàm cập nhật biểu đồ
function updateBudgetChart(newSpentAmount, newRemainingAmount) {
    budgetChart.data.datasets[0].data = [newSpentAmount, Math.max(0, newRemainingAmount)];
    budgetChart.data.datasets[0].backgroundColor = [
        '#dc3545',
        newRemainingAmount >= 0 ? '#28a745' : '#ffc107'
    ];
    budgetChart.update();
}

// Xử lý form thêm chi tiêu
document.getElementById('addExpenseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const amountInput = document.getElementById('expenseAmount');
    const noteInput = document.getElementById('expenseNote');
    const spentAmountChange = parseInt(amountInput.value);
    const note = noteInput.value;

    if (isNaN(spentAmountChange) || spentAmountChange <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi!',
            text: 'Vui lòng nhập số tiền chi tiêu hợp lệ.',
        });
        return;
    }

    fetch(`{{ route('budgets.update_spent', $budget) }}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            spent_amount_change: spentAmountChange,
            notes: note 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });

            // Cập nhật các giá trị trên giao diện
            const updatedBudget = data.budget;
            spentAmount = updatedBudget.actual_cost;
            remainingAmount = updatedBudget.estimated_cost - updatedBudget.actual_cost;
            budgetAmount = updatedBudget.estimated_cost; // Cập nhật lại budgetAmount nếu cần

            // Cập nhật modal
            document.getElementById('currentSpentModal').textContent = spentAmount.toLocaleString('vi-VN');
            document.getElementById('remainingBudgetModal').textContent = remainingAmount.toLocaleString('vi-VN');
            
            // Cập nhật thống kê nhanh
            document.querySelector('.bg-primary.bg-opacity-10 .h5').textContent = budgetAmount.toLocaleString('vi-VN');
            document.querySelector('.bg-danger.bg-opacity-10 .h5').textContent = spentAmount.toLocaleString('vi-VN');
            document.querySelector('.bg-success.bg-opacity-10 .h5').textContent = remainingAmount.toLocaleString('vi-VN');
            const newPercentage = budgetAmount > 0 ? (spentAmount / budgetAmount) * 100 : 0;
            document.querySelector('.bg-info.bg-opacity-10 .h5').textContent = newPercentage.toFixed(1) + '%';

            // Cập nhật thanh tiến độ
            const progressBar = document.querySelector('.progress-bar');
            progressBar.style.width = Math.min(newPercentage, 100) + '%';
            progressBar.textContent = newPercentage.toFixed(1) + '%';
            if (newPercentage < 50) {
                progressBar.className = 'progress-bar bg-success';
            } else if (newPercentage < 80) {
                progressBar.className = 'progress-bar bg-warning';
            } else {
                progressBar.className = 'progress-bar bg-danger';
            }
            if (newPercentage > 100) {
                 progressBar.textContent = 'Vượt ' + (newPercentage - 100).toFixed(1) + '%';
            }

            // Cập nhật các ô số liệu dưới biểu đồ
            document.querySelector('.col-md-6 .row.text-center .col-4:nth-child(1) .h6').textContent = budgetAmount.toLocaleString('vi-VN');
            document.querySelector('.col-md-6 .row.text-center .col-4:nth-child(2) .h6').textContent = spentAmount.toLocaleString('vi-VN');
            const remainingDisplay = document.querySelector('.col-md-6 .row.text-center .col-4:nth-child(3) .h6');
            remainingDisplay.textContent = remainingAmount.toLocaleString('vi-VN');
            remainingDisplay.className = 'h6 mb-0 ' + (remainingAmount >= 0 ? 'text-success' : 'text-danger');

            // Cập nhật biểu đồ
            updateBudgetChart(spentAmount, remainingAmount);

            // Đóng modal và reset form
            var addExpenseModal = bootstrap.Modal.getInstance(document.getElementById('addExpenseModal'));
            addExpenseModal.hide();
            amountInput.value = '';
            noteInput.value = '';

            // Cập nhật ghi chú nếu có
            if (updatedBudget.notes && document.querySelector('.bg-light.p-3.rounded')) {
                 document.querySelector('.bg-light.p-3.rounded').innerHTML = updatedBudget.notes.replace(/\n/g, '<br>');
            } else if (updatedBudget.notes && !document.querySelector('.bg-light.p-3.rounded')) {
                // Nếu chưa có phần ghi chú, tạo mới
                const notesSection = document.createElement('div');
                notesSection.innerHTML = `
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">Ghi chú:</h6>
                            <div class="bg-light p-3 rounded">
                                ${updatedBudget.notes.replace(/\n/g, '<br>')}
                            </div>
                        </div>
                    </div>
                `;
                document.querySelector('.card-body > .row:last-of-type').insertAdjacentElement('afterend', notesSection);
            }

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: data.message || 'Có lỗi xảy ra, vui lòng thử lại.',
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Lỗi!',
            text: 'Không thể kết nối đến máy chủ. Vui lòng kiểm tra lại kết nối mạng.',
        });
    });
});

// Xuất báo cáo ngân sách
function exportBudgetReport() {
    const reportData = {
        budget: {
            description: '{{ $budget->description }}',
            category: '{{ $budget->category }}',
            estimated_cost: {{ $budget->estimated_cost }},
            actual_cost: {{ $budget->actual_cost }},
            remaining: {{ $budget->estimated_cost - $budget->actual_cost }},
            percentage: {{ $percentage }}
        },
        event: {
            name: '{{ $budget->event->name }}',
            type: '{{ $budget->event->type }}'
        }
    };
    
    // Tạo nội dung báo cáo
    let reportContent = `BÁO CÁO NGÂN SÁCH\n`;
    reportContent += `================\n\n`;
    reportContent += `Sự kiện: ${reportData.event.name}\n`;
    reportContent += `Loại: ${reportData.event.type}\n`;
    reportContent += `Ngân sách: ${reportData.budget.description}\n`;
    reportContent += `Danh mục: ${reportData.budget.category}\n\n`;
    reportContent += `CHI TIẾT TÀI CHÍNH:\n`;
    reportContent += `- Ngân sách: ${reportData.budget.estimated_cost.toLocaleString('vi-VN')} VNĐ\n`;
    reportContent += `- Đã chi: ${reportData.budget.actual_cost.toLocaleString('vi-VN')} VNĐ\n`;
    reportContent += `- Còn lại: ${reportData.budget.remaining.toLocaleString('vi-VN')} VNĐ\n`;
    reportContent += `- Tỷ lệ sử dụng: ${reportData.budget.percentage.toFixed(1)}%\n\n`;
    reportContent += `Ngày xuất báo cáo: ${new Date().toLocaleDateString('vi-VN')}\n`;
    
    // Tạo và tải file
    const blob = new Blob([reportContent], { type: 'text/plain;charset=utf-8' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `bao-cao-ngan-sach-${reportData.budget.description.replace(/\s+/g, '-').toLowerCase()}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Format số tiền trong modal
document.getElementById('expenseAmount').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endpush