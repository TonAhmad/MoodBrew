<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VibeEntry;
use App\Services\AI\AiSentimentService;
use App\Services\VibeWallService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * VibeWallController - Handle Vibe Wall moderation
 * Admin bisa approve/reject entries dan lihat analytics
 */
class VibeWallController extends Controller
{
    protected VibeWallService $vibeWallService;
    protected AiSentimentService $aiSentimentService;

    public function __construct(
        VibeWallService $vibeWallService,
        AiSentimentService $aiSentimentService
    ) {
        $this->vibeWallService = $vibeWallService;
        $this->aiSentimentService = $aiSentimentService;
    }

    /**
     * Display all vibe entries
     */
    public function index(Request $request): View
    {
        $query = VibeEntry::with('user');

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'featured') {
                $query->where('is_featured', true);
            }
        }

        // Filter by sentiment
        if ($request->filled('sentiment')) {
            if ($request->sentiment === 'positive') {
                $query->where('sentiment_score', '>=', 0.3);
            } elseif ($request->sentiment === 'neutral') {
                $query->whereBetween('sentiment_score', [-0.3, 0.3]);
            } elseif ($request->sentiment === 'negative') {
                $query->where('sentiment_score', '<=', -0.3);
            }
        }

        $entries = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => VibeEntry::count(),
            'pending' => VibeEntry::where('is_approved', false)->count(),
            'approved' => VibeEntry::where('is_approved', true)->count(),
            'featured' => VibeEntry::where('is_featured', true)->count(),
            'positive' => VibeEntry::where('sentiment_score', '>=', 0.3)->count(),
            'negative' => VibeEntry::where('sentiment_score', '<=', -0.3)->count(),
        ];

        // Check if AI is configured
        $aiAvailable = $this->aiSentimentService->isConfigured();

        return view('admin.vibewall.index', compact('entries', 'stats', 'aiAvailable'));
    }

    /**
     * Display pending entries for moderation
     */
    public function pending(): View
    {
        $entries = VibeEntry::with('user')
            ->where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $aiAvailable = $this->aiSentimentService->isConfigured();

        return view('admin.vibewall.pending', compact('entries', 'aiAvailable'));
    }

    /**
     * Approve an entry
     */
    public function approve(VibeEntry $entry): RedirectResponse
    {
        $entry->update(['is_approved' => true]);

        return redirect()
            ->back()
            ->with('success', 'Entry berhasil di-approve!');
    }

    /**
     * Reject (delete) an entry
     */
    public function reject(VibeEntry $entry): RedirectResponse
    {
        $entry->delete();

        return redirect()
            ->back()
            ->with('success', 'Entry berhasil dihapus!');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(VibeEntry $entry): RedirectResponse
    {
        $entry->update(['is_featured' => !$entry->is_featured]);

        $status = $entry->is_featured ? 'ditambahkan ke' : 'dihapus dari';

        return redirect()
            ->back()
            ->with('success', "Entry berhasil {$status} featured!");
    }

    /**
     * Analyze sentiment for an entry (requires AI)
     */
    public function analyze(VibeEntry $entry): RedirectResponse
    {
        if (!$this->aiSentimentService->isConfigured()) {
            return redirect()
                ->back()
                ->with('error', 'Fitur AI Sentiment belum dikonfigurasi!');
        }

        $result = $this->aiSentimentService->analyzeSentiment($entry->content);
        $entry->update(['sentiment_score' => $result['score'] ?? 0]);

        return redirect()
            ->back()
            ->with('success', 'Sentiment analysis berhasil!');
    }
}
