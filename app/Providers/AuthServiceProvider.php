<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Enums\Role\Roles;
use App\Models\Account\Account;
use App\Models\Category\Category;
use App\Models\Transaction\Transaction;
use App\Models\Transfer\Transfer;
use App\Models\User;
use App\Models\UserNewsletter;
use App\Policies\Account\AccountPolicy;
use App\Policies\Category\CategoryPolicy;
use App\Policies\Role\RolePolicy;
use App\Policies\Transaction\TransactionPolicy;
use App\Policies\Transfer\TransferPolicy;
use App\Policies\User\UserNewsletterPolicy;
use App\Policies\User\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        User::class => UserPolicy::class,
        Transaction::class => TransactionPolicy::class,
        Account::class => AccountPolicy::class,
        Category::class => CategoryPolicy::class,
        Role::class => RolePolicy::class,
        Transfer::class => TransferPolicy::class,
        UserNewsletter::class => UserNewsletterPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        \Gate::before(function (User $user, string $ability) {
            if ($user->hasRole(Roles::admin->value)) {
                return true;
            }
        });
    }
}
