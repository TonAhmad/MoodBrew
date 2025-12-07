<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\AI\AiChatService;
use App\Services\AI\AiRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AI Controller untuk Customer
 * 
 * Handle semua AI-related requests dari customer:
 * - Mood-based recommendation
 * - Chat dengan AI barista
 * 
 * @author [Nama Teman Anda]
 */
class AiController extends Controller
{
    public function __construct(
        protected AiRecommendationService $recommendationService,
        protected AiChatService $chatService
    ) {}

    /**
     * Get recommendation berdasarkan mood
     * 
     * @route POST /api/ai/recommend
     * @param Request $request
     * @return JsonResponse
     * 
     * Request Body:
     * {
     *     "mood": "Saya lelah dan butuh energi"
     * }
     * 
     * Response:
     * {
     *     "success": true,
     *     "message": "AI response",
     *     "recommendations": [...],
     *     "prompt_id": 123
     * }
     */
    public function recommend(Request $request): JsonResponse
    {
        $request->validate([
            'mood' => 'required|string|max:500',
        ]);

        $result = $this->recommendationService->getRecommendation(
            userMood: $request->mood,
            userId: auth()->id(),
            sessionId: session()->getId()
        );

        // Transform recommendations untuk include semua data yang diperlukan
        if (isset($result['recommendations']) && is_array($result['recommendations'])) {
            $result['recommendations'] = collect($result['recommendations'])->map(function($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'description' => $item['description'] ?? '',
                    'category' => $item['category'] ?? 'coffee',
                    'image_path' => $item['image_path'] ?? null,
                ];
            })->toArray();
        }

        return response()->json($result);
    }

    /**
     * Chat dengan AI barista
     * 
     * @route POST /order/ai/chat
     * @param Request $request
     * @return JsonResponse
     * 
     * Request Body:
     * {
     *     "message": "Apa yang cocok untuk sore hari?",
     *     "history": [
     *         {"user": "Hai", "assistant": "Halo!"}
     *     ]
     * }
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array',
        ]);

        $result = $this->chatService->chat(
            message: $request->message,
            conversationHistory: $request->history ?? [],
            userId: auth()->id(),
            sessionId: session()->getId()
        );

        // Transform menu suggestions untuk include semua data
        if (isset($result['suggested_menus']) && is_array($result['suggested_menus'])) {
            $result['suggested_menus'] = collect($result['suggested_menus'])->map(function($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'description' => $item['description'] ?? '',
                    'category' => $item['category'] ?? 'coffee',
                    'image_path' => $item['image_path'] ?? null,
                ];
            })->toArray();
        }

        return response()->json($result);
    }

    /**
     * Get quick reply suggestions
     * 
     * @route GET /order/ai/quick-replies
     * @return JsonResponse
     */
    public function quickReplies(): JsonResponse
    {
        return response()->json([
            'quick_replies' => $this->chatService->getQuickReplies(),
        ]);
    }

    /**
     * Get conversation history
     * 
     * @route GET /order/ai/history
     * @return JsonResponse
     */
    public function history(): JsonResponse
    {
        $history = $this->recommendationService->getConversationHistory(
            userId: auth()->id(),
            sessionId: session()->getId(),
            limit: 20
        );

        return response()->json([
            'history' => $history,
        ]);
    }
}
