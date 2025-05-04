<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<div class="d-flex align-items-center mb-3">
    <button id="refreshSummary" class="btn btn-primary me-2">
        <i class="fas fa-sync-alt"></i> Refresh
    </button>
</div>

<div id="summaryContainer" class="mt-3"></div>

@php
    $classlistId = $classlist->id ?? null;
@endphp
<style>
    table thead th {
        background-color: rgb(50, 89, 202);
        color: white;
        text-align: center !important;
        vertical-align: middle !important;
    }

    table tbody td {
        text-align: center !important;
        vertical-align: middle !important;
    }

    .student-name {
        font-weight: bold;
    }
</style>

<!-- jQuery + DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const classlistId = "{{ $classlistId }}";
        let reportData = [];
        let activityTitles = [];

        document.querySelector('a[href="#summary_report"]').addEventListener('shown.bs.tab', function () {
            fetchSummaryData();
        });

        document.getElementById('refreshSummary').addEventListener('click', function () {
            fetchSummaryData();
        });

        document.getElementById('searchStudent')?.addEventListener('input', function () {
            $('#summaryTable').DataTable().search(this.value).draw();
        });

        function fetchSummaryData() {
            const container = document.getElementById('summaryContainer');
            const refreshBtn = document.getElementById('refreshSummary');

            refreshBtn.innerHTML = `<i class="fas fa-sync-alt fa-spin"></i> Refreshing...`;
            refreshBtn.disabled = true;
            container.innerHTML = '<p><em>Loading summary report...</em></p>';

            fetch(`/instructor/class/${classlistId}/summary-report`)
                .then(response => {
                    if (!response.ok) throw new Error('Failed to fetch summary');
                    return response.json();
                })
                .then(data => {
                    reportData = data;
                    activityTitles = (data[0]?.activities || []).map(a => a.title);
                    renderSummaryTable(reportData);
                })
                .catch(error => {
                    console.error(error);
                    container.innerHTML = '<p class="text-danger">Failed to load summary report.</p>';
                })
                .finally(() => {
                    refreshBtn.innerHTML = `<i class="fas fa-sync-alt"></i> Refresh`;
                    refreshBtn.disabled = false;
                });
        }

        function renderSummaryTable(data) {
            const container = document.getElementById('summaryContainer');

            if (!data.length) {
                container.innerHTML = '<p>No students found.</p>';
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table id="summaryTable" class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Student Name</th>`;

            activityTitles.forEach(title => {
                html += `<th>${title}</th>`;
            });

            html += `</tr></thead><tbody>`;

            data.forEach(student => {
                html += `<tr><td class="student-name">${student.name}</td>`;

                activityTitles.forEach(title => {
                    const activity = student.activities.find(a => a.title === title);
                    html += `<td>${activity?.score ?? '--'}</td>`;
                });

                html += `</tr>`;
            });

            html += `</tbody></table></div>`;

            container.innerHTML = html;

            $('#summaryTable').DataTable({
    paging: true,
    searching: true,
    ordering: true,
    responsive: true,
    lengthChange: false,
    language: {
        searchPlaceholder: "Type student name",
        search: ""
    }
});

        }
    });
</script>
