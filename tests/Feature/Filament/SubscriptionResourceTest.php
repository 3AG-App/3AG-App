<?php

use App\Filament\Resources\Subscriptions\Pages\ListSubscriptions;
use App\Filament\Resources\Subscriptions\Pages\ViewSubscription;
use App\Filament\Resources\Subscriptions\SubscriptionResource;
use App\Models\User;
use Laravel\Cashier\Subscription;
use Livewire\Livewire;

beforeEach(function () {
    $adminEmail = 'admin@test.com';
    config(['admin.emails' => [$adminEmail]]);

    $this->admin = User::factory()->create(['email' => $adminEmail]);
    $this->actingAs($this->admin);
});

describe('List Subscriptions Page', function () {
    it('can render the index page', function () {
        $this->get(SubscriptionResource::getUrl('index'))
            ->assertSuccessful();
    });

    it('can list subscriptions', function () {
        $subscriptions = Subscription::factory()->count(3)->create();

        Livewire::test(ListSubscriptions::class)
            ->assertCanSeeTableRecords($subscriptions);
    });

    it('can search subscriptions by stripe id', function () {
        $subscription = Subscription::factory()->create(['stripe_id' => 'sub_unique_12345']);
        $otherSubscription = Subscription::factory()->create(['stripe_id' => 'sub_other_67890']);

        Livewire::test(ListSubscriptions::class)
            ->searchTable('sub_unique')
            ->assertCanSeeTableRecords([$subscription])
            ->assertCanNotSeeTableRecords([$otherSubscription]);
    });

    it('can filter subscriptions by status', function () {
        $activeSubscription = Subscription::factory()->active()->create();
        $canceledSubscription = Subscription::factory()->canceled()->create();

        Livewire::test(ListSubscriptions::class)
            ->filterTable('stripe_status', 'active')
            ->assertCanSeeTableRecords([$activeSubscription])
            ->assertCanNotSeeTableRecords([$canceledSubscription]);
    });
});

describe('View Subscription Page', function () {
    it('can render the view page', function () {
        $subscription = Subscription::factory()->create();

        $this->get(SubscriptionResource::getUrl('view', ['record' => $subscription]))
            ->assertSuccessful();
    });

    it('displays subscription details', function () {
        $subscription = Subscription::factory()->create(['stripe_id' => 'sub_view_12345']);

        Livewire::test(ViewSubscription::class, ['record' => $subscription->getRouteKey()])
            ->assertSee('sub_view_12345');
    });
});
