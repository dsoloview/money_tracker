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
}
