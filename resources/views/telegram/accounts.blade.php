<b>Your accounts:</b>
@foreach ($accounts as $account)
    <u>{{ $account->name }}</u>: {{ $account->balance }} {{ $account->currency->symbol }} ({{ $account->user_currency_balance }} {{ $user->settings->mainCurrency->symbol }})
@endforeach
<b>Total balance: </b> {{ $totalBalance }} {{ $user->settings->mainCurrency->symbol }}
