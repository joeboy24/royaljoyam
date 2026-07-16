@extends('layouts.dashlay')

@section('content')

  <div class="content dash-expenses-content">
    <div class="container-fluid dash-expenses-body">

      @include('inc.messages')

      <div class="card dash-expenses-card">
        <x-dash-page-header
          title="Expenditure"
          subtitle="Record branch expenses for {{ $salesDateLabel }}."
          icon="fa fa-money"
        >
          <x-slot:actions>
            <a href="/sales" class="dash-page-header-btn inventory-action-btn dash-tip" data-tip="Back to sales">
              <i class="fa fa-shopping-basket"></i>
              <span>Sales</span>
            </a>
          </x-slot:actions>
        </x-dash-page-header>

        <div class="card-body dash-form-body dash-expenses-panel">
          <div class="dash-expenses-inner">
          <section class="dash-expenses-form-section">
            <h6 class="inventory-edit-section-title"><i class="fa fa-plus-circle"></i> Add expense</h6>

            <form action="{{ route('expenses.store') }}" method="POST" class="dash-expenses-form">
              @csrf

              <div class="dash-expenses-form-grid">
                <label class="inventory-edit-field">
                  <span class="inventory-edit-label">Title</span>
                  <input
                    type="text"
                    class="inventory-edit-input @error('title') is-invalid @enderror"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="e.g. Internet payment"
                    required
                    autofocus
                  />
                  @error('title')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                </label>

                <label class="inventory-edit-field">
                  <span class="inventory-edit-label">Cost (Gh₵)</span>
                  <input
                    type="number"
                    step="any"
                    min="0"
                    class="inventory-edit-input @error('expense_cost') is-invalid @enderror"
                    name="expense_cost"
                    value="{{ old('expense_cost') }}"
                    placeholder="e.g. 1000"
                    required
                  />
                  @error('expense_cost')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                </label>

                <label class="inventory-edit-field dash-expenses-field-branch">
                  <span class="inventory-edit-label">Branch</span>
                  @if ($isAdmin)
                    <select name="branch" class="inventory-edit-input inventory-edit-select @error('branch') is-invalid @enderror" required>
                      <option value="" disabled {{ old('branch') ? '' : 'selected' }}>Select branch</option>
                      @foreach ($activeBranches as $branch)
                        <option value="{{ $branch->id }}" @selected(old('branch') == $branch->id)>
                          {{ $branch->name }}
                        </option>
                      @endforeach
                    </select>
                    @error('branch')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                  @else
                    <div class="dash-expenses-branch-readonly">
                      <i class="fa fa-map-marker" aria-hidden="true"></i>
                      <span>{{ $activeBranches->first()->name ?? auth()->user()->status }}</span>
                    </div>
                  @endif
                </label>

                <label class="inventory-edit-field dash-expenses-field-desc">
                  <span class="inventory-edit-label">Description</span>
                  <textarea
                    name="desc"
                    class="inventory-edit-input inventory-edit-textarea @error('desc') is-invalid @enderror"
                    rows="2"
                    placeholder="Optional details"
                  >{{ old('desc') }}</textarea>
                  @error('desc')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                </label>
              </div>

              <div class="dash-expenses-form-footer">
                <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary">
                  <i class="fa fa-save"></i>
                  Save expense
                </button>
              </div>
            </form>
          </section>

          <div class="dash-expenses-divider"></div>

          <section class="dash-expenses-list-section">
            <div class="dist-section-toolbar">
              <h6 class="inventory-edit-section-title"><i class="fa fa-list"></i> Expenses for {{ $salesDateLabel }}</h6>
              <span class="dash-config-branch-count">{{ $expenses->count() }}</span>
            </div>

            @if ($expenses->count() > 0)
              <div class="table-responsive dist-branch-table-wrap dash-config-branch-table-wrap dash-expenses-table-wrap">
                <table class="table mt dist-branch-table dash-config-branch-table dash-expenses-table">
                  <thead class="text-secondary hideMe">
                    <tr>
                      <th>#</th>
                      <th>Branch</th>
                      <th>Title</th>
                      <th>Cost</th>
                      <th>Date</th>
                      <th class="ryt actsize">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($expenses as $expense)
                      <tr @class(['rowColour' => $loop->even])>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $expense->companybranch->name ?? '—' }}</td>
                        <td>
                          <span class="dash-expenses-title">{{ $expense->title }}</span>
                          @if ($expense->desc)
                            <p class="dash-expenses-desc">{{ $expense->desc }}</p>
                          @endif
                        </td>
                        <td class="dash-expenses-amount">Gh₵ {{ number_format((float) $expense->expense_cost, 2) }}</td>
                        <td>{{ $expense->created_at?->format('D, M j') }}</td>
                        <td class="ryt">
                          <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="dash-expenses-delete-form">
                            @csrf
                            @method('DELETE')
                            <button
                              type="submit"
                              class="inventory-action-btn inventory-action-btn-icon dash-config-delete-btn dash-tip"
                              data-tip="Delete expense"
                              onclick="return confirm('Delete this expense record for Gh₵ {{ number_format((float) $expense->expense_cost, 2) }}?');"
                            >
                              <i class="fa fa-trash"></i>
                            </button>
                          </form>
                        </td>
                      </tr>
                    @endforeach
                    <tr class="dash-expenses-total-row">
                      <td colspan="3"><strong>Total ({{ $expenses->count() }} {{ Str::plural('record', $expenses->count()) }})</strong></td>
                      <td class="dash-expenses-amount">Gh₵ {{ number_format($expenseTotal, 2) }}</td>
                      <td colspan="2"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            @else
              <div class="dash-empty-state dash-config-empty dash-expenses-empty">
                <span class="dash-empty-state-icon" aria-hidden="true"><i class="fa fa-money"></i></span>
                <p class="dash-empty-state-title">No expenses yet</p>
                <p class="dash-empty-state-text">Add the first expense for <strong>{{ $salesDateLabel }}</strong> using the form above.</p>
              </div>
            @endif
          </section>
          </div>
        </div>
      </div>

    </div>
  </div>

@endsection

@section('footer')
  <link rel="stylesheet" href="/maindir/css/dash-expenses.css?v=6">
@endsection
