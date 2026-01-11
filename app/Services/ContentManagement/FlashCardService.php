<?php

namespace App\Services\ContentManagement;

use App\Models\Content;
use App\Models\FlashCard;
use App\Models\FlashCardActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class FlashCardService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getFlashCards(?string $category = null, string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        $query = Content::orderBy($orderBy, $order)->isPublish()->where('type', 1)->latest();

        if (! is_null($category)) {
            $query->where('category', $category);
        }

        return $query;
    }

    public function storeNextQuestionData(int $userId, int $contentId, int $cardId): ?FlashCardActivity
    {
        $activity = FlashCardActivity::where('user_id', $userId)
            ->where('content_id', $contentId)
            ->where('card_id', $cardId)
            ->first();

        if (! $activity) {
            $activity = FlashCardActivity::create([
                'user_id' => $userId,
                'content_id' => $contentId,
                'card_id' => $cardId,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
        }

        return $activity;
    }

    public function getFlashCardsByContent(?int $contentId = null, string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        return FlashCard::with('content')->where('content_id', $contentId)->orderBy($orderBy, $order);

    }

    public function createFlashCard(array $data): FlashCard|array
    {
        try {
            // Fetch content
            $content = Content::findOrFail($data['content_id']);

            if (! $content) {
                return [
                    'success' => false,
                    'message' => 'Content not found.',
                    'status' => Response::HTTP_NOT_FOUND,
                ];
            }

            if ($content->type === Content::TYPE_STUDY_GUIDE) {
                return [
                    'success' => false,
                    'message' => 'Cannot create flash card: Content is a Study Guide.',
                    'status' => Response::HTTP_FORBIDDEN,
                ];
            }

            $data['created_by'] = Auth::id();

            return DB::transaction(function () use ($data) {
                return FlashCard::create($data);
            });

        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Something went wrong: '.$e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
        }
    }

    public function updateFlashCard(int $id, array $data): FlashCard|array
    {
        try {
            $flashCard = FlashCard::findOrFail($id);

            if (! $flashCard) {
                return [
                    'success' => false,
                    'message' => 'Flash card not found.',
                    'status' => Response::HTTP_NOT_FOUND,
                ];
            }

            $data['updated_by'] = Auth::id();

            return DB::transaction(function () use ($flashCard, $data) {
                $flashCard->update($data);

                return $flashCard;
            });

        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Something went wrong: '.$e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
        }
    }

    public function deleteFlashCard(int $id): bool|array
    {
        try {
            $flashCard = FlashCard::findOrFail($id);

            if (! $flashCard) {
                return [
                    'success' => false,
                    'message' => 'Flash card not found.',
                    'status' => Response::HTTP_NOT_FOUND,
                ];
            }

            return DB::transaction(function () use ($flashCard) {
                $flashCard->forceDelete();

                return true;
            });

        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Something went wrong: '.$e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
        }
    }
}
