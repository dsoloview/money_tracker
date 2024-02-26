<?php

namespace App\Services\User\Setting;

use App\Data\User\Setting\UserSettingData;
use App\Models\User;
use App\Models\UserSetting;

class UserSettingService
{
    public function createSettingForUser(User $user, UserSettingData $settingData): UserSetting
    {
        return $user->settings()->create($settingData->all());
    }

    public function updateSettingForUser(User $user, UserSettingData $settingData): UserSetting
    {
        $user->settings->update($settingData->all());

        return $user->settings;
    }
}
