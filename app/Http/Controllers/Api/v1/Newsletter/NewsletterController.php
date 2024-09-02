<?php

namespace App\Http\Controllers\Api\v1\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Resources\Newsletter\NewsletterCollection;
use App\Http\Resources\Newsletter\NewsletterResource;
use App\Models\Newsletter\Newsletter;
use App\Services\Newsletter\NewsletterService;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Authenticated]
#[Group(name: 'Newsletters')]
class NewsletterController extends Controller
{
    public function __construct(
        private NewsletterService $newsletterService
    ) {
    }

    #[Endpoint('Get all newsletters')]
    #[ResponseFromApiResource(NewsletterCollection::class, Newsletter::class, with: ['availableChannels'])]
    public function index(): NewsletterCollection
    {
        $newsletters = $this->newsletterService->getAll();
        return new NewsletterCollection($newsletters);
    }

    #[Endpoint('Get a newsletter')]
    #[ResponseFromApiResource(NewsletterResource::class, Newsletter::class, with: ['availableChannels'])]
    public function show(Newsletter $newsletter): NewsletterResource
    {
        $newsletter = $this->newsletterService->show($newsletter->id);
        return new NewsletterResource($newsletter);
    }
}
