<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CBC Report Card</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        h2 { font-size: 14px; margin: 16px 0 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
        .meta { margin-bottom: 12px; }
    </style>
</head>
<body>
    <h1>{{ $school->school_name ?? 'School' }}</h1>
    <div class="meta">
        <div><strong>Learner:</strong> {{ $student->full_name ?? '—' }}</div>
        <div><strong>Admission:</strong> {{ $student->admission ?? '—' }}</div>
        <div><strong>Term:</strong> {{ $term->name ?? '—' }}</div>
        <div><strong>Scheme:</strong> {{ $card->scheme_snapshot['name'] ?? 'CBC' }}</div>
    </div>

    <h2>Competency summary</h2>
    <table>
        <thead>
            <tr>
                <th>Learning area / subject</th>
                <th>Band</th>
                <th>Comment</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->subject_name }}</td>
                    <td>{{ $item->band_code ?? '—' }}</td>
                    <td>{{ $item->teacher_comment ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 24px; font-size: 10px; color: #666;">
        EE Exceeding · ME Meeting · AE Approaching · BE Below Expectations
    </p>
</body>
</html>
