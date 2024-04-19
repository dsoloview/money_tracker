<b>Transaction successfully created</b>
<b>Date:</b> {{ $transaction->date }}
<b>Amount:</b> {{ $transaction->amount }} {{ $transaction->account->currency->symbol }} ({{ $transaction->user_currency_amount->amount }} {{ $transaction->user_currency_amount->currency->symbol }})
<b>Type:</b> {{ $transaction->type }}
<b>Account:</b> {{ $transaction->account->name }}
<b>Categories:</b> {{ $transaction->categories->implode('name', ', ') }}
