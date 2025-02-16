@extends('instructor.dashboard')
@section('content')

<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 grid-margin stretch-card">
            <div class="card shadow-sm border-0 rounded">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title text-center">Certificate Transactions</h4>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                            <i class="fas fa-plus"></i> Add Section
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="myTable" class="table table-hover table-bordered text-center align-middle" style="width: 100%;">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td>1</td>
                                    <td>John Doe</td>
                                    <td>johndoe@example.com</td>
                                    <td><button class="btn btn-sm btn-primary">View Students</button></td>
                                </tr>
                                <tr class="text-center">
                                    <td>2</td>
                                    <td>Jane Smith</td>
                                    <td>janesmith@example.com</td>
                                    <td><button class="btn btn-sm btn-primary">View Students</button></td>
                                </tr>
                                <tr class="text-center">
                                    <td>3</td>
                                    <td>Michael Johnson</td>
                                    <td>michaelj@example.com</td>
                                    <td><button class="btn btn-sm btn-primary">View Students</button></td>
                                </tr>
                                <tr class="text-center">
                                    <td>4</td>
                                    <td>Emily Davis</td>
                                    <td>emilyd@example.com</td>
                                    <td><button class="btn btn-sm btn-primary">View Students</button></td>
                                </tr>
                                <tr class="text-center">
                                    <td>5</td>
                                    <td>Chris Wilson</td>
                                    <td>chrisw@example.com</td>
                                    <td><button class="btn btn-sm btn-primary">View Students</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSectionModalLabel">Add Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="sectionName" class="form-label">Section Name</label>
                        <input type="text" class="form-control" id="sectionName" placeholder="Enter section name">
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalLabel">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="transactionDetails">Loading...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="imageViewerModal" tabindex="-1" aria-labelledby="imageViewerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageViewerModalLabel">Image Viewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imageViewer" src="" alt="Viewing Image" style="max-width: 100%; height: auto; border-radius: 8px;">
            </div>
        </div>
    </div>
</div>

@endsection
@push('script')
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "ordering": true,
            "paging": true,
            "searching": true
        });
    });
</script>
@endpush
