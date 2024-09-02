<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Data\User\Newsletter\UserNewsletterUpdateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserNewsletterUpdateRequest;
use App\Http\Resources\User\UserNewsletter\UserNewsletterCollection;
use App\Http\Resources\User\UserNewsletter\UserNewsletterResource;
use App\Models\User;
use App\Models\UserNewsletter;
use App\Services\User\Newsletter\UserNewsletterService;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'User newsletters', description: 'User newsletters')]
#[Authenticated]
class UserNewsletterController extends Controller
{
    public function __construct(
        private readonly UserNewsletterService $userNewsletterService
    ) {
    }

    #[Endpoint('Get all user newsletters')]
    #[ResponseFromApiResource(
        UserNewsletterCollection::class,
        UserNewsletter::class,
        with: ['channel', 'newsletter', 'newsletter.availableChannels']
    )]
    public function index(User $user)
    {
        $this->authorize('view', $user);

        $transfers = $this->userNewsletterService->getUserNewsletters($user);

        return new UserNewsletterCollection($transfers);
    }

    #[Endpoint('Get a user newsletter')]
    #[ResponseFromApiResource(
        UserNewsletterResource::class,
        UserNewsletter::class,
    )]
    public function show(User $user, UserNewsletter $userNewsletter)
    {
        $this->authorize('view', $userNewsletter);

        return new UserNewsletterResource($userNewsletter);
    }

    #[Endpoint('Update a user newsletter')]
    public function update(User $user, UserNewsletter $userNewsletter, UserNewsletterUpdateRequest $request)
    {
        $this->authorize('update', $userNewsletter);

        $data = UserNewsletterUpdateData::from($request);

        $this->userNewsletterService->update($userNewsletter, $data);

        return response()->noContent();
    }

    #[Endpoint('Subscribe to a user newsletter')]
    public function subscribe(User $user, UserNewsletter $userNewsletter)
    {
        $this->authorize('subscribe', $userNewsletter);

        $this->userNewsletterService->subscribe($userNewsletter);

        return response()->noContent();
    }

    #[Endpoint('Unsubscribe from a user newsletter')]
    public function unsubscribe(User $user, UserNewsletter $userNewsletter)
    {
        $this->authorize('unsubscribe', $userNewsletter);

        $this->userNewsletterService->unsubscribe($userNewsletter);

        return response()->noContent();
    }
}
