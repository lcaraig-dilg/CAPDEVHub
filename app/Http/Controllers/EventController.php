<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function show($slug)
    {
        // Find activity by slug (generated from title)
        $activity = Activity::all()->first(function ($activity) use ($slug) {
            return \Illuminate\Support\Str::slug($activity->title) === $slug;
        });
        
        if (!$activity) {
            abort(404);
        }
        
        // Fetch recent posts from DILG-NCR website (RSS feed)
        $websitePosts = $this->getWebsitePosts();
        
        return view('events.show', compact('activity', 'websitePosts'));
    }

    protected function getWebsitePosts()
    {
        try {
            $rssUrl = env('DILG_NCR_RSS_FEED', 'https://ncr.dilg.gov.ph/feed');
            
            // Try to fetch RSS feed
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'Mozilla/5.0 (compatible; CAPDEVhub/1.0)',
                ]
            ]);
            
            $rssContent = @file_get_contents($rssUrl, false, $context);
            
            if ($rssContent) {
                $xml = @simplexml_load_string($rssContent);
                if ($xml && isset($xml->channel->item)) {
                    $posts = [];
                    $count = 0;
                    foreach ($xml->channel->item as $item) {
                        if ($count >= 5) break; // Limit to 5 recent posts
                        $posts[] = [
                            'title' => (string) $item->title,
                            'link' => (string) $item->link,
                            'date' => isset($item->pubDate) ? date('M d, Y', strtotime((string) $item->pubDate)) : null,
                        ];
                        $count++;
                    }
                    return $posts;
                }
            }
        } catch (\Exception $e) {
            // If RSS feed fails, return empty array
        }
        
        // Return empty array if RSS feed is not available
        return [];
    }
}
