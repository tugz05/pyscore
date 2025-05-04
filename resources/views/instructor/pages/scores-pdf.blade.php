<!DOCTYPE html>
<html>

<head>
    <title>{{ $activity->title }} - Scores</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        .header-info {
            margin-bottom: 20px;
        }

        .header-info p {
            margin: 5px 0;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .summary-item {
            flex: 1;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 0.8em;
            color: #666;
        }
    </style>
</head>

<body>
    <div id="pdfContent">
        <div class="header"
            style="display: flex; justify-content: center; align-items: center; flex-direction: column; margin-bottom: 20px;">
            <img src="{{ asset('assets/png/nemsu_logo.png') }}" alt="Logo"
                style="width: 50px; height: auto; margin-bottom: 4px;">
            <h4 style="margin: 0;">NORTH EASTERN MINDANAO STATE UNIVERSITY</h4>
            <h5 style="margin: 0;">College of Information Technology Education</h5>
        </div>


        <div class="header-info">
            <p><strong>Class name:</strong> {{ $activity->classlist->name }}</p>
            <p><strong>Instructor name:</strong> {{ $activity->user->name }}</p>
            <p><strong>Activity name:</strong> {{ $activity->title }}</p>
            <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($activity->due_date)->format('F d, Y') }}</p>
            <p><strong>Due Time:</strong> {{ \Carbon\Carbon::parse($activity->due_time)->format('h:i A') }}</p>

        </div>

        <div class="summary">
            <div class="summary-item"><strong>Submitted:</strong> {{ $submitted }}</div>
            <div class="summary-item"><strong>Pending:</strong> {{ $pending }}</div>
            <div class="summary-item"><strong>Missing:</strong> {{ $missing }}</div>
        </div>


        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $students = $students->sortBy(function ($student) {
                        $parts = explode(' ', trim($student->name));
                        return strtolower(end($parts));
                    });
                @endphp

                @foreach ($students as $student)
                    <tr>
                        <td>{{ $student->name }}</td>
                        <td>
                            @if (isset($outputs[$student->id]))
                                @if ($outputs[$student->id]->status === 'submitted')
                                    {{ $outputs[$student->id]->score }}/{{ $activity->points }}
                                @elseif($outputs[$student->id]->status === 'missing')
                                    Missing
                                @else
                                    Pending
                                @endif
                            @else
                                Pending
                            @endif
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>

        <div class="footer">
            Generated on {{ now()->format('F d, Y h:i A') }}
        </div>
    </div>

    <!-- Include html2pdf.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <!-- Auto-generate and download PDF -->
    <script>
        window.onload = function() {
            const element = document.getElementById('pdfContent');
            html2pdf().from(element).set({
                margin: 0.5,
                filename: '{{ Str::slug($activity->title) }}_scores.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'in',
                    format: 'letter',
                    orientation: 'portrait'
                }
            }).save().then(() => {
                // Delay a little tao ensure file is fully saved before closing

                window.close();

            });
        };
    </script>

</body>

</html>
