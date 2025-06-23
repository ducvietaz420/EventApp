<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventImage;
use App\Models\Checklist;
use App\Models\AiSuggestion;

use App\Exports\EventsExport;
use App\Exports\EventDetailExport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

/**
 * Controller quản lý sự kiện
 * Xử lý các thao tác CRUD cho sự kiện
 */
class EventController extends Controller
{
    /**
     * Constructor - Yêu cầu authentication cho tất cả routes
     */
    public function __construct()
    {
        $this->middleware(['auth', 'check.user.status']);
    }
    /**
     * Hiển thị danh sách tất cả sự kiện
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Event::with(['images', 'checklists']);
        
        // Lọc theo trạng thái nếu có
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Lọc theo loại sự kiện nếu có
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Tìm kiếm theo tên
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Sắp xếp theo ngày tạo mới nhất
        $events = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Trả về JSON nếu là API request
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $events,
                'message' => 'Danh sách sự kiện được tải thành công'
            ]);
        }
        
        return view('events.index', compact('events'));
    }

    /**
     * Hiển thị form tạo sự kiện mới
     */
    public function create(): View
    {
        return view('events.create');
    }

    /**
     * Lưu sự kiện mới vào database
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['wedding', 'conference', 'party', 'corporate', 'exhibition', 'other'])],
            'status' => ['required', Rule::in(['planning', 'confirmed', 'in_progress', 'completed', 'cancelled'])],
            'event_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'expected_guests' => 'nullable|integer|min:1',
            // 'budget' => 'nullable|numeric|min:0', // Đã xóa trường budget
            'notes' => 'nullable|string',
            'deadline_design' => 'nullable|date',
            'deadline_booking' => 'nullable|date',
            'deadline_final' => 'nullable|date'
        ]);
        
        // Xử lý trường expected_guests để tránh lỗi database constraint
        if (is_null($validated['expected_guests']) || $validated['expected_guests'] === '') {
            $validated['expected_guests'] = 0;
        }
        
        $event = Event::create($validated);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $event,
                'message' => 'Sự kiện đã được tạo thành công'
            ], 201);
        }
        
        return redirect()->route('events.show', $event)
                        ->with('success', 'Sự kiện đã được tạo thành công!');
    }

    /**
     * Hiển thị chi tiết một sự kiện
     */
    public function show(Event $event): View|JsonResponse
    {
        $event->load([
            'images' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'nghiemThuImages' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'thietKeImages' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'checklists' => function($query) {
                $query->orderBy('due_date', 'asc');
            },
            'aiSuggestions' => function($query) {
                $query->where('status', '!=', 'rejected')
                      ->orderBy('created_at', 'desc');
            }
        ]);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $event,
                'message' => 'Chi tiết sự kiện được tải thành công'
            ]);
        }
        
        return view('events.show', compact('event'));
    }

    /**
     * Hiển thị form chỉnh sửa sự kiện
     */
    public function edit(Event $event): View
    {
        return view('events.edit', compact('event'));
    }

    /**
     * Cập nhật thông tin sự kiện
     */
    public function update(Request $request, Event $event): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['wedding', 'conference', 'party', 'corporate', 'exhibition', 'concert', 'other'])],
            'status' => ['required', Rule::in(['planning', 'confirmed', 'in_progress', 'completed', 'cancelled', 'postponed'])],
            'event_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'expected_attendees' => 'nullable|integer|min:1',
            'actual_attendees' => 'nullable|integer|min:0',
            // 'budget' => 'nullable|numeric|min:0', // Đã xóa trường budget
            'actual_cost' => 'nullable|numeric|min:0',
            'special_requirements' => 'nullable|string',
            'notes' => 'nullable|string',
            'design_deadline' => 'nullable|date',
            'booking_deadline' => 'nullable|date',
            'setup_deadline' => 'nullable|date',
            'final_deadline' => 'nullable|date'
        ]);
        
        $event->update($validated);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $event->fresh(),
                'message' => 'Sự kiện đã được cập nhật thành công'
            ]);
        }
        
        return redirect()->route('events.show', $event)
                        ->with('success', 'Sự kiện đã được cập nhật thành công!');
    }

    /**
     * Cập nhật trạng thái của sự kiện.
     */
    public function updateStatus(Request $request, Event $event): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['planning', 'confirmed', 'in_progress', 'completed', 'cancelled'])],
        ]);

        $event->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $event->fresh(),
                'message' => 'Trạng thái sự kiện đã được cập nhật thành công.'
            ]);
        }

        return back()->with('success', 'Trạng thái sự kiện đã được cập nhật thành công!');
    }

    /**
     * Xóa sự kiện
     */
    public function destroy(Event $event): RedirectResponse|JsonResponse
    {
        $event->delete();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sự kiện đã được xóa thành công'
            ]);
        }
        
        return redirect()->route('events.index')
                        ->with('success', 'Sự kiện đã được xóa thành công!');
    }

    /**
     * Hiển thị dashboard tổng quan
     */
    public function dashboard(Request $request): View|JsonResponse
    {
        // Lấy thống kê tổng quan sự kiện
        $totalEvents = Event::count();
        $completedEvents = Event::where('status', 'completed')->count();
        $activeEvents = Event::where('status', 'in_progress')->count();
        $inProgressEvents = $activeEvents; // Thêm biến này
        $planningEvents = Event::where('status', 'planning')->count();
        $upcomingEvents = Event::where('event_date', '>', now())->count();
        
        // Thống kê Ảnh
        $totalImages = EventImage::count();
        $nghiemThuImages = EventImage::nghiemThu()->count();
        $thietKeImages = EventImage::thietKe()->count();
        
        // Thống kê Checklist
        $totalChecklists = Checklist::count();
        $completedChecklists = Checklist::completed()->count();
        $pendingChecklists = Checklist::pending()->count();
        $overdueChecklists = Checklist::overdue()->count();
        
        // Thống kê AI Suggestions
        $totalSuggestions = AiSuggestion::count();
        $acceptedSuggestions = AiSuggestion::accepted()->count();
        $pendingSuggestions = AiSuggestion::pending()->count();
        $highConfidenceSuggestions = AiSuggestion::highConfidence()->count();
        

        
        // Lấy dữ liệu cho hiển thị
        $recentEvents = Event::with(['images', 'checklists', 'aiSuggestions'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                            
        $upcomingEventsList = Event::with(['images', 'checklists'])
                            ->where('event_date', '>', now())
                            ->orderBy('event_date', 'asc')
                            ->limit(5)
                            ->get();
        
        // Tasks cần chú ý - chỉ lấy từ Checklist
        $overdueChecklists = Checklist::where('due_date', '<', now())
                                     ->where('status', '!=', 'completed')
                                     ->with('event')
                                     ->limit(10)
                                     ->get();
        
        $pendingTasks = $overdueChecklists->map(function($item) {
            $item->type = 'checklist';
            $item->urgency = 'overdue';
            $item->priority = $item->priority ?? 'medium';
            return $item;
        })->sortBy('due_date');
        
        // Gợi ý AI mới nhất
        $recentSuggestions = AiSuggestion::with('event')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(5)
                                        ->get();
        
        // Thống kê ảnh theo sự kiện
        $eventsWithImages = Event::has('images')->count();
        $averageImagesPerEvent = $totalImages > 0 && $totalEvents > 0 ? round($totalImages / $totalEvents, 1) : 0;
        
        // Dữ liệu thực cho biểu đồ sự kiện theo tháng (12 tháng gần nhất)
        $monthlyData = [];
        $monthlyLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');
            $monthlyData[] = Event::whereYear('created_at', $date->year)
                                  ->whereMonth('created_at', $date->month)
                                  ->count();
        }
        
        // Dữ liệu thực cho biểu đồ phân bố loại sự kiện
        $eventTypes = Event::selectRaw('type, COUNT(*) as count')
                          ->groupBy('type')
                          ->pluck('count', 'type')
                          ->toArray();
        
        $typeLabels = [];
        $typeData = [];
        $typeDisplayMap = [
            'wedding' => 'Đám cưới',
            'conference' => 'Hội nghị',
            'party' => 'Tiệc',
            'corporate' => 'Doanh nghiệp',
            'exhibition' => 'Triển lãm',
            'workshop' => 'Workshop',
            'seminar' => 'Seminar',
            'other' => 'Khác'
        ];
        
        foreach ($eventTypes as $type => $count) {
            $typeLabels[] = $typeDisplayMap[$type] ?? ucfirst($type);
            $typeData[] = $count;
        }
        
        // Nếu không có dữ liệu, sử dụng dữ liệu trống thay vì dữ liệu giả
        if (empty($typeLabels)) {
            $typeLabels = ['Chưa có dữ liệu'];
            $typeData = [0];
        }
        
        // Nếu là API request, trả về JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'events' => [
                        'total' => $totalEvents,
                        'completed' => $completedEvents,
                        'active' => $activeEvents,
                        'planning' => $planningEvents,
                        'upcoming' => $upcomingEvents,
                    ],
                    'images' => [
                        'total' => $totalImages,
                        'nghiem_thu' => $nghiemThuImages,
                        'thiet_ke' => $thietKeImages,
                        'events_with_images' => $eventsWithImages,
                        'average_per_event' => $averageImagesPerEvent,
                    ],
                    'checklists' => [
                        'total' => $totalChecklists,
                        'completed' => $completedChecklists,
                        'pending' => $pendingChecklists,
                        'overdue' => $overdueChecklists,
                    ],
                    'suggestions' => [
                        'total' => $totalSuggestions,
                        'accepted' => $acceptedSuggestions,
                        'pending' => $pendingSuggestions,
                        'high_confidence' => $highConfidenceSuggestions,
                    ],

                    'charts' => [
                        'monthly_labels' => $monthlyLabels,
                        'monthly_data' => $monthlyData,
                        'type_labels' => $typeLabels,
                        'type_data' => $typeData,
                    ],
                    'recent_events' => $recentEvents,
                    'upcoming_events' => $upcomingEventsList,
                    'pending_tasks' => $pendingTasks,
                    'recent_suggestions' => $recentSuggestions,
                ],
                'message' => 'Thống kê dashboard được tải thành công'
            ]);
        }
        
        // Trả về view dashboard với tất cả dữ liệu thực
        return view('dashboard', compact(
            'totalEvents', 'completedEvents', 'activeEvents', 'inProgressEvents', 'planningEvents', 'upcomingEvents',
            'totalImages', 'nghiemThuImages', 'thietKeImages', 'eventsWithImages', 'averageImagesPerEvent',
            'totalChecklists', 'completedChecklists', 'pendingChecklists', 'overdueChecklists',
            'totalSuggestions', 'acceptedSuggestions', 'pendingSuggestions', 'highConfidenceSuggestions',
            'recentEvents', 'upcomingEventsList', 'pendingTasks', 'recentSuggestions',
            'monthlyLabels', 'monthlyData', 'typeLabels', 'typeData'
        ));
    }

    /**
     * Xuất danh sách sự kiện ra file Excel
     */
    public function exportEvents(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');

        $filename = 'danh-sach-su-kien-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

        return Excel::download(
            new EventsExport($startDate, $endDate, $status),
            $filename
        );
    }

    /**
     * Xuất báo cáo chi tiết sự kiện ra file Excel
     */
    public function exportEventDetail(Event $event)
    {
        $filename = 'bao-cao-su-kien-' . $event->id . '-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

        return Excel::download(
            new EventDetailExport($event),
            $filename
        );
    }

    /**
     * Hiển thị trang quản lý ảnh cho sự kiện
     */
    public function imagesIndex(Event $event): View
    {
        // Load tất cả các relationship cần thiết
        $event->load(['images', 'nghiemThuImages', 'thietKeImages']);
        
        return view('events.images.index', compact('event'));
    }

    /**
     * Upload ảnh cho sự kiện
     */
    public function uploadImages(Request $request, Event $event): RedirectResponse|JsonResponse
    {
        // Custom validation dựa trên loại file
        $imageType = $request->input('image_type');
        
        if ($imageType === 'nghiem_thu') {
            // Ảnh nghiệm thu - chỉ chấp nhận file ảnh
            $request->validate([
                'images' => 'required|array|min:1',
                'images.*' => 'required|file|mimes:jpeg,png,jpg,gif,bmp,tiff|max:10240', // 10MB
                'image_type' => 'required|in:nghiem_thu,thiet_ke',
                'category' => 'nullable|string|max:100',
                'description' => 'nullable|string|max:500'
            ]);
        } else {
            // File thiết kế - chấp nhận cả ảnh và file thiết kế
            $request->validate([
                'images' => 'required|array|min:1',
                'images.*' => 'required|file|mimes:jpeg,png,jpg,gif,bmp,tiff,ai,psd,eps,svg,pdf|max:51200', // 50MB
                'image_type' => 'required|in:nghiem_thu,thiet_ke',
                'category' => 'nullable|string|max:100',
                'description' => 'nullable|string|max:500'
            ]);
        }

        $uploadedImages = [];
        
        foreach ($request->file('images') as $file) {
            // Kiểm tra kích thước file dựa trên loại
            $maxFileSize = $imageType === 'nghiem_thu' ? 10 * 1024 * 1024 : 50 * 1024 * 1024; // 10MB cho ảnh, 50MB cho thiết kế
            
            if ($file->getSize() > $maxFileSize) {
                $maxSizeMB = $maxFileSize / 1024 / 1024;
                return redirect()->back()
                    ->withErrors(['images' => "File {$file->getClientOriginalName()} có kích thước vượt quá {$maxSizeMB}MB"])
                    ->withInput();
            }
            
            $uploadedImage = $this->storeImage(
                $file, 
                $event, 
                $request->input('image_type'), 
                $request->input('category'),
                $request->input('description')
            );
            $uploadedImages[] = $uploadedImage;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $uploadedImages,
                'message' => 'File đã được upload thành công!'
            ]);
        }

        $fileCount = count($uploadedImages);
        $message = $imageType === 'nghiem_thu' 
            ? "Đã upload {$fileCount} ảnh nghiệm thu thành công!" 
            : "Đã upload {$fileCount} file thiết kế thành công!";

        return redirect()->route('events.images.index', $event)
                        ->with('success', $message);
    }

    /**
     * Xóa ảnh
     */
    public function deleteImage(Event $event, EventImage $image): RedirectResponse|JsonResponse
    {
        if ($image->event_id !== $event->id) {
            abort(404);
        }

        // Xóa file khỏi storage
        if (Storage::disk('public')->exists($image->file_path)) {
            Storage::disk('public')->delete($image->file_path);
        }

        // Xóa record khỏi database
        $image->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ảnh đã được xóa thành công!'
            ]);
        }

        return redirect()->route('events.images.index', $event)
                        ->with('success', 'Ảnh đã được xóa thành công!');
    }

    /**
     * Download tất cả ảnh của sự kiện dưới dạng file ZIP
     */
    public function downloadImagesZip(Event $event)
    {
        $images = $event->images;
        
        if ($images->isEmpty()) {
            return redirect()->back()->with('error', 'Sự kiện này chưa có ảnh nào!');
        }

        // Tạo tên file ZIP
        $zipFileName = $this->sanitizeFileName($event->name) . '-' . now()->format('Y-m-d-H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Tạo thư mục temp nếu chưa có
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            // Tạo cấu trúc thư mục: Tên sự kiện -> Tên khách hàng -> Ngày sự kiện
            $eventName = $this->sanitizeFileName($event->name);
            $clientName = $this->sanitizeFileName($event->client_name ?: 'Khach-hang');
            $eventDate = $event->event_date ? $event->event_date->format('d-m-Y') : 'Chua-xac-dinh';
            
            $basePath = $eventName . '/' . $clientName . '/' . $eventDate;
            
            // Tạo thư mục con trong ZIP
            $zip->addEmptyDir($basePath . '/Thiet-ke');
            $zip->addEmptyDir($basePath . '/Nghiem-thu');

            foreach ($images as $image) {
                $filePath = storage_path('app/public/' . $image->file_path);
                
                if (file_exists($filePath)) {
                    if ($image->image_type === 'nghiem_thu') {
                        $folderPath = $basePath . '/Nghiem-thu';
                    } else {
                        // Ảnh thiết kế - tạo thư mục con theo category
                        $folderPath = $basePath . '/Thiet-ke';
                        if ($image->category) {
                            $categoryName = $this->getCategoryDisplayName($image->category);
                            $folderPath .= '/' . $categoryName;
                            // Tạo thư mục category nếu chưa có
                            $zip->addEmptyDir($folderPath);
                        }
                    }
                    
                    $zipFilePath = $folderPath . '/' . $this->sanitizeFileName($image->original_filename);
                    $zip->addFile($filePath, $zipFilePath);
                }
            }
            
            $zip->close();

            // Download file và xóa file tạm
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'Không thể tạo file ZIP!');
    }

    /**
     * Lưu ảnh vào storage và database
     */
    private function storeImage(UploadedFile $file, Event $event, string $imageType, ?string $category = null, ?string $description = null): EventImage
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . uniqid() . '.' . $extension;
        
        // Lưu file vào storage/app/public/events/{event_id}/{image_type}/
        $directory = "events/{$event->id}/{$imageType}";
        $filePath = $file->storeAs($directory, $filename, 'public');
        
        // Tạo record trong database
        return EventImage::create([
            'event_id' => $event->id,
            'filename' => $filename,
            'original_filename' => $originalName,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'image_type' => $imageType,
            'category' => $category,
            'description' => $description
        ]);
    }

    /**
     * Làm sạch tên file để sử dụng trong ZIP - hỗ trợ tiếng Việt
     */
    private function sanitizeFileName(string $filename): string
    {
        // Bước 1: Sử dụng transliterator để chuyển đổi ký tự có dấu thành không dấu
        if (class_exists('Transliterator')) {
            $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove');
            if ($transliterator) {
                $filename = $transliterator->transliterate($filename);
            }
        } else {
            // Fallback: Manual mapping cho tiếng Việt
            $vietnamese = [
                'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
                'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
                'ì', 'í', 'ị', 'ỉ', 'ĩ',
                'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
                'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
                'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
                'đ',
                'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
                'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
                'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
                'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
                'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
                'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
                'Đ'
            ];
            
            $latin = [
                'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
                'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
                'i', 'i', 'i', 'i', 'i',
                'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
                'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
                'y', 'y', 'y', 'y', 'y',
                'd',
                'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A',
                'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E',
                'I', 'I', 'I', 'I', 'I',
                'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O',
                'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U',
                'Y', 'Y', 'Y', 'Y', 'Y',
                'D'
            ];
            
            $filename = str_replace($vietnamese, $latin, $filename);
        }
        
        // Bước 2: Loại bỏ các ký tự đặc biệt (giữ lại chữ cái, số, dấu cách, gạch ngang, gạch dưới, dấu chấm)
        $filename = preg_replace('/[^a-zA-Z0-9\s\-_.]/', '', $filename);
        
        // Bước 3: Thay thế nhiều dấu cách liên tiếp thành một dấu gạch ngang
        $filename = preg_replace('/\s+/', '-', $filename);
        
        // Bước 4: Loại bỏ dấu gạch ngang ở đầu và cuối
        $filename = trim($filename, '-');
        
        // Bước 5: Giới hạn độ dài tên file (tối đa 100 ký tự)
        if (strlen($filename) > 100) {
            $filename = substr($filename, 0, 100);
            $filename = rtrim($filename, '-');
        }
        
        // Bước 6: Nếu tên file rỗng, trả về 'unknown'
        return $filename ?: 'unknown';
    }

    /**
     * Lấy tên hiển thị cho category
     */
    private function getCategoryDisplayName(string $category): string
    {
        $categoryMap = [
            'backdrop' => 'Backdrop',
            'led' => 'LED',
            'san-khau' => 'San-khau',
            'standee' => 'Standee',
            'trang-tri' => 'Trang-tri',
            'am-thanh' => 'Am-thanh',
            'anh-sang' => 'Anh-sang',
            'khac' => 'Khac'
        ];

        return $categoryMap[$category] ?? ucfirst($this->sanitizeFileName($category));
    }
}
