<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use App\Models\Tenant\MailSetting;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\Server;
use Dedoc\Scramble\Support\Generator\ServerVariable;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        $apiPath = trim((string) config('scramble.api_path', 'api'), '/');

        /*
        |--------------------------------------------------------------------------
        | Central API Documentation
        |--------------------------------------------------------------------------
        */
        Scramble::configure('default')
            ->routes(function (Route $route) use ($apiPath) {
                if (! Str::startsWith($route->uri(), $apiPath)) {
                    return false;
                }

                $name = (string) ($route->getName() ?? '');

                return $name === 'verification.verify'
                    || Str::startsWith($name, 'central.');
            })
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $centralApiServer = rtrim(
                    (string) config('scramble.central_api_server', config('app.url').'/api'),
                    '/'
                );

                $openApi->servers = [
                    Server::make($centralApiServer)
                        ->setDescription('Central API server'),
                ];

                $openApi->security = [];

                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });

        /*
        |--------------------------------------------------------------------------
        | Tenant API Documentation
        |--------------------------------------------------------------------------
        */
        Scramble::registerApi('tenant', [
            'info' => [
                'version' => config('scramble.info.version', '1.0.0'),
                'description' => 'Tenant store API. Use a tenant domain for requests because tenant routes initialize tenancy by domain.',
            ],
        ])
            ->expose(
                ui: fn ($router, $action) => $router
                    ->get('docs/tenant/api', $action)
                    ->name('scramble.tenant.ui'),

                document: fn ($router, $action) => $router
                    ->get('docs/tenant/api.json', $action)
                    ->name('scramble.tenant.document'),
            )
            ->routes(function (Route $route) use ($apiPath) {
                if (! Str::startsWith($route->uri(), $apiPath)) {
                    return false;
                }

                return Str::startsWith(
                    (string) ($route->getName() ?? ''),
                    'tenant.'
                );
            })
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $tenantApiServer = rtrim(
                    (string) config('scramble.tenant_api_server', 'http://{tenant}.ecommerce-application-backend.test/api'),
                    '/'
                );

                $tenantServer = Server::make($tenantApiServer)
                    ->setDescription('Tenant API server');

                $tenantServer->variables = [
                    'tenant' => ServerVariable::make(
                        default: (string) config('scramble.default_tenant', 'demo'),
                        description: 'The tenant subdomain or tenant domain identifier.'
                    ),
                ];

                $openApi->servers = [
                    $tenantServer,
                ];

                $openApi->security = [];

                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });

        // Only run this if the tenant database connection is active and the table exists
        if (function_exists('tenant') && tenant() && Schema::hasTable('mail_settings')) {

            $mailSettings = MailSetting::query()->first();

            if ($mailSettings) {
                Config::set('mail.default', $mailSettings->mailer ?? 'smtp');
                Config::set('mail.mailers.smtp.host', $mailSettings->host);
                Config::set('mail.mailers.smtp.port', $mailSettings->port);
                Config::set('mail.mailers.smtp.encryption', $mailSettings->encryption);
                Config::set('mail.mailers.smtp.username', $mailSettings->username);
                Config::set('mail.mailers.smtp.password', $mailSettings->password);

                Config::set('mail.from.address', $mailSettings->from_address);
                Config::set('mail.from.name', $mailSettings->from_name);
            }
        }
    }
}
