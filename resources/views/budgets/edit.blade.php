@extends('layouts.app')

@section('title', 'Chỉnh sửa ngân sách')
@section('page-title', 'Chỉnh sửa ngân sách')

@section('page-actions')
    <a href="{{ route('budgets.show', $budget) }}" class="btn btn-info">
        <i class="fas fa-eye me-2"></i>Xem chi tiết
    </a>
    <a href="{{ route('budgets.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Chỉnh sửa thông tin ngân sách</h5>
                <div class="text-muted small">
                    <i class="fas fa-calendar me-1"></i>Tạo: {{ $budget->created_at->format('d/m/Y H:i') }}
                    @if($budget->updated_at != $budget->created_at)
                        <br><i class="fas fa-edit me-1"></i>Cập nhật: {{ $budget->updated_at->format('d/m/Y H:i') }}
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Thống kê nhanh -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                            <div class="h5 mb-1 text-primary">{{ number_format($budget->estimated_cost) }} VNĐ</div>
                            <div class="small text-muted">Ngân sách</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-danger bg-opacity-10 rounded">
                            <div class="h5 mb-1 text-danger">{{ number_format($budget->actual_cost) }} VNĐ</div>
                            <div class="small text-muted">Đã chi</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                            <div class="h5 mb-1 text-success">{{ number_format($budget->estimated_cost - $budget->actual_cost) }} VNĐ</div>
                            <div class="small text-muted">Còn lại</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                            <div class="h5 mb-1 text-info">{{ $budget->estimated_cost > 0 ? number_format(($budget->actual_cost / $budget->estimated_cost) * 100, 1) : 0 }}%</div>
                            <div class="small text-muted">Tiến độ</div>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('budgets.update', $budget) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_id" class="form-label">Sự kiện <span class="text-danger">*</span></label>
                                <select class="form-select @error('event_id') is-invalid @enderror" id="event_id" name="event_id" required>
                                    <option value="">Chọn sự kiện</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" 
                                                {{ old('event_id', $budget->event_id) == $event->id ? 'selected' : '' }}
                                                data-event-type="{{ $event->type }}">
                                            {{ $event->name }} ({{ ucfirst($event->type) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('event_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="">Chọn danh mục</option>
                                    <option value="venue" {{ old('category', $budget->category) === 'venue' ? 'selected' : '' }}>Địa điểm</option>
                                    <option value="catering" {{ old('category', $budget->category) === 'catering' ? 'selected' : '' }}>Catering</option>
                                    <option value="decoration" {{ old('category', $budget->category) === 'decoration' ? 'selected' : '' }}>Trang trí</option>
                                    <option value="equipment" {{ old('category', $budget->category) === 'equipment' ? 'selected' : '' }}>Thiết bị</option>
                                    <option value="marketing" {{ old('category', $budget->category) === 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="staff" {{ old('category', $budget->category) === 'staff' ? 'selected' : '' }}>Nhân sự</option>
                                    <option value="transportation" {{ old('category', $budget->category) === 'transportation' ? 'selected' : '' }}>Vận chuyển</option>
                                    <option value="other" {{ old('category', $budget->category) === 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="item_name" class="form-label">Tên khoản mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('item_name') is-invalid @enderror" 
                               id="item_name" name="item_name" value="{{ old('item_name', $budget->item_name) }}" 
                               placeholder="Ví dụ: Thuê địa điểm, Chi phí catering..." required>
                        @error('item_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Mô tả chi tiết về khoản chi này..." required>{{ old('description', $budget->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estimated_cost" class="form-label">Số tiền ngân sách (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('estimated_cost') is-invalid @enderror" 
                                       id="estimated_cost" name="estimated_cost" value="{{ old('estimated_cost', $budget->estimated_cost) }}" 
                                       min="0" step="1000" placeholder="0" required>
                                @error('estimated_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Nhập số tiền dự kiến cho khoản chi này</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="actual_cost" class="form-label">Số tiền đã chi (VNĐ)</label>
                                <input type="number" class="form-control @error('actual_cost') is-invalid @enderror" 
                                       id="actual_cost" name="actual_cost" value="{{ old('actual_cost', $budget->actual_cost) }}" 
                                       min="0" step="1000" placeholder="0">
                                @error('actual_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Số tiền đã chi tiêu thực tế</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hiển thị số tiền còn lại -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info" id="remainingAmount">
                                <i class="fas fa-calculator me-2"></i>
                                <strong>Số tiền còn lại:</strong> 
                                <span id="remainingValue">{{ number_format($budget->estimated_cost - $budget->actual_cost) }} VNĐ</span>
                                <span id="remainingPercent" class="ms-2">
                                    ({{ $budget->estimated_cost > 0 ? number_format((($budget->estimated_cost - $budget->actual_cost) / $budget->estimated_cost) * 100, 1) : 0 }}%)
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="allocated_date" class="form-label">Ngày phân bổ</label>
                                <input type="date" class="form-control @error('allocated_date') is-invalid @enderror" 
                                       id="allocated_date" name="allocated_date" 
                                       value="{{ old('allocated_date', $budget->allocated_date ? $budget->allocated_date->format('Y-m-d') : '') }}">
                                @error('allocated_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="deadline" class="form-label">Hạn sử dụng</label>
                                <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                                       id="deadline" name="deadline" 
                                       value="{{ old('deadline', $budget->deadline ? $budget->deadline->format('Y-m-d') : '') }}">
                                @error('deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Ngày cuối cùng có thể sử dụng ngân sách này</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Thông tin bổ sung về khoản ngân sách này...">{{ old('notes', $budget->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('budgets.show', $budget) }}" class="btn btn-info">
                                <i class="fas fa-eye me-2"></i>Xem chi tiết
                            </a>
                            <a href="{{ route('budgets.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Khôi phục
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật ngân sách
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Lịch sử thay đổi -->
        <div class="card shadow mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử thay đổi</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Tạo ngân sách</h6>
                            <p class="mb-1">Ngân sách được tạo với số tiền {{ number_format($budget->estimated_cost) }} VNĐ</p>
                            <small class="text-muted">{{ $budget->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    
                    @if($budget->updated_at != $budget->created_at)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Cập nhật thông tin</h6>
                            <p class="mb-1">Thông tin ngân sách được cập nhật</p>
                            <small class="text-muted">{{ $budget->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($budget->actual_cost > 0)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-danger"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Chi tiêu</h6>
                            <p class="mb-1">Đã chi {{ number_format($budget->actual_cost) }} VNĐ</p>
                            <small class="text-muted">Cập nhật lần cuối: {{ $budget->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}
</style>
@endpush

@push('scripts')
<script>
// Store original values for reset functionality
const originalValues = {
    event_id: '{{ $budget->event_id }}',
    category: '{{ $budget->category }}',
    description: '{{ $budget->description }}',
    amount: '{{ $budget->amount }}',
    spent_amount: '{{ $budget->spent_amount }}',
    allocated_date: '{{ $budget->allocated_date ? $budget->allocated_date->format('Y-m-d') : '' }}',
    deadline: '{{ $budget->deadline ? $budget->deadline->format('Y-m-d') : '' }}',
    notes: `{{ $budget->notes }}`
};

// Format number input and update remaining amount
document.querySelectorAll('#amount, #spent_amount').forEach(input => {
    input.addEventListener('input', function() {
        // Remove non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Update remaining amount display
        updateRemainingAmount();
    });
});

function updateRemainingAmount() {
    const amount = parseInt(document.getElementById('amount').value) || 0;
    const spentAmount = parseInt(document.getElementById('spent_amount').value) || 0;
    const remaining = amount - spentAmount;
    const percentage = amount > 0 ? ((remaining / amount) * 100) : 0;
    
    // Update display
    document.getElementById('remainingValue').textContent = remaining.toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('remainingPercent').textContent = `(${percentage.toFixed(1)}%)`;
    
    // Update alert class based on remaining amount
    const alertDiv = document.getElementById('remainingAmount');
    alertDiv.className = 'alert';
    
    if (remaining < 0) {
        alertDiv.classList.add('alert-danger');
    } else if (percentage < 20) {
        alertDiv.classList.add('alert-warning');
    } else {
        alertDiv.classList.add('alert-info');
    }
    
    // Show warning if spent amount exceeds budget
    const spentInput = document.getElementById('spent_amount');
    if (spentAmount > amount && amount > 0) {
        spentInput.classList.add('is-invalid');
        if (!spentInput.nextElementSibling || !spentInput.nextElementSibling.classList.contains('invalid-feedback')) {
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = 'Số tiền đã chi vượt quá ngân sách!';
            spentInput.parentNode.appendChild(feedback);
        }
    } else {
        spentInput.classList.remove('is-invalid');
        const feedback = spentInput.parentNode.querySelector('.invalid-feedback');
        if (feedback && feedback.textContent.includes('vượt quá ngân sách')) {
            feedback.remove();
        }
    }
}

// Reset form to original values
function resetForm() {
    if (confirm('Bạn có chắc chắn muốn khôi phục về giá trị ban đầu?')) {
        Object.keys(originalValues).forEach(key => {
            const element = document.getElementById(key);
            if (element) {
                element.value = originalValues[key];
            }
        });
        
        // Trigger change events
        updateRemainingAmount();
    }
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = ['event_id', 'category', 'description', 'amount'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ các trường bắt buộc!');
    }
});

// Initialize remaining amount calculation
updateRemainingAmount();

// Auto-save draft (optional feature)
let autoSaveTimer;
const formInputs = document.querySelectorAll('input, select, textarea');

formInputs.forEach(input => {
    input.addEventListener('input', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            // Auto-save logic could be implemented here
            console.log('Auto-saving draft...');
        }, 5000); // Save after 5 seconds of inactivity
    });
});

// Warn about unsaved changes
let formChanged = false;
formInputs.forEach(input => {
    input.addEventListener('change', function() {
        formChanged = true;
    });
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = 'Bạn có thay đổi chưa được lưu. Bạn có chắc chắn muốn rời khỏi trang?';
    }
});

// Mark form as saved when submitted
document.querySelector('form').addEventListener('submit', function() {
    formChanged = false;
});
</script>
@endpush