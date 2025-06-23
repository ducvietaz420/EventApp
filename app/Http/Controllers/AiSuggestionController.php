<?php

namespace App\Http\Controllers;

use App\Models\AiSuggestion;
use App\Models\Event;
use App\Models\Budget;
use App\Models\Timeline;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class AiSuggestionController extends Controller
{
    /**
     * Constructor - Yêu cầu authentication
     */
    public function __construct()
    {
        $this->middleware(['auth', 'check.user.status']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Event $event = null): View
    {
        $query = AiSuggestion::query();
        
        // Nếu có sự kiện cụ thể, lọc AI suggestions theo sự kiện đó
        if ($event) {
            $query->where('event_id', $event->id);
        }
        
        // Áp dụng các bộ lọc tìm kiếm
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('suggestion_type') && $request->suggestion_type) {
            $query->bySuggestionType($request->suggestion_type);
        }
        
        if ($request->has('status') && $request->status) {
            $query->byStatus($request->status);
        }
        
        if ($request->has('confidence') && $request->confidence) {
            if ($request->confidence === 'high') {
                $query->highConfidence();
            }
        }
        
        // Sắp xếp theo độ tin cậy và thời gian tạo
        $query->orderByConfidence('desc')
              ->orderBy('created_at', 'desc');
        
        $suggestions = $query->with('event')->paginate(15);
        $events = Event::all();
        
        // Thống kê
        $stats = [
            'total' => $query->count(),
            'high_confidence' => AiSuggestion::highConfidence()->count(),
            'accepted' => AiSuggestion::accepted()->count(),
            'pending' => AiSuggestion::pending()->count(),
        ];
        
        return view('ai-suggestions.index', compact('suggestions', 'event', 'events', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Event $event = null): View
    {
        $events = Event::all();
        
        return view('ai-suggestions.create', compact('events', 'event'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'suggestion_type' => 'required|string|in:budget,timeline,checklist,supplier,general',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'details' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);
        
        $suggestion = AiSuggestion::create(array_merge($validated, [
            'status' => 'generated',
            'ai_model' => 'manual',
            'confidence_score' => 1.0
        ]));
        
        return redirect()->route('ai-suggestions.show', $suggestion)
                         ->with('success', 'Gợi ý đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(AiSuggestion $aiSuggestion): View
    {
        $aiSuggestion->load('event');
        
        // Lấy các gợi ý liên quan
        $relatedSuggestions = AiSuggestion::where('event_id', $aiSuggestion->event_id)
                                         ->where('id', '!=', $aiSuggestion->id)
                                         ->where('suggestion_type', $aiSuggestion->suggestion_type)
                                         ->orderByConfidence('desc')
                                         ->limit(5)
                                         ->get();
        
        return view('ai-suggestions.show', compact('aiSuggestion', 'relatedSuggestions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AiSuggestion $aiSuggestion): View
    {
        $events = Event::all();
        
        return view('ai-suggestions.edit', compact('aiSuggestion', 'events'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AiSuggestion $aiSuggestion): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'suggestion_type' => 'required|string|in:budget,timeline,checklist,supplier,general',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'details' => 'nullable|array',
            'tags' => 'nullable|array',
            'status' => 'nullable|string|in:pending,accepted,rejected,implemented,under_review',
            'implementation_notes' => 'nullable|string',
        ]);
        
        $aiSuggestion->update($validated);
        
        return redirect()->route('ai-suggestions.show', $aiSuggestion)
                         ->with('success', 'Gợi ý AI đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AiSuggestion $aiSuggestion): RedirectResponse
    {
        $eventId = $aiSuggestion->event_id;
        $aiSuggestion->delete();
        
        return redirect()->route('events.show', $eventId)
                         ->with('success', 'Gợi ý AI đã được xóa thành công!');
    }
    
    /**
     * Generate AI suggestions using Gemini API
     */
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'suggestion_type' => 'required|string|in:budget,timeline,checklist,supplier,general',
            'prompt' => 'nullable|string',
        ]);
        
        $event = Event::with(['budgets', 'timelines', 'checklists'])->findOrFail($validated['event_id']);
        
        try {
            // Chuẩn bị dữ liệu context cho AI
            $context = $this->prepareEventContext($event);
            
            // Tạo prompt dựa trên loại gợi ý
            $prompt = $this->generatePrompt($validated['suggestion_type'], $context, $validated['prompt'] ?? null);
            
            // Gọi Gemini API
            $geminiResponse = $this->callGeminiAPI($prompt);
            
            if (!$geminiResponse) {
                // Kiểm tra xem có phải do thiếu API key không
                $apiKey = env('GEMINI_API_KEY');
                if (!$apiKey) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Chưa cấu hình GEMINI_API_KEY. Vui lòng thêm GEMINI_API_KEY vào file .env để sử dụng tính năng gợi ý AI.',
                        'error_type' => 'missing_api_key'
                    ], 400);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể kết nối với AI service. Vui lòng kiểm tra kết nối mạng và thử lại.',
                    'error_type' => 'api_connection_error'
                ], 500);
            }
            
            // Xử lý response và tạo suggestion
            $suggestion = $this->processSuggestionResponse($event->id, $validated['suggestion_type'], $geminiResponse);
            
            return response()->json([
                'success' => true,
                'message' => 'Gợi ý AI đã được tạo thành công!',
                'suggestion' => $suggestion
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo gợi ý AI: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update suggestion status
     */
    public function updateStatus(Request $request, AiSuggestion $aiSuggestion): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,accepted,rejected,implemented,under_review',
            'notes' => 'nullable|string'
        ]);
        
        $aiSuggestion->status = $validated['status'];
        
        if ($validated['status'] === 'accepted' || $validated['status'] === 'implemented') {
            $aiSuggestion->reviewed_at = now();
            $aiSuggestion->reviewed_by = 'System'; // Có thể thay bằng user hiện tại
        }
        
        if (isset($validated['notes'])) {
            $aiSuggestion->implementation_notes = $validated['notes'];
        }
        
        $aiSuggestion->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Trạng thái gợi ý đã được cập nhật!',
            'suggestion' => $aiSuggestion
        ]);
    }
    
    /**
     * Rate suggestion
     */
    public function rate(Request $request, AiSuggestion $aiSuggestion): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string'
        ]);
        
        $aiSuggestion->rating = $validated['rating'];
        if (isset($validated['feedback'])) {
            $aiSuggestion->user_feedback = $validated['feedback'];
        }
        
        $aiSuggestion->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Đánh giá đã được lưu!',
            'suggestion' => $aiSuggestion
        ]);
    }
    
    /**
     * Toggle favorite status
     */
    public function toggleFavorite(AiSuggestion $aiSuggestion): JsonResponse
    {
        $aiSuggestion->is_favorite = !$aiSuggestion->is_favorite;
        $aiSuggestion->save();
        
        return response()->json([
            'success' => true,
            'message' => $aiSuggestion->is_favorite ? 'Đã thêm vào yêu thích!' : 'Đã xóa khỏi yêu thích!',
            'is_favorite' => $aiSuggestion->is_favorite
        ]);
    }
    
    /**
     * Accept suggestion
     */
    public function accept(Event $event, AiSuggestion $aiSuggestion): RedirectResponse
    {
        $aiSuggestion->status = 'accepted';
        $aiSuggestion->reviewed_at = now();
        $aiSuggestion->reviewed_by = 'System'; // Có thể thay bằng user hiện tại
        $aiSuggestion->save();
        
        return redirect()->route('events.show', $event)
                         ->with('success', 'Gợi ý AI đã được chấp nhận!');
    }
    
    /**
     * Reject suggestion
     */
    public function reject(Event $event, AiSuggestion $aiSuggestion): RedirectResponse
    {
        $aiSuggestion->status = 'rejected';
        $aiSuggestion->reviewed_at = now();
        $aiSuggestion->reviewed_by = 'System'; // Có thể thay bằng user hiện tại
        $aiSuggestion->save();
        
        return redirect()->route('events.show', $event)
                         ->with('success', 'Gợi ý AI đã được từ chối!');
    }
    
    /**
     * Prepare event context for AI
     */
    private function prepareEventContext(Event $event): array
    {
        return [
            'event' => [
                'name' => $event->name,
                'description' => $event->description,
                'type' => $event->type,
                'start_date' => $event->start_date ? $event->start_date->format('Y-m-d') : null,
                'end_date' => $event->end_date ? $event->end_date->format('Y-m-d') : null,
                'location' => $event->location,
                'expected_attendees' => $event->expected_attendees,
                'estimated_budget' => $event->estimated_budget,
            ],
            'budgets' => $event->budgets->map(function($budget) {
                return [
                    'category' => $budget->category,
                    'estimated_cost' => $budget->estimated_cost,
                    'actual_cost' => $budget->actual_cost,
                ];
            })->toArray(),
            'timelines' => $event->timelines->map(function($timeline) {
                return [
                    'title' => $timeline->title,
                    'start_time' => $timeline->start_time ? $timeline->start_time->format('Y-m-d H:i') : null,
                    'end_time' => $timeline->end_time ? $timeline->end_time->format('Y-m-d H:i') : null,
                    'status' => $timeline->status,
                ];
            })->toArray(),
            'checklists' => $event->checklists->map(function($checklist) {
                return [
                    'title' => $checklist->title,
                    'category' => $checklist->category,
                    'priority' => $checklist->priority,
                    'status' => $checklist->status,
                ];
            })->toArray(),
        ];
    }
    
    /**
     * Generate prompt based on suggestion type
     */
    private function generatePrompt(string $type, array $context, ?string $customPrompt = null): string
    {
        $basePrompt = "Bạn là một chuyên gia tổ chức sự kiện. Dựa trên thông tin sự kiện sau:\n\n";
        $basePrompt .= "Tên sự kiện: " . $context['event']['name'] . "\n";
        $basePrompt .= "Loại sự kiện: " . $context['event']['type'] . "\n";
        $basePrompt .= "Mô tả: " . $context['event']['description'] . "\n";
        $basePrompt .= "Số người tham dự dự kiến: " . ($context['event']['expected_attendees'] ?? 'Chưa xác định') . "\n";
        $basePrompt .= "Ngân sách ước tính: " . ($context['event']['estimated_budget'] ?? 'Chưa xác định') . "\n\n";
        
        switch ($type) {
            case 'budget':
                $basePrompt .= "Hãy đưa ra gợi ý về ngân sách chi tiết cho sự kiện này. Bao gồm các khoản chi chính, ước tính chi phí và lời khuyên tiết kiệm.";
                break;
            case 'timeline':
                $basePrompt .= "Hãy đưa ra gợi ý về timeline chi tiết cho sự kiện này. Bao gồm các mốc thời gian quan trọng, thứ tự công việc và thời gian thực hiện.";
                break;
            case 'checklist':
                $basePrompt .= "Hãy đưa ra gợi ý về checklist chi tiết cho sự kiện này. Bao gồm các công việc cần làm, thứ tự ưu tiên và người phụ trách.";
                break;
            case 'supplier':
                $basePrompt .= "Hãy đưa ra gợi ý về các nhà cung cấp và dịch vụ cần thiết cho sự kiện này. Bao gồm loại dịch vụ, tiêu chí lựa chọn và lời khuyên đàm phán.";
                break;
            default:
                $basePrompt .= "Hãy đưa ra gợi ý tổng quát để cải thiện và tối ưu hóa sự kiện này.";
                break;
        }
        
        if ($customPrompt) {
            $basePrompt .= "\n\nYêu cầu cụ thể: " . $customPrompt;
        }
        
        $basePrompt .= "\n\nVui lòng trả lời bằng tiếng Việt và cung cấp thông tin chi tiết, thực tế.";
        
        return $basePrompt;
    }
    
    /**
     * Call Gemini API
     */
    private function callGeminiAPI(string $prompt): ?string
    {
        $apiKey = env('GEMINI_API_KEY');
        
        if (!$apiKey) {
            \Log::warning('GEMINI_API_KEY chưa được cấu hình trong file .env');
            return null;
        }
        
        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topP' => 0.8,
                        'topK' => 40,
                        'maxOutputTokens' => 2048,
                    ],
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ]
                    ]
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                \Log::info('Gemini API Response: ', $data);
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $data['candidates'][0]['content']['parts'][0]['text'];
                } else {
                    \Log::warning('Gemini API Response không có text trong format mong đợi: ', $data);
                    return null;
                }
            } else {
                \Log::error('Gemini API Response Error: ' . $response->status() . ' - ' . $response->body());
                return null;
            }
            
        } catch (\Exception $e) {
            \Log::error('Gemini API Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }
    
    /**
     * Process AI response and create suggestion
     */
    private function processSuggestionResponse(int $eventId, string $type, string $response): AiSuggestion
    {
        // Parse response để tạo title và confidence score
        $lines = explode("\n", $response);
        $title = $this->extractTitle($response, $type);
        $confidence = $this->calculateConfidence($response);
        
        return AiSuggestion::create([
            'event_id' => $eventId,
            'suggestion_type' => $type,
            'title' => $title,
            'content' => $response,
            'ai_model' => 'gemini-2.0-flash',
            'confidence_score' => $confidence,
            'status' => 'generated',
            'input_parameters' => [
                'type' => $type,
                'generated_at' => now()->toISOString()
            ]
        ]);
    }
    
    /**
     * Extract title from AI response
     */
    private function extractTitle(string $response, string $type): string
    {
        $lines = array_filter(explode("\n", $response));
        $firstLine = trim($lines[0] ?? '');
        
        // Nếu dòng đầu tiên là tiêu đề, sử dụng nó
        if (strlen($firstLine) > 10 && strlen($firstLine) < 100) {
            return $firstLine;
        }
        
        // Nếu không, tạo tiêu đề dựa trên type
        $typeNames = [
            'budget' => 'Gợi ý ngân sách',
            'timeline' => 'Gợi ý timeline',
            'checklist' => 'Gợi ý checklist',
            'supplier' => 'Gợi ý nhà cung cấp',
            'general' => 'Gợi ý tổng quát'
        ];
        
        return ($typeNames[$type] ?? 'Gợi ý AI') . ' - ' . now()->format('d/m/Y H:i');
    }
    
    /**
     * Calculate confidence score based on response quality
     */
    private function calculateConfidence(string $response): float
    {
        $length = strlen($response);
        $lineCount = count(explode("\n", $response));
        
        // Tính confidence dựa trên độ dài và cấu trúc của response
        $confidence = 0.5;
        
        if ($length > 500) $confidence += 0.2;
        if ($length > 1000) $confidence += 0.1;
        if ($lineCount > 5) $confidence += 0.1;
        if (strpos($response, '•') !== false || strpos($response, '-') !== false) $confidence += 0.1;
        
        return min(1.0, $confidence);
    }
}
