<?php

namespace App\Telegram\Enum;

enum AvailableTelegramCommands: string
{
    case START = '/start';
    case ACCOUNTS = '/accounts';
    case LOGOUT = '/logout';
    case AUTHORIZE = '/authorize';
    case HELP = '/help';
    case RESET = '/reset';
    case NEW_TRANSACTION = '/new_transaction';

    public static function commandsWithoutAuthorization(): array
    {
        return [
            self::START->value,
            self::HELP->value,
            self::AUTHORIZE->value,
            self::RESET->value,
        ];
    }

    public static function commandNeedToHaveAuthorization(string $command): bool
    {
        $result = false;
        foreach (self::commandsWithoutAuthorization() as $commandWithoutAuthorization) {
            if (str_contains($command, $commandWithoutAuthorization)) {
                $result = true;
            }
        }

        return $result;
    }
}
