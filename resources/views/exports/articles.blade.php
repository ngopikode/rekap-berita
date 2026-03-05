<table>
    <thead>
        <tr>
            <th colspan="{{ $daysInMonth + 3 }}" style="font-size: 16px; font-weight: bold; text-align: center; background-color: #f0f0f0;">
                REKAP BERITA BULAN {{ strtoupper(now()->translatedFormat('F Y')) }}
            </th>
        </tr>
        <tr>
            <th style="width: 50px; font-weight: bold; background-color: #e0e0e0;">NO</th>
            <th style="width: 250px; font-weight: bold; background-color: #e0e0e0;">NAMA MEDIA</th>
            @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $date = now()->setDay($day);
                    $isSunday = $date->isSunday();
                    $bgClass = $isSunday ? 'background-color: #ff0000; color: #ffffff;' : 'background-color: #e0e0e0;';
                @endphp
                <th style="width: 35px; font-weight: bold; {{ $bgClass }}">{{ $day }}</th>
            @endfor
            <th style="width: 60px; font-weight: bold; background-color: #ffff00;">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($publishers as $index => $publisher)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: left; font-weight: bold;">{{ $publisher->name }}</td>
                @php
                    $total = 0;
                    $articlesByDay = $publisher->articles->groupBy(fn($article) => \Illuminate\Support\Carbon::parse($article->published_at)->day);
                @endphp
                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $count = $articlesByDay->get($day, collect())->count();
                        $total += $count;
                        $date = now()->setDay($day);
                        $isSunday = $date->isSunday();
                        $bgClass = $isSunday ? 'background-color: #ffcccc;' : '';
                    @endphp
                    <td style="text-align: center; {{ $bgClass }}">
                        {{ $count > 0 ? $count : '' }}
                    </td>
                @endfor
                <td style="text-align: center; font-weight: bold; background-color: #ffffcc;">{{ $total }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; background-color: #ffffcc;">TOTAL POSTINGAN PER TANGGAL</td>
            @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $date = now()->setDay($day);
                    $isSunday = $date->isSunday();
                    $bgClass = $isSunday ? 'background-color: #ff0000; color: #ffffff;' : 'background-color: #ffffcc;';
                @endphp
                <td style="text-align: center; font-weight: bold; {{ $bgClass }}">
                    {{ $totalPerDate[$day] > 0 ? $totalPerDate[$day] : '' }}
                </td>
            @endfor
            <td style="text-align: center; font-weight: bold; background-color: #ffff00;">{{ $grandTotal }}</td>
        </tr>
    </tfoot>
</table>
