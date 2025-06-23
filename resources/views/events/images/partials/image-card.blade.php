<div class="card image-card h-100">
    @if(in_array(strtolower(pathinfo($image->original_filename, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff']))
        <!-- Hiển thị ảnh preview -->
        <img src="{{ $image->file_url }}" 
             alt="{{ $image->original_filename }}" 
             class="image-thumbnail"
             onclick="viewImage('{{ $image->file_url }}', '{{ $image->original_filename }}', '{{ $image->description ?? '' }}')"
             loading="lazy">
    @else
        <!-- Hiển thị icon cho file không phải ảnh -->
        <div class="file-icon" onclick="window.open('{{ $image->file_url }}', '_blank')">
            <div class="text-center">
                @php
                    $extension = strtolower(pathinfo($image->original_filename, PATHINFO_EXTENSION));
                    $iconClass = 'fas fa-file';
                    switch($extension) {
                        case 'ai':
                            $iconClass = 'fab fa-adobe';
                            break;
                        case 'psd':
                            $iconClass = 'far fa-file-image';
                            break;
                        case 'eps':
                        case 'svg':
                            $iconClass = 'fas fa-vector-square';
                            break;
                        case 'pdf':
                            $iconClass = 'fas fa-file-pdf';
                            break;
                        default:
                            $iconClass = 'fas fa-file-image';
                    }
                @endphp
                <i class="{{ $iconClass }} fa-3x mb-2"></i>
                <div class="fw-bold">.{{ strtoupper($extension) }}</div>
                <div class="small">{{ $image->file_size_human }}</div>
            </div>
        </div>
    @endif
    
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="card-title mb-0 text-truncate" title="{{ $image->original_filename }}">
                {{ Str::limit($image->original_filename, 20) }}
            </h6>
            <span class="badge bg-{{ $image->image_type === 'nghiem_thu' ? 'success' : 'warning' }} badge-category">
                {{ $image->image_type_display }}
            </span>
        </div>
        
        @if($image->category)
            <div class="mb-2">
                <span class="badge bg-secondary badge-category">
                    {{ $image->category }}
                </span>
            </div>
        @endif
        
        @if($image->description)
            <p class="card-text small text-muted mb-2" title="{{ $image->description }}">
                {{ Str::limit($image->description, 50) }}
            </p>
        @endif
        
        <div class="d-flex justify-content-between align-items-center text-muted small">
            <span>{{ $image->file_size_human }}</span>
            <span>{{ $image->created_at->format('d/m/Y') }}</span>
        </div>
        
        <div class="mt-2">
            <div class="btn-group btn-group-sm w-100" role="group">
                @if(in_array(strtolower(pathinfo($image->original_filename, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff']))
                    <button type="button" class="btn btn-outline-primary" 
                            onclick="viewImage('{{ $image->file_url }}', '{{ $image->original_filename }}', '{{ $image->description ?? '' }}')">
                        <i class="fas fa-eye"></i>
                    </button>
                @endif
                <a href="{{ $image->file_url }}" download="{{ $image->original_filename }}" class="btn btn-outline-success">
                    <i class="fas fa-download"></i>
                </a>
                <button type="button" class="btn btn-outline-danger" 
                        onclick="deleteImage({{ $image->event_id }}, {{ $image->id }}, '{{ $image->original_filename }}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div> 