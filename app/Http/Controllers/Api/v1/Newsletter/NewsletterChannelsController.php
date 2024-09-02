<?php

namespace App\Http\Controllers\Api\v1\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Resources\Newsletter\Channel\NewsletterChannelCollection;
use App\Http\Resources\Newsletter\Channel\NewsletterChannelResource;
use App\Models\Newsletter\NewsletterChannel;
use App\Services\Newsletter\NewsletterChannelsService;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Authenticated]
#[Group(name: 'Newsletters')]
class NewsletterChannelsController extends Controller
{
    public function __construct(
        private NewsletterChannelsService $newsletterChannelsService
    ) {
    }

    #[Endpoint('Get all newsletter channels')]
    #[ResponseFromApiResource(NewsletterChannelCollection::class, NewsletterChannel::class)]
    public function index(): NewsletterChannelCollection
    {
        $newsletters = $this->newsletterChannelsService->getAll();
        return new NewsletterChannelCollection($newsletters);
    }

    #[Endpoint('Get a newsletter channel')]
    #[ResponseFromApiResource(NewsletterChannelResource::class, NewsletterChannel::class)]
    public function show(NewsletterChannel $newsletterChannel): NewsletterChannelResource
    {
        $newsletter = $this->newsletterChannelsService->show($newsletterChannel->id);
        return new NewsletterChannelResource($newsletter);
    }
}
