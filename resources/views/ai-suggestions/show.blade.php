@extends('layouts.app')

@section('title', 'Chi tiết gợi ý AI')

@section('page-title', 'Chi tiết gợi ý AI')

@section('page-actions')
    <div class="btn-group" role="group">
        <a href="{{ route('ai-suggestions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
        @if($aiSuggestion->status === 'generated')
            <button type="button" class="btn btn-success" onclick="updateStatus({{ $aiSuggestion->id }}, 'accepted')">
                <i class="fas fa-check me-2"></i>Chấp nhận
            </button>
            <button type="button" class="btn btn-danger" onclick="updateStatus({{ $aiSuggestion->id }}, 'rejected')">
                <i class="fas fa-times me-2"></i>Từ chối
            </button>
        @endif
        <button type="button" class="btn btn-outline-warning" onclick="toggleFavorite({{ $aiSuggestion->id }})">
            <i class="fas fa-star{{ $aiSuggestion->is_favorite ? '' : '-o' }} me-2"></i>
            {{ $aiSuggestion->is_favorite ? 'Bỏ yêu thích' : 'Yêu thích' }}
        </button>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-2">{{ $aiSuggestion->title }}</h1>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-primary">{{ ucfirst($aiSuggestion->suggestion_type) }}</span>
                        <span class="badge bg-{{ $aiSuggestion->status === 'generated' ? 'warning' : ($aiSuggestion->status === 'accepted' ? 'success' : 'danger') }}">
                            {{ ucfirst($aiSuggestion->status) }}
                        </span>
                        <span class="badge bg-info">{{ $aiSuggestion->ai_model }}</span>
                        @if($aiSuggestion->confidence_score)
                            <span class="badge bg-secondary">
                                Độ tin cậy: {{ number_format($aiSuggestion->confidence_score * 100, 1) }}%
                            </span>
                        @endif
                        @if($aiSuggestion->is_favorite)
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-star me-1"></i>Yêu thích
                            </span>
                        @endif
                    </div>
                    <div class="text-muted">
                        <small>
                            <i class="fas fa-calendar me-1"></i>
                            Tạo lúc: {{ $aiSuggestion->created_at->format('d/m/Y H:i') }}
                            ({{ $aiSuggestion->created_at->diffForHumans() }})
                        </small>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    @if($aiSuggestion->event)
                        <div class="card border-0 bg-light">
                            <div class="card-body p-3">
                                <h6 class="card-title mb-1">Sự kiện liên quan</h6>
                                <p class="card-text">
                                    <strong>{{ $aiSuggestion->event->name }}</strong><br>
                                    <small class="text-muted">{{ $aiSuggestion->event->type }}</small>
                                </p>
                                <a href="{{ route('events.show', $aiSuggestion->event) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>Xem sự kiện
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- AI Suggestion Content -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-robot me-2"></i>Nội dung gợi ý AI
                    </h5>
                </div>
                <div class="card-body">
                    <div class="ai-content">
                        {!! nl2br(e($aiSuggestion->content)) !!}
                    </div>
                </div>
            </div>

            <!-- User Feedback -->
            @if($aiSuggestion->user_feedback)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-comment me-2"></i>Phản hồi của người dùng
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $aiSuggestion->user_feedback }}</p>
                    </div>
                </div>
            @endif

            <!-- Implementation Notes -->
            @if($aiSuggestion->implementation_notes)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>Ghi chú triển khai
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $aiSuggestion->implementation_notes }}</p>
                    </div>
                </div>
            @endif


        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Thông tin chi tiết
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-0 text-primary">{{ $aiSuggestion->ai_model }}</div>
                                <small class="text-muted">AI Model</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-0 text-info">
                                    {{ $aiSuggestion->confidence_score ? number_format($aiSuggestion->confidence_score * 100, 1) . '%' : 'N/A' }}
                                </div>
                                <small class="text-muted">Độ tin cậy</small>
                            </div>
                        </div>
                        @if($aiSuggestion->rating)
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-warning">
                                        {{ $aiSuggestion->rating }}/5
                                    </div>
                                    <small class="text-muted">Đánh giá</small>
                                </div>
                            </div>
                        @endif
                        @if($aiSuggestion->estimated_cost)
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-success">
                                        {{ number_format($aiSuggestion->estimated_cost) }}đ
                                    </div>
                                    <small class="text-muted">Chi phí ước tính</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Input Parameters -->
            @if($aiSuggestion->input_parameters)
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>Tham số đầu vào
                        </h6>
                    </div>
                    <div class="card-body">
                        <pre class="bg-light p-3 rounded"><code>{{ json_encode($aiSuggestion->input_parameters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                </div>
            @endif

            <!-- Related Suppliers -->
            @if($aiSuggestion->related_suppliers)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-truck me-2"></i>Nhà cung cấp liên quan
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($aiSuggestion->related_suppliers as $supplier)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $supplier['name'] ?? 'N/A' }}</span>
                                @if(isset($supplier['contact']))
                                    <small class="text-muted">{{ $supplier['contact'] }}</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tags -->
            @if($aiSuggestion->tags)
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-tags me-2"></i>Tags
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($aiSuggestion->tags as $tag)
                            <span class="badge bg-secondary me-1 mb-1">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStatus(suggestionId, status) {
    if (confirm('Bạn có chắc chắn muốn ' + (status === 'accepted' ? 'chấp nhận' : 'từ chối') + ' gợi ý này?')) {
        fetch(`{{ url('/ai-suggestions') }}/${suggestionId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật trạng thái');
        });
    }
}

function toggleFavorite(suggestionId) {
    fetch(`{{ url('/ai-suggestions') }}/${suggestionId}/favorite`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật yêu thích');
    });
}


</script>

<style>
.ai-content {
    line-height: 1.8;
    font-size: 1.05em;
}

.ai-content h1, .ai-content h2, .ai-content h3, .ai-content h4, .ai-content h5, .ai-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.ai-content ul, .ai-content ol {
    margin-left: 1.5rem;
    margin-bottom: 1rem;
}

.ai-content li {
    margin-bottom: 0.5rem;
}

.ai-content strong {
    color: #2c3e50;
}

.ai-content code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.9em;
}

.ai-content pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
}
</style>
@endpush 