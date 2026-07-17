@props([
    'title',
    'dateFrom' => null,
    'dateTo' => null,
    'search' => null,
    'searchLabel' => 'Search',
    'branch' => null,
])

<p style="color: #aaa; font-size: 0.9em; letter-spacing: 0.5px">{{ $title }}</p>

<div class="invCenter">
  <table class="invCenterTbl">
    <tbody>
      <tr>
        <td class="col-sm-3">Date From :</td>
        <td class="col-sm-3">{{ filled($dateFrom) ? $dateFrom : 'All / session date' }}</td>
        <td class="col-sm-4"></td>
      </tr>
      <tr>
        <td class="col-sm-3">Date To :</td>
        <td class="col-sm-3">{{ filled($dateTo) ? $dateTo : '—' }}</td>
      </tr>
      @if (filled($branch) && $branch !== 'All Branches')
        <tr>
          <td class="col-sm-3">Branch :</td>
          <td class="col-sm-3">{{ $branch }}</td>
        </tr>
      @endif
      @if (filled($search))
        <tr>
          <td class="col-sm-3">{{ $searchLabel }} :</td>
          <td class="col-sm-3">{{ $search }}</td>
        </tr>
      @endif
    </tbody>
  </table>
</div>
