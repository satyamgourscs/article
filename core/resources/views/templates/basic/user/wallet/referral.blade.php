@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="container py-4">
        <div class="row gy-4">
            <div class="col-lg-4">
                <div class="card custom--card h-100">
                    <div class="card-body">
                        <h5 class="mb-3">@lang('Wallet overview')</h5>
                        <p class="mb-1 text-muted">@lang('Current balance')</p>
                        <h3 class="mb-3">{{ showAmount($wallet->balance) }}</h3>
                        <p class="mb-1 text-muted">@lang('Total earned')</p>
                        <h5 class="mb-0">{{ showAmount($wallet->total_earned) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card custom--card">
                    <div class="card-body">
                        <h5 class="mb-3">@lang('Request withdrawal')</h5>
                        <p class="text-muted small mb-3">
                            @lang('Minimum balance to withdraw: :amount. Minimum request: :amount.', ['amount' => showAmount(\App\Http\Controllers\User\StudentWalletController::MIN_WITHDRAW_BALANCE)])
                        </p>
                        <form action="{{ route('user.referral_wallet.withdraw') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form--label">@lang('Amount')</label>
                                <input type="number" step="0.01" name="amount" class="form-control form--control" required
                                    min="{{ \App\Http\Controllers\User\StudentWalletController::MIN_WITHDRAW_BALANCE }}"
                                    value="{{ old('amount') }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form--label">@lang('Note (optional)')</label>
                                <textarea name="note" class="form-control form--control" rows="2">{{ old('note') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn--base">@lang('Submit request')</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card custom--card">
                    <div class="card-body">
                        <h5 class="mb-3">@lang('Wallet transactions')</h5>
                        <div class="table-responsive">
                            <table class="table table--responsive--lg">
                                <thead>
                                    <tr>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Type')</th>
                                        <th>@lang('Source')</th>
                                        <th>@lang('Amount')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transactions as $tx)
                                        <tr>
                                            <td>{{ showDateTime($tx->created_at) }}</td>
                                            <td>{{ __($tx->type) }}</td>
                                            <td>{{ __($tx->source ?? '—') }}</td>
                                            <td>{{ showAmount($tx->amount) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">@lang('No transactions yet')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($transactions->hasPages())
                            {{ paginateLinks($transactions) }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card custom--card">
                    <div class="card-body">
                        <h5 class="mb-3">@lang('Withdraw requests')</h5>
                        <div class="table-responsive">
                            <table class="table table--responsive--lg">
                                <thead>
                                    <tr>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($withdrawals as $w)
                                        <tr>
                                            <td>{{ showDateTime($w->created_at) }}</td>
                                            <td>{{ showAmount($w->amount) }}</td>
                                            <td>{{ __($w->status) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">@lang('No requests yet')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($withdrawals->hasPages())
                            {{ paginateLinks($withdrawals) }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
