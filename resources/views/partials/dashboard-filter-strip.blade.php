@php
    $filterAction = $filterAction ?? route('dashboard');
    $mode = $period['mode'];
    $selectedDate = $period['selectedDate'];
    $selectedMonth = $period['selectedMonth'];
    $selectedYear = $period['selectedYear'];
    $selectedDari = $period['selectedDari'];
    $selectedSampai = $period['selectedSampai'];
@endphp

<form method="GET" action="{{ $filterAction }}" class="vx-filter-strip">
    <span class="vx-filter-label">Filter:</span>
    <select name="mode" class="vx-filter-select" onchange="this.form.submit()">
        <option value="harian" @selected($mode === 'harian')>Harian</option>
        <option value="bulanan" @selected($mode === 'bulanan')>Bulanan</option>
        <option value="tahunan" @selected($mode === 'tahunan')>Tahunan</option>
        <option value="range" @selected($mode === 'range')>Range tanggal</option>
    </select>
    @if ($mode === 'harian')
        <input type="date" name="tanggal" value="{{ $selectedDate }}" class="vx-filter-date">
    @elseif ($mode === 'bulanan')
        <input type="month" name="bulan" value="{{ $selectedMonth }}" class="vx-filter-date">
    @elseif ($mode === 'tahunan')
        <input type="number" name="tahun" min="2000" max="2100" value="{{ $selectedYear }}" class="vx-filter-date vx-filter-date-year">
    @elseif ($mode === 'range')
        <input type="date" name="dari" value="{{ $selectedDari }}" class="vx-filter-date">
        <input type="date" name="sampai" value="{{ $selectedSampai }}" class="vx-filter-date">
    @endif
    <button type="submit" class="vx-filter-apply">Terapkan</button>
</form>
