<b>Your statistics:</b>
Period: <b>{{ $data->dateFrom->format('Y-m-d') }} - {{ $data->dateTo->format('Y-m-d') }}</b>
Your currency: <b>{{ $data->currency->code }}</b>
Total income: <b>{{ $data->totalIncome }}</b>
Total expense: <b>{{ $data->totalExpense }}</b>
Transactions count: <b>{{ $data->transactionsCount }}</b>
Balance: <b>{{ $data->totalBalance }}</b>

