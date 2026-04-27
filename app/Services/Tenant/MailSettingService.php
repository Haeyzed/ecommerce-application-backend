<?php

namespace App\Services\Tenant;

use App\Models\Tenant\MailSetting;

/**
 * Class MailSettingService
 * * Handles business logic related to tenant SMTP/Mail configurations.
 */
class MailSettingService
{
    /**
     * Retrieve the current mail settings or create defaults.
     *
     * @return MailSetting
     */
    public function getCurrentSettings(): MailSetting
    {
        return MailSetting::query()->firstOrCreate([], [
            'mailer'       => 'smtp',
            'host'         => '127.0.0.1',
            'port'         => 1025,
            'encryption'   => 'tls',
            'from_address' => 'noreply@example.com',
            'from_name'    => function_exists('tenant') && tenant() ? tenant('name') ?? 'Store' : 'Store',
        ]);
    }

    /**
     * Update the tenant mail settings.
     *
     * @param array $data Validated mail settings data.
     * @return MailSetting
     */
    public function updateSettings(array $data): MailSetting
    {
        $settings = $this->getCurrentSettings();

        // Remove password from array if it is null (prevents wiping out existing password)
        if (array_key_exists('password', $data) && is_null($data['password'])) {
            unset($data['password']);
        }

        $settings->update($data);

        return $settings->fresh();
    }
}
