<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Travel Itinerary – WayWay</title>
    <style>
        @page {
            margin: 0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #4E4E4E;
            background: #ffffff;
            /* body padding = consistent margin on every page in dompdf */
            padding: 36px 40px 40px 40px;
        }

        /* ===== TOP STRIP ===== */
        /* sits at very top (no padding-top on body), then space below */
        .header-strip {
            background: #9FCCDA;
            height: 6px;
            width: auto;
            margin-top: -36px;
            margin-left: -40px;
            margin-right: -40px;
            margin-bottom: 28px;
        }

        /* ===== HEADER ===== */
        .header-main {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .header-main td { vertical-align: middle; }

        .brand-name {
            font-size: 10px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: #9FCCDA;
            margin-bottom: 6px;
        }

        .doc-title {
            font-size: 30px;
            font-weight: bold;
            color: #1a1a1a;
            line-height: 1.05;
        }

        .doc-title-accent { color: #2a6e8a; }

        .doc-subtitle {
            font-size: 12px;
            color: #9FCCDA;
            margin-top: 5px;
            letter-spacing: 1px;
        }

        .meta-right { text-align: right; vertical-align: middle; }

        .meta-item {
            display: inline-block;
            margin-left: 22px;
            text-align: center;
            vertical-align: top;
        }

        .meta-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #aab4bc;
            margin-bottom: 3px;
        }

        .meta-value {
            font-size: 13px;
            font-weight: bold;
            color: #1a1a1a;
        }

        /* ===== STATS BAR ===== */
        .stats-bar {
            background: #f2f8fa;
            border: 1px solid #d8edf3;
            border-radius: 7px;
            overflow: hidden;
            margin-bottom: 22px;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
        }

        .stats-table td {
            text-align: center;
            padding: 12px 8px;
            border-right: 1px solid #d8edf3;
        }

        .stats-table td:last-child { border-right: none; }

        .stat-num {
            font-size: 16px;
            font-weight: bold;
            color: #2a6e8a;
        }

        .stat-lbl {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #9aacb5;
            margin-top: 3px;
        }

        /* ===== SECTION LABEL ===== */
        .section-label {
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #d8edf3;
        }

        .section-label-text {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #2a6e8a;
        }

        /* ===== STOP ROWS ===== */
        .stop-row {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
            margin-bottom: 12px;
        }

        .col-time {
            width: 80px;
            vertical-align: top;
            padding: 0 14px 0 0;
        }

        .time-badge {
            background: #edf7fa;
            border-radius: 5px;
            padding: 8px 8px;
            text-align: center;
            border-left: 3px solid #9FCCDA;
        }

        .time-of-day {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #9aacb5;
            margin-bottom: 3px;
        }

        .time-range {
            font-size: 13px;
            font-weight: bold;
            color: #2a6e8a;
        }

        .time-depart {
            font-size: 11px;
            color: #b0b8bf;
            margin-top: 3px;
        }

        .col-dot {
            width: 22px;
            text-align: center;
            vertical-align: top;
            padding: 4px 4px 0;
        }

        .dot-outer {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #9FCCDA;
            margin: 0 auto 4px;
        }

        .dot-outer.last { border-color: #F5DBB4; }

        .dot-line {
            width: 2px;
            height: 62px;
            background: #cce8f0;
            margin: 0 auto;
        }

        .col-content {
            vertical-align: top;
            padding: 0 0 12px 14px;
        }

        .stop-card {
            background: #ffffff;
            border: 1px solid #ddedf3;
            border-radius: 7px;
            padding: 12px 16px;
        }

        .stop-card.featured {
            border-left: 4px solid #F5DBB4;
        }

        .stop-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 7px;
        }

        .stop-header-table td { vertical-align: middle; }

        .stop-name {
            font-size: 14px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .stop-number {
            font-size: 11px;
            color: #c0c8d0;
            text-align: right;
            white-space: nowrap;
            padding-left: 8px;
        }

        .tag-row { margin-bottom: 8px; }

        .tag {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            margin-right: 5px;
        }

        .tag-cat    { background: #e5f4f9; color: #2a6e8a; }
        .tag-price  { background: #fef6e8; color: #a0733a; border: 1px solid #f5dbb4; }
        .tag-dur    { background: #f4f6f8; color: #7a8a94; }
        .tag-travel { background: #f0f5f0; color: #4a7a4a; }
        .tag-feat   { background: #F5DBB4; color: #8a5a20; }

        .stop-desc {
            font-size: 11px;
            color: #5e6e78;
            line-height: 1.7;
            border-top: 1px solid #edf4f7;
            padding-top: 8px;
            margin-top: 4px;
        }

        /* ===== TIPS ===== */
        .tips-section {
            margin-top: 22px;
            border-top: 2px solid #d8edf3;
            padding-top: 16px;
            page-break-inside: avoid;
        }

        .tips-table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Each tip column takes exactly half */
        .tips-col {
            width: 50%;
            vertical-align: top;
        }

        .tips-col-left  { padding-right: 20px; }
        .tips-col-right { padding-left: 20px; border-left: 1px solid #d8edf3; }

        .tip-head {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #2a6e8a;
            margin-bottom: 10px;
        }

        /* tip rows as plain table for dompdf compat */
        .tip-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 7px;
        }

        .tip-row td { vertical-align: top; }

        .tip-bullet {
            width: 16px;
            font-size: 13px;
            color: #F5DBB4;
            line-height: 1.55;
        }

        .tip-text {
            font-size: 11px;
            color: #5a6a74;
            line-height: 1.6;
        }

        /* ===== FOOTER ===== */
        .footer-strip {
            margin-top: 22px;
            margin-left: -40px;
            margin-right: -40px;
            background: #f2f8fa;
            border-top: 1px solid #d8edf3;
            padding: 12px 40px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td { vertical-align: middle; }

        .footer-brand {
            font-size: 12px;
            font-weight: bold;
            color: #2a6e8a;
            letter-spacing: 0.8px;
        }

        .footer-info {
            font-size: 11px;
            color: #aab4bc;
            text-align: right;
        }

        .footer-dot { margin: 0 5px; color: #ccd8de; }
    </style>
</head>
<body>

@php
    $schedule = $itinerary['schedule'] ?? [];
    $route    = $itinerary['route']    ?? [];

    $companionMap = [
        'solo'     => 'Solo Traveler',
        'pasangan' => 'Couple',
        'keluarga' => 'Family',
        'grup'     => 'Group',
    ];
    $companionLabel = $companionMap[$history->companion] ?? $history->companion;

    $totalFees = array_sum(array_column($route, 'harga'));

    $tipsMap = [
        'solo' => [
            'Travel light — one carry-on is enough for a day trip.',
            'Use Grab for fast and affordable transfers between stops.',
            'Check opening hours the night before to avoid surprises.',
        ],
        'pasangan' => [
            'Head to coastal spots around sunset for golden-hour views.',
            'Book dinner reservations in advance, especially on weekends.',
            'Bring cash — several local attractions are cash-only.',
        ],
        'keluarga' => [
            'Pack sunscreen, hats, and extra water for the little ones.',
            'Schedule a proper rest break after lunch so everyone recharges.',
            'Check age or height restrictions at venues before you arrive.',
        ],
        'grup' => [
            'Agree on a shared meeting point before splitting up at big sites.',
            'Settle group expenses together at the end of the day.',
            'Leave buffer time between stops — large groups always run late.',
        ],
    ];
    $tips = $tipsMap[$history->companion] ?? $tipsMap['solo'];
@endphp

{{-- TOP STRIP --}}
<div class="header-strip"></div>

{{-- HEADER --}}
<table class="header-main">
    <tr>
        <td>
            {{-- Logo + nama brand sejajar --}}
            <div style="display: flex; align-items: center; gap: 6px;">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/logo/logoo.png'))) }}"
                     style="height: 40px; width: auto; padding-top: 6pt;"
                     alt="WayWay">
                <span style="font-weight: bold; color: #415c7f; font-size: 14px; vertical-align: middle;">
                    WayWay
                </span>
            </div>

            <div class="doc-title">Travel <span class="doc-title-accent">Itinerary</span></div>
            <div class="doc-subtitle">Batam, Indonesia</div>
        </td>
        <td class="meta-right">
            <div class="meta-item">
                <div class="meta-label">Date</div>
                <div class="meta-value">{{ \Carbon\Carbon::parse($history->tanggal_kunjungan)->format('d M Y') }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Departing From</div>
                <div class="meta-value">{{ $history->origin_label ?: 'Batam Center' }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Traveling As</div>
                <div class="meta-value">{{ $companionLabel }}</div>
            </div>
        </td>
    </tr>
</table>

{{-- STATS BAR --}}
<div class="stats-bar">
    <table class="stats-table">
        <tr>
            <td>
                <div class="stat-num">{{ $history->stop_count }}</div>
                <div class="stat-lbl">Destinations</div>
            </td>
            <td>
                <div class="stat-num">{{ $history->total_distance }} km</div>
                <div class="stat-lbl">Total Distance</div>
            </td>
            <td>
                <div class="stat-num">{{ $history->formatted_duration }}</div>
                <div class="stat-lbl">Drive Time</div>
            </td>
            <td>
                <div class="stat-num">Rp {{ number_format($totalFees, 0, ',', '.') }}</div>
                <div class="stat-lbl">Est. Entrance Fees</div>
            </td>
            <td>
                <div class="stat-num">Rp {{ number_format($history->budget, 0, ',', '.') }}</div>
                <div class="stat-lbl">Budget / Person</div>
            </td>
        </tr>
    </table>
</div>

{{-- SECTION LABEL --}}
<div class="section-label">
    <span class="section-label-text">Your Destinations</span>
</div>

{{-- STOPS --}}
@foreach($schedule as $i => $item)
@php
    $stop       = $item['stop'];
    $isLast     = $i === count($schedule) - 1;
    $hour       = (int) explode(':', $item['arrival_time'])[0];
    if      ($hour < 12) $tod = 'Morning';
    elseif  ($hour < 14) $tod = 'Midday';
    elseif  ($hour < 17) $tod = 'Afternoon';
    else                 $tod = 'Evening';
    $isFeatured = !empty($stop['is_featured']);
    $stopNum    = $i + 1;
    $totalStops = count($schedule);
@endphp

<table class="stop-row">
    <tr>
        <td class="col-time">
            <div class="time-badge">
                <div class="time-of-day">{{ $tod }}</div>
                <div class="time-range">{{ $item['arrival_time'] }}</div>
                <div class="time-depart">– {{ $item['departure_time'] }}</div>
            </div>
        </td>

        <td class="col-dot">
            <div class="dot-outer {{ $isLast ? 'last' : '' }}"></div>
            @if(!$isLast)
            <div class="dot-line"></div>
            @endif
        </td>

        <td class="col-content">
            <div class="stop-card {{ $isFeatured ? 'featured' : '' }}">
                <table class="stop-header-table">
                    <tr>
                        <td><div class="stop-name">{{ $stop['nama'] }}</div></td>
                        <td><div class="stop-number">Stop {{ $stopNum }} / {{ $totalStops }}</div></td>
                    </tr>
                </table>

                <div class="tag-row">
                    <span class="tag tag-cat">{{ $stop['kategori'] }}</span>
                    <span class="tag tag-price">Rp {{ number_format($stop['harga'], 0, ',', '.') }}</span>
                    <span class="tag tag-dur">{{ $stop['visit_duration'] }} min</span>
                    @if($stop['order'] > 1)
                    <span class="tag tag-travel">{{ $stop['road_duration_min'] ?? $stop['travel_minutes'] }} min drive</span>
                    @endif
                    @if($isFeatured)
                    <span class="tag tag-feat">Recommended</span>
                    @endif
                </div>

                @if(!empty($stop['deskripsi']))
                <div class="stop-desc">{{ \Str::limit($stop['deskripsi'], 320) }}</div>
                @endif
            </div>
        </td>
    </tr>
</table>
@endforeach

{{-- TIPS --}}
<div class="tips-section">
    <table class="tips-table">
        <tr>
            <td class="tips-col tips-col-left">
                <div class="tip-head">Travel Tips</div>
                @foreach($tips as $tip)
                <table class="tip-row">
                    <tr>
                        <td class="tip-bullet">&#9670;</td>
                        <td class="tip-text">{{ $tip }}</td>
                    </tr>
                </table>
                @endforeach
            </td>
            <td class="tips-col tips-col-right">
                <div class="tip-head">Getting Around</div>
                <table class="tip-row">
                    <tr>
                        <td class="tip-bullet">&#9670;</td>
                        <td class="tip-text">Grab or metered taxi is the easiest option between stops.</td>
                    </tr>
                </table>
                <table class="tip-row">
                    <tr>
                        <td class="tip-bullet">&#9670;</td>
                        <td class="tip-text">Coastal spots typically require private or chartered transport.</td>
                    </tr>
                </table>
                <table class="tip-row">
                    <tr>
                        <td class="tip-bullet">&#9670;</td>
                        <td class="tip-text">Allow extra time during peak hours, especially 12:00–14:00.</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

{{-- FOOTER --}}
<div class="footer-strip">
    <table class="footer-table">
        <tr>
            <td><div class="footer-brand">WayWay Travel Planner</div></td>
            <td>
                <div class="footer-info">
                    {{ \Carbon\Carbon::parse($history->tanggal_kunjungan)->format('d F Y') }}
                    <span class="footer-dot">&middot;</span>
                    {{ $history->stop_count }} destination{{ $history->stop_count != 1 ? 's' : '' }}
                    <span class="footer-dot">&middot;</span>
                    Budget Rp {{ number_format($history->budget, 0, ',', '.') }} / person
                </div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>