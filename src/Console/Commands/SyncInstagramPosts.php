<?php

namespace Shopen\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Shopen\Models\Instagram\InstagramPost;
use Shopen\Services\ConfigService;
use Shopen\Services\Facebook\InstagramApiService;
use Throwable;

class SyncInstagramPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopen:sync-instagram-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Instagram posts';

    public function __construct(
        private readonly ConfigService $config,
        private readonly InstagramApiService $api
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = $this->config->get('instagram/long_lived_token');
        $igUserId = $this->config->get('instagram/ig_user_id');

        if (!$token || !$igUserId) {
            return;
        }

            $media = $this->api->fetchRecentMedia($igUserId, $token, 20);

            foreach ($media as $item) {
                try {
                    if (!in_array($item['media_type'] ?? '', ['IMAGE', 'CAROUSEL_ALBUM'])) {
                        continue;
                    }
                    $instagramId = $item['id'] ?? null;
                    $mediaUrl = $item['media_url'] ?? ($item['thumbnail_url'] ?? null);
                    $permalink = $item['permalink'] ?? null;
                    $timestamp = isset($item['timestamp']) ? Carbon::make($item['timestamp']) : null;

                    if (!$instagramId || !$mediaUrl || !$permalink) {
                        continue;
                    }

                    DB::beginTransaction();
                    $post = InstagramPost::query()->where('media_id', $instagramId)->firstOrNew(['media_id' => $instagramId]);
                    $post->post_url = $permalink;
                    $post->timestamp = $timestamp;
                    $post->save();

                    if (!$post->media()->count()) {
                        $post->addMediaFromUrl($mediaUrl)->toMediaCollection();
                    }

                    DB::commit();
                } catch (Throwable $e) {
                    DB::rollBack();
                    Log::error('[Instagram] Błąd crona: ' . $e->getMessage());
                }
            }

    }
}
