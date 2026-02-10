<?php

namespace App\Services\ContentManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\Content;
use App\Models\StudyGuideActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use Throwable;

class ContentService
{
    use FileManagementTrait;

    public function storeNextPageData(int $userId, int $contentId, int $pageNumber): ?StudyGuideActivity
    {
        $activity = StudyGuideActivity::where('user_id', $userId)
            ->where('content_id', $contentId)
            ->where('page_number', $pageNumber)
            ->first();

        if (! $activity) {
            $activity = StudyGuideActivity::create([
                'user_id' => $userId,
                'content_id' => $contentId,
                'page_number' => $pageNumber,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
        }

        return $activity;
    }

    public function getContents($type = 0, ?string $file_type = null, ?string $category = null, string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        $query = Content::orderBy($orderBy, $order)->latest();
        if (! is_null($type)) {
            $query->where('type', $type);
        }

        if (! is_null($category)) {
            $query->where('category', $category);
        }
        if (! is_null($file_type)) {
            $query->where('file_type', $file_type);
        }

        return $query;
    }

    public function findContent($id): ?Content
    {
        $content = Content::findOrFail($id);
        if (! $content) {
            throw new \Exception('Content not found');
        }

        return $content;
    }

    public function createContent(array $data, $file): Content
    {
        return DB::transaction(function () use ($data, $file) {

            $contentType = $this->normalizeContentType($data['type'] ?? null);
            $data['type'] = $contentType;

            if ($file) {
                $mimeType = $file->getMimeType();
                $data['file_type'] = $this->detectFileType($mimeType);

                if ($data['file_type'] === 'invalid') {
                    throw new \Exception('Only audio and PDF files are allowed.');
                }

                $data['file'] = $this->handleFileUpload(
                    $file,
                    'contents',
                    $data['file_type']
                );

                if ($this->shouldCalculateTotalPages($data['file_type'], $contentType)) {
                    $data['total_pages'] = $this->calculatePdfPageCount($file);
                } else {
                    $data['total_pages'] = 0;
                }
            }

            $data['created_by'] = Auth::id();

            return Content::create($data);
        });
    }

    private function detectFileType(string $mimeType): string
    {
        return match (true) {
            str_starts_with($mimeType, 'audio/') => 'audio',
            $mimeType === 'application/pdf' => 'pdf',
            default => 'invalid',
        };
    }

    public function updateContent(Content $content, array $data, $file): Content
    {
        return DB::transaction(function () use ($content, $data, $file) {

            $contentType = $this->normalizeContentType($data['type'] ?? $content->type);
            $data['type'] = $contentType;

            if ($file) {
                $mimeType = $file->getMimeType();
                $data['file_type'] = $this->detectFileType($mimeType);

                if ($data['file_type'] === 'invalid') {
                    throw new \Exception('Only audio and PDF files are allowed.');
                }

                if (! empty($content->file)) {
                    $this->fileDelete($content->file);
                }

                $data['file'] = $this->handleFileUpload(
                    $file,
                    'contents',
                    $data['file_type']
                );

                if ($this->shouldCalculateTotalPages($data['file_type'], $contentType)) {
                    $data['total_pages'] = $this->calculatePdfPageCount($file);
                } else {
                    $data['total_pages'] = 0;
                }
            }
            $data['updated_by'] = Auth::id();
            $content->update($data);

            return $content;
        });
    }

    public function deleteContent(Content $content): void
    {
        DB::transaction(function () use ($content) {
            $content->forceDelete();
        });
    }

    private function normalizeContentType(mixed $type): int
    {
        $allowedTypes = [Content::TYPE_STUDY_GUIDE, Content::TYPE_FLASHCARD];
        $type = is_null($type) ? Content::TYPE_STUDY_GUIDE : (int) $type;

        return in_array($type, $allowedTypes, true) ? $type : Content::TYPE_STUDY_GUIDE;
    }

    private function shouldCalculateTotalPages(string $fileType, int $contentType): bool
    {
        return $fileType === 'pdf' && $contentType === Content::TYPE_STUDY_GUIDE;
    }

    protected function calculatePdfPageCount($file): int
    {
        $filePath = (string) $file->getRealPath();
        $pageCountFromPattern = $this->countPdfPagesByPattern($filePath);
        if ($pageCountFromPattern > 0) {
            return $pageCountFromPattern;
        }

        try {
            $parser = new Parser;
            $pdf = $parser->parseFile($filePath);

            return count($pdf->getPages());
        } catch (Throwable $exception) {
            Log::warning('Failed to parse PDF for total pages.', [
                'file_name' => $file->getClientOriginalName(),
                'error' => $exception->getMessage(),
            ]);

            return 0;
        }
    }

    protected function countPdfPagesByPattern(string $filePath): int
    {
        if (! is_file($filePath)) {
            return 0;
        }

        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            return 0;
        }

        $pageCount = 0;
        $carry = '';
        $pattern = '/\/Type\s*\/Page\b/';

        try {
            while (! feof($handle)) {
                $chunk = fread($handle, 1024 * 1024);
                if ($chunk === false) {
                    break;
                }

                $buffer = $carry.$chunk;
                $carryLength = strlen($carry);
                $matches = [];
                $matchedPages = preg_match_all($pattern, $buffer, $matches, PREG_OFFSET_CAPTURE);

                if ($matchedPages !== false) {
                    foreach ($matches[0] as $match) {
                        $matchValue = (string) $match[0];
                        $matchOffset = (int) $match[1];

                        if (($matchOffset + strlen($matchValue)) > $carryLength) {
                            $pageCount++;
                        }
                    }
                }

                $carry = substr($buffer, -64);
            }
        } finally {
            fclose($handle);
        }

        return $pageCount;
    }
}
