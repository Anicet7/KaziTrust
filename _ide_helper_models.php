<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $tenant_id
 * @property string $name
 * @property string $uuid
 * @property bool $is_active
 * @property string|null $webhook_url
 * @property string|null $webhook_secret
 * @property string $llm_provider
 * @property string|null $llm_api_key
 * @property array<array-key, mixed>|null $ai_settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AppApiKey> $apiKeys
 * @property-read int|null $api_keys_count
 * @property-read \App\Models\Tenant $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TrustLog> $trustLogs
 * @property-read int|null $trust_logs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App whereAiSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App whereLlmApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App whereLlmProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App whereWebhookSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|App whereWebhookUrl($value)
 */
	class App extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $app_id
 * @property string $name
 * @property string $key
 * @property string|null $secret
 * @property string|null $last_used_at
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\App $app
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppApiKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppApiKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppApiKey query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppApiKey whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppApiKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppApiKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppApiKey whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppApiKey whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppApiKey whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppApiKey whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppApiKey whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppApiKey whereUpdatedAt($value)
 */
	class AppApiKey extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $subscription_id
 * @property int $tenant_id
 * @property numeric $amount
 * @property string $currency
 * @property string $status
 * @property string $provider
 * @property string|null $provider_transaction_id
 * @property array<array-key, mixed>|null $provider_response
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Subscription $subscription
 * @property-read \App\Models\Tenant $tenant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereProviderResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereProviderTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property numeric $price_monthly
 * @property numeric $price_yearly
 * @property string $currency
 * @property int $max_apps
 * @property int $max_api_keys_per_app
 * @property int $max_requests_per_month
 * @property int $max_users
 * @property array<array-key, mixed>|null $features
 * @property bool $is_active
 * @property bool $is_public
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMaxApiKeysPerApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMaxApps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMaxRequestsPerMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMaxUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan wherePriceMonthly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan wherePriceYearly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereUpdatedAt($value)
 */
	class Plan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tenant_id
 * @property int $plan_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $trial_ends_at
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property string|null $payment_provider
 * @property string|null $payment_provider_id
 * @property numeric|null $price_paid
 * @property string $currency
 * @property string $billing_cycle
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Plan $plan
 * @property-read \App\Models\Tenant $tenant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereBillingCycle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePaymentProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePaymentProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePricePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUpdatedAt($value)
 */
	class Subscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $email
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Subscription|null $activeSubscription
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\App> $apps
 * @property-read int|null $apps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedAt($value)
 */
	class Tenant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $app_id
 * @property string $phone_number
 * @property array<array-key, mixed> $nokia_payload
 * @property string $ai_provider
 * @property array<array-key, mixed> $ai_response
 * @property int $token_count
 * @property int $latency_ms
 * @property numeric $cost_estimate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\App $app
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog whereAiProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog whereAiResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog whereCostEstimate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog whereLatencyMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog whereNokiaPayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog whereTokenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrustLog whereUpdatedAt($value)
 */
	class TrustLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $tenant_id
 * @property string $role
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Tenant|null $tenant
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\HasTenants, \Filament\Models\Contracts\FilamentUser {}
}

