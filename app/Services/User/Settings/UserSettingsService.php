<?php

namespace App\Services\User\Settings;

use App\Data\User\Settings\UserSettingsData;
use App\Models\User;
use App\Models\UserSettings;

class UserSettingsService
{
    public function createSettingsForUser(User $user, UserSettingsData $settingData): UserSettings
    {
        return $user->settings()->create($settingData->all());
    }

    public function updateSettingsForUser(User $user, UserSettingsData $settingData): UserSettings
    {
        $user->settings->update($settingData->all());

        return $user->settings;
    }
}
