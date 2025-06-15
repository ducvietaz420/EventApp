@extends('layouts.app')

@section('title', 'Tạo ngân sách mới')
@section('page-title', 'Tạo ngân sách mới')

@section('page-actions')
    <a href="{{ route('budgets.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">Thông tin ngân sách</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('budgets.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_id" class="form-label">Sự kiện <span class="text-danger">*</span></label>
                                <select class="form-select @error('event_id') is-invalid @enderror" id="event_id" name="event_id" required>
                                    <option value="">Chọn sự kiện</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" 
                                                {{ old('event_id', request('event_id')) == $event->id ? 'selected' : '' }}
                                                data-event-type="{{ $event->type }}">
                                            {{ $event->name }} ({{ $event->type_display }})
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
                                    <option value="venue" {{ old('category') === 'venue' ? 'selected' : '' }}>Địa điểm</option>
                                    <option value="catering" {{ old('category') === 'catering' ? 'selected' : '' }}>Catering</option>
                                    <option value="decoration" {{ old('category') === 'decoration' ? 'selected' : '' }}>Trang trí</option>
                                    <option value="equipment" {{ old('category') === 'equipment' ? 'selected' : '' }}>Thiết bị</option>
                                    <option value="marketing" {{ old('category') === 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="staff" {{ old('category') === 'staff' ? 'selected' : '' }}>Nhân sự</option>
                                    <option value="transportation" {{ old('category') === 'transportation' ? 'selected' : '' }}>Vận chuyển</option>
                                    <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Khác</option>
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
                               id="item_name" name="item_name" value="{{ old('item_name') }}" 
                               placeholder="Ví dụ: Thuê địa điểm, Chi phí catering..." required>
                        @error('item_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Mô tả chi tiết về khoản chi này..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Số tiền ngân sách (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" name="amount" value="{{ old('amount') }}" 
                                       min="0" step="1000" placeholder="0" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Nhập số tiền dự kiến cho khoản chi này</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="spent_amount" class="form-label">Số tiền đã chi (VNĐ)</label>
                                <input type="number" class="form-control @error('spent_amount') is-invalid @enderror" 
                                       id="spent_amount" name="spent_amount" value="{{ old('spent_amount', 0) }}" 
                                       min="0" step="1000" placeholder="0">
                                @error('spent_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Số tiền đã chi tiêu thực tế (mặc định: 0)</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="allocated_date" class="form-label">Ngày phân bổ</label>
                                <input type="date" class="form-control @error('allocated_date') is-invalid @enderror" 
                                       id="allocated_date" name="allocated_date" 
                                       value="{{ old('allocated_date', date('Y-m-d')) }}">
                                @error('allocated_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="deadline" class="form-label">Hạn sử dụng</label>
                                <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                                       id="deadline" name="deadline" value="{{ old('deadline') }}">
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
                                  placeholder="Thông tin bổ sung về khoản ngân sách này...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Gợi ý ngân sách -->
                    <div class="card bg-light mb-3" id="budgetSuggestions" style="display: none;">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Gợi ý ngân sách</h6>
                        </div>
                        <div class="card-body" id="suggestionsContent">
                            <!-- Nội dung gợi ý sẽ được load bằng JavaScript -->
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('budgets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Tạo ngân sách
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Mẫu ngân sách theo loại sự kiện -->
        <div class="card shadow mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Mẫu ngân sách tham khảo</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Hội nghị/Seminar</h6>
                        <ul class="list-unstyled small">
                            <li><strong>Địa điểm:</strong> 30-40% tổng ngân sách</li>
                            <li><strong>Catering:</strong> 25-35% tổng ngân sách</li>
                            <li><strong>Thiết bị:</strong> 15-20% tổng ngân sách</li>
                            <li><strong>Marketing:</strong> 10-15% tổng ngân sách</li>
                            <li><strong>Nhân sự:</strong> 5-10% tổng ngân sách</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Tiệc/Sự kiện giải trí</h6>
                        <ul class="list-unstyled small">
                            <li><strong>Địa điểm:</strong> 25-35% tổng ngân sách</li>
                            <li><strong>Catering:</strong> 35-45% tổng ngân sách</li>
                            <li><strong>Trang trí:</strong> 15-25% tổng ngân sách</li>
                            <li><strong>Thiết bị:</strong> 10-15% tổng ngân sách</li>
                            <li><strong>Marketing:</strong> 5-10% tổng ngân sách</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Format number input
document.querySelectorAll('#amount, #spent_amount').forEach(input => {
    input.addEventListener('input', function() {
        // Remove non-numeric characters except for decimal point
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Update remaining amount display
        updateRemainingAmount();
    });
    
    input.addEventListener('blur', function() {
        // Format with thousand separators for display
        if (this.value) {
            const formatted = parseInt(this.value).toLocaleString('vi-VN');
            this.setAttribute('data-formatted', formatted);
        }
    });
});

function updateRemainingAmount() {
    const amount = parseInt(document.getElementById('amount').value) || 0;
    const spentAmount = parseInt(document.getElementById('spent_amount').value) || 0;
    const remaining = amount - spentAmount;
    
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

// Auto-suggest item name based on category
document.getElementById('category').addEventListener('change', function() {
    const category = this.value;
    const itemNameInput = document.getElementById('item_name');
    
    if (!itemNameInput.value && category) {
        const suggestions = {
            'venue': 'Thuê địa điểm tổ chức',
            'catering': 'Chi phí ăn uống',
            'decoration': 'Trang trí và thiết kế',
            'equipment': 'Thuê thiết bị âm thanh, ánh sáng',
            'marketing': 'Quảng cáo và truyền thông',
            'staff': 'Chi phí nhân sự',
            'transportation': 'Vận chuyển và di chuyển',
            'other': 'Chi phí khác'
        };
        
        if (suggestions[category]) {
            itemNameInput.value = suggestions[category];
        }
    }
});

// Load budget suggestions based on event type
document.getElementById('event_id').addEventListener('change', function() {
    const eventId = this.value;
    const selectedOption = this.options[this.selectedIndex];
    const eventType = selectedOption.getAttribute('data-event-type');
    
    if (eventType) {
        loadBudgetSuggestions(eventType);
    }
});

function loadBudgetSuggestions(eventType) {
    const suggestions = {
        'conference': {
            'venue': { min: 5000000, max: 15000000, desc: 'Hội trường cho 100-300 người' },
            'catering': { min: 3000000, max: 10000000, desc: 'Coffee break và buffet' },
            'equipment': { min: 2000000, max: 5000000, desc: 'Âm thanh, máy chiếu, micro' }
        },
        'workshop': {
            'venue': { min: 2000000, max: 8000000, desc: 'Phòng học cho 20-50 người' },
            'catering': { min: 1000000, max: 3000000, desc: 'Coffee break' },
            'equipment': { min: 1000000, max: 3000000, desc: 'Máy chiếu, flipchart' }
        },
        'party': {
            'venue': { min: 10000000, max: 30000000, desc: 'Nhà hàng hoặc khách sạn' },
            'catering': { min: 15000000, max: 50000000, desc: 'Tiệc buffet hoặc set menu' },
            'decoration': { min: 5000000, max: 15000000, desc: 'Trang trí chủ đề' }
        }
    };
    
    const eventSuggestions = suggestions[eventType];
    if (eventSuggestions) {
        let html = '<div class="row">';
        
        Object.keys(eventSuggestions).forEach(category => {
            const suggestion = eventSuggestions[category];
            html += `
                <div class="col-md-4 mb-2">
                    <div class="border rounded p-2 suggestion-item" 
                         onclick="applySuggestion('${category}', ${suggestion.min}, '${suggestion.desc}')" 
                         style="cursor: pointer;">
                        <strong>${getCategoryName(category)}</strong><br>
                        <small class="text-muted">${suggestion.desc}</small><br>
                        <span class="text-primary">${suggestion.min.toLocaleString('vi-VN')} - ${suggestion.max.toLocaleString('vi-VN')} VNĐ</span>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        
        document.getElementById('suggestionsContent').innerHTML = html;
        document.getElementById('budgetSuggestions').style.display = 'block';
    }
}

function getCategoryName(category) {
    const names = {
        'venue': 'Địa điểm',
        'catering': 'Catering',
        'decoration': 'Trang trí',
        'equipment': 'Thiết bị',
        'marketing': 'Marketing',
        'staff': 'Nhân sự',
        'transportation': 'Vận chuyển'
    };
    return names[category] || category;
}

function applySuggestion(category, amount, description) {
    document.getElementById('category').value = category;
    document.getElementById('amount').value = amount;
    document.getElementById('item_name').value = description;
    
    // Trigger change events
    document.getElementById('category').dispatchEvent(new Event('change'));
    document.getElementById('amount').dispatchEvent(new Event('input'));
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

// Set deadline based on event date
document.getElementById('event_id').addEventListener('change', function() {
    // This would require AJAX call to get event date
    // For now, we'll just suggest setting deadline to event date
});
</script>
@endpush