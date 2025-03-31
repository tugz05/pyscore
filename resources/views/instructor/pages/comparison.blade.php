<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold">Similar Submissions</h4>
        <button class="btn btn-primary" onclick="fetchComparisonData()">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
    <div id="comparison-results" class="mt-3"></div>
</div>

<!-- Modal for displaying full code -->
<div class="modal fade" id="codeModal" tabindex="-1" aria-labelledby="codeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="codeModalLabel"></h5> <!-- Dynamic Title -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre class="bg-dark text-white p-3 rounded" id="fullCode"></pre>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", fetchComparisonData);

// Function to fetch and update the comparison data
function fetchComparisonData() {
    let refreshButton = document.querySelector("button.btn-primary");
    refreshButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
    refreshButton.disabled = true;

    fetch("{{ route('activity.comparison', $activity->id) }}")
        .then(response => response.json())
        .then(data => {
            let outputHtml = "";
            if (data.length > 0) {
                data.forEach(group => {
                    outputHtml += `
                        <div class="card my-3 shadow-sm">
                            <div class="card-body">
                                <h6 class="fw-bold">Common Code:</h6>
                                <pre class="bg-dark text-white p-3 rounded">${group.original_code}</pre>
                                <p class="fw-bold mt-2">Submitted by:</p>
                                <ul>
                                    ${group.students.map(student => `
                                        <li>
                                            <a href="#" class="text-primary" onclick="showCode('${encodeURIComponent(student.full_code)}', '${student.name}')">
                                                ${student.name}
                                            </a>
                                        </li>
                                    `).join('')}
                                </ul>
                            </div>
                        </div>`;
                });
            } else {
                outputHtml = "<p class='text-muted'>No duplicate submissions found.</p>";
            }
            document.getElementById("comparison-results").innerHTML = outputHtml;

            // Reset button after loading
            refreshButton.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
            refreshButton.disabled = false;
        })
        .catch(error => {
            console.error("Error fetching comparison data:", error);
            refreshButton.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
            refreshButton.disabled = false;
        });
}

// Function to show full submitted code in the modal with student's name
function showCode(code, studentName) {
    document.getElementById("codeModalLabel").textContent = `${studentName}'s Submitted Code`;
    document.getElementById("fullCode").textContent = decodeURIComponent(code);
    var codeModal = new bootstrap.Modal(document.getElementById('codeModal'));
    codeModal.show();
}
</script>
