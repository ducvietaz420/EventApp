@extends('layouts.app')

@section('title', 'Chỉnh sửa sự kiện: ' . $event->name)
@section('page-title', 'Chỉnh sửa sự kiện')

@section('page-actions')
    <div class="d-flex align-items-center">
        <div class="btn-group" role="group">
            <a href="{{ route('events.show', $event->id) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>Xem chi tiết
            </a>
            <a href="{{ route('events.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">Chỉnh sửa thông tin sự kiện</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('events.update', $event->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên sự kiện <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name', $event->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Loại sự kiện <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                                        required>
                                        <option value="">Chọn loại sự kiện</option>
                                        <option value="wedding" {{ old('type', $event->type) === 'wedding' ? 'selected' : '' }}>Đám cưới</option>
                                        <option value="conference" {{ old('type', $event->type) === 'conference' ? 'selected' : '' }}>Hội nghị</option>
                                        <option value="exhibition" {{ old('type', $event->type) === 'exhibition' ? 'selected' : '' }}>Triển lãm</option>
                                        <option value="party" {{ old('type', $event->type) === 'party' ? 'selected' : '' }}>
                                            Tiệc</option>
                                        <option value="corporate" {{ old('type', $event->type) === 'corporate' ? 'selected' : '' }}>Sự kiện công ty</option>
                                        <option value="other" {{ old('type', $event->type) === 'other' ? 'selected' : '' }}>
                                            Khác</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                                name="description" rows="3">{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="event_date" class="form-label">Ngày diễn ra</label>
                                    <input type="date" class="form-control @error('event_date') is-invalid @enderror" id="event_date"
                                        name="event_date"
                                        value="{{ old('event_date', $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') : '') }}">
                                    @error('event_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expected_guests" class="form-label">Số khách mời dự kiến</label>
                                    <input type="number" class="form-control @error('expected_guests') is-invalid @enderror" id="expected_guests" name="expected_guests" value="{{ old('expected_guests', $event->expected_guests) }}" min="1">
                                    @error('expected_guests')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                                <div class="mb-3">
                                    <label for="venue" class="form-label">Địa điểm</label>
                                    <input type="text" class="form-control @error('venue') is-invalid @enderror" id="venue" name="venue" value="{{ old('venue', $event->venue) }}">
                                    @error('venue')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ chi tiết</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                name="address"
                                rows="2">{{ old('address', $event->address ?? $event->venue_address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="client_name" class="form-label">Tên khách hàng <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                                        id="client_name" name="client_name"
                                        value="{{ old('client_name', $event->client_name) }}" required>
                                    @error('client_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="client_phone" class="form-label">Số điện thoại khách hàng</label>
                                    <input type="text" class="form-control @error('client_phone') is-invalid @enderror"
                                        id="client_phone" name="client_phone"
                                        value="{{ old('client_phone', $event->client_phone) }}">
                                    @error('client_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="client_email" class="form-label">Email khách hàng</label>
                            <input type="email" class="form-control @error('client_email') is-invalid @enderror"
                                id="client_email" name="client_email"
                                value="{{ old('client_email', $event->client_email) }}">
                            @error('client_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status"
                                        name="status" required>
                                        <option value="planning" {{ old('status', $event->status) === 'planning' ? 'selected' : '' }}>Đang lên kế hoạch</option>
                                        <option value="confirmed" {{ old('status', $event->status) === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                        <option value="in_progress" {{ old('status', $event->status) === 'in_progress' ? 'selected' : '' }}>Đang tiến hành</option>
                                        <option value="completed" {{ old('status', $event->status) === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                        <option value="cancelled" {{ old('status', $event->status) === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                        <option value="postponed" {{ old('status', $event->status) === 'postponed' ? 'selected' : '' }}>Tạm hoãn</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                        </div>

                        <div class="mb-3">
                            <label for="special_requirements" class="form-label">Yêu cầu đặc biệt</label>
                            <textarea class="form-control @error('special_requirements') is-invalid @enderror"
                                id="special_requirements" name="special_requirements"
                                rows="2">{{ old('special_requirements', $event->special_requirements) }}</textarea>
                            @error('special_requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                rows="3">{{ old('notes', $event->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="design_deadline" class="form-label">Hạn thiết kế</label>
                                    <input type="date" class="form-control @error('design_deadline') is-invalid @enderror"
                                        id="design_deadline" name="design_deadline"
                                        value="{{ old('design_deadline', $event->design_deadline ? $event->design_deadline->format('Y-m-d') : '') }}">
                                    @error('design_deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="booking_deadline" class="form-label">Hạn đặt chỗ</label>
                                    <input type="date" class="form-control @error('booking_deadline') is-invalid @enderror"
                                        id="booking_deadline" name="booking_deadline"
                                        value="{{ old('booking_deadline', $event->booking_deadline ? $event->booking_deadline->format('Y-m-d') : '') }}">
                                    @error('booking_deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="setup_deadline" class="form-label">Hạn thiết lập</label>
                                    <input type="date" class="form-control @error('setup_deadline') is-invalid @enderror"
                                        id="setup_deadline" name="setup_deadline"
                                        value="{{ old('setup_deadline', $event->setup_deadline ? $event->setup_deadline->format('Y-m-d') : '') }}">
                                    @error('setup_deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="final_deadline" class="form-label">Hạn cuối cùng</label>
                                    <input type="date" class="form-control @error('final_deadline') is-invalid @enderror"
                                        id="final_deadline" name="final_deadline"
                                        value="{{ old('final_deadline', $event->final_deadline ? $event->final_deadline->format('Y-m-d') : '') }}">
                                    @error('final_deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Thông tin bổ sung -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    Tạo lúc: {{ $event->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    <i class="fas fa-edit me-1"></i>
                                    Cập nhật lần cuối: {{ $event->updated_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('events.show', $event->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật sự kiện
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Thống kê nhanh -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-primary">{{ $event->budgets->count() }}</h5>
                            <small class="text-muted">Khoản ngân sách</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-info">{{ $event->timelines->count() }}</h5>
                            <small class="text-muted">Mốc thời gian</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-warning">{{ $event->checklists->count() }}</h5>
                            <small class="text-muted">Công việc</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-success">{{ $event->suppliers->count() }}</h5>
                            <small class="text-muted">Nhà cung cấp</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function (e) {
            const requiredFields = ['name', 'type', 'status'];
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

        // Status change warning
        const originalStatus = '{{ $event->status }}';
        document.getElementById('status').addEventListener('change', function () {
            const newStatus = this.value;

            if (originalStatus === 'completed' && newStatus !== 'completed') {
                if (!confirm('Bạn có chắc chắn muốn thay đổi trạng thái từ "Hoàn thành"? Điều này có thể ảnh hưởng đến báo cáo.')) {
                    this.value = originalStatus;
                }
            }

            if (newStatus === 'cancelled') {
                if (!confirm('Bạn có chắc chắn muốn hủy sự kiện này? Điều này sẽ ảnh hưởng đến tất cả các kế hoạch liên quan.')) {
                    this.value = originalStatus;
                }
            }
        });
    </script>
@endpush