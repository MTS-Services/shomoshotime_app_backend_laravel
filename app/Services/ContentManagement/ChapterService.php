<?php

namespace App\Services\ContentManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\Chapter;
use App\Models\Content;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class ChapterService
{
    use FileManagementTrait;
   public function getChaptersByContent(?int $contentId = null, string $orderBy = 'created_at', string $order = 'desc'): Builder
    {

        return Chapter::with('content')->where('content_id', $contentId)->orderBy($orderBy, $order);
    }

    public function createChapter(array $data, $file = null): Chapter|array
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

             return DB::transaction(function () use ($data, $file) {

            if ($file) {
                $data['file'] = $this->handleFileUpload(
                    $file,
                    'chapters',
                    $data['file_type'] ?? 'chapter'
                );
            }

            $data['created_by'] = Auth::id();

            return Chapter::create($data);
        });

        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Something went wrong: '.$e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
        }
    }

    public function updateChapter(int $id, array $data): Chapter|array
    {
        try {
            $Chapter = Chapter::findOrFail($id);

            if (! $Chapter) {
                return [
                    'success' => false,
                    'message' => 'Flash card not found.',
                    'status' => Response::HTTP_NOT_FOUND,
                ];
            }

            $data['updated_by'] = Auth::id();

            return DB::transaction(function () use ($Chapter, $data) {
                $Chapter->update($data);

                return $Chapter;
            });

        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Something went wrong: '.$e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
        }
    }

    public function deleteChapter(int $id): bool|array
    {
        try {
            $Chapter = Chapter::findOrFail($id);

            if (! $Chapter) {
                return [
                    'success' => false,
                    'message' => 'Flash card not found.',
                    'status' => Response::HTTP_NOT_FOUND,
                ];
            }

            return DB::transaction(function () use ($Chapter) {
                $Chapter->forceDelete();

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
