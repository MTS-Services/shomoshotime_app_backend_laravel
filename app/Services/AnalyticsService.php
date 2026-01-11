<?php

namespace App\Services;

use App\Models\StudyGuideActivity;

class AnalyticsService
{
    public function getAnalyticsData($userId)
    {
        $all = StudyGuideActivity::with(['content' => (function ($q) {
                $q->isPublish()->studyGuide()->select('id','total_pages');
            })])
            ->whereHas('content', function ($q) {
                $q->isPublish()->studyGuide();
            })
            ->where('user_id', $userId)->get();
    }
}
