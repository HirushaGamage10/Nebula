@extends('inc.app')

@section('title', 'NEBULA | File Management')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">File Management Dashboard</h2>
            <hr>
            
            <p class="text-center mb-4">
                Upload, manage, and organize files for the Nebula Institute Management System.
            </p>

            <!-- File Upload Section -->
            <h5 class="mb-3">File Upload</h5>
            
            <div class="row mb-3">
                <label for="uploadCategory" class="col-sm-2 col-form-label">File Category</label>
                <div class="col-sm-4">
                    <select class="form-select" id="uploadCategory">
                        <option value="all">All Files</option>
                        <option value="images">Images</option>
                        <option value="documents">Documents</option>
                        <option value="certificates">Certificates</option>
                        <option value="photos">Photos</option>
                    </select>
                </div>
                <label for="uploadDirectory" class="col-sm-2 col-form-label">Directory</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="uploadDirectory" placeholder="uploads" value="uploads">
                </div>
            </div>

            <div class="row mb-3">
                <label for="uploadFile" class="col-sm-2 col-form-label">Select File<span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input type="file" class="form-control" id="uploadFile" accept="*/*">
                    <small class="text-muted">Maximum file size: 10MB</small>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-sm-10 offset-sm-2">
                    <button type="button" class="btn btn-primary me-2" id="uploadSingleFile">
                        <i class="bx bx-upload me-1"></i>Upload File
                    </button>
                    <button type="button" class="btn btn-success me-2" id="uploadMultipleFiles">
                        <i class="bx bx-folder-plus me-1"></i>Upload Multiple Files
                    </button>
                </div>
            </div>

            <hr class="my-4">

            <!-- File Management Section -->
            <h5 class="mb-3">File Management</h5>
            
            <div class="row mb-3">
                <label for="fileCategory" class="col-sm-2 col-form-label">Filter by Category</label>
                <div class="col-sm-4">
                    <select class="form-select" id="fileCategory">
                        <option value="">All Categories</option>
                        <option value="images">Images</option>
                        <option value="documents">Documents</option>
                        <option value="certificates">Certificates</option>
                        <option value="photos">Photos</option>
                    </select>
                </div>
                <label for="fileDirectory" class="col-sm-2 col-form-label">Filter by Directory</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="fileDirectory" placeholder="Enter directory path">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-sm-10 offset-sm-2">
                    <button type="button" class="btn btn-info me-2" id="listFiles">
                        <i class="bx bx-list-ul me-1"></i>List Files
                    </button>
                    <button type="button" class="btn btn-warning me-2" id="cleanupFiles">
                        <i class="bx bx-trash me-1"></i>Cleanup Orphaned Files
                    </button>
                </div>
            </div>

            <!-- File List -->
            <div id="fileList" style="display: none;">
                <h6 class="mb-3">File List</h6>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Size</th>
                                        <th>Type</th>
                                        <th>Upload Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="filesTableBody">
                                    <!-- Files will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Storage Statistics -->
            <h5 class="mb-3">Storage Statistics</h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="bx bx-hdd text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2">Total Storage</h6>
                            <h4 id="totalStorage">0 MB</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="bx bx-file text-success" style="font-size: 2rem;"></i>
                            <h6 class="mt-2">Total Files</h6>
                            <h4 id="totalFiles">0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="bx bx-folder text-info" style="font-size: 2rem;"></i>
                            <h6 class="mt-2">Directories</h6>
                            <h4 id="totalDirectories">0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="bx bx-calendar text-warning" style="font-size: 2rem;"></i>
                            <h6 class="mt-2">Recent Uploads</h6>
                            <h4 id="recentUploads">0</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Storage Usage by Category -->
            <hr class="my-4">
            <h5 class="mb-3">Storage Usage by Category</h5>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Images</h6>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-primary" id="imagesProgress" style="width: 0%"></div>
                            </div>
                            <small class="text-muted" id="imagesInfo">0 files, 0 MB</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Documents</h6>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-success" id="documentsProgress" style="width: 0%"></div>
                            </div>
                            <small class="text-muted" id="documentsInfo">0 files, 0 MB</small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Certificates</h6>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-info" id="certificatesProgress" style="width: 0%"></div>
                            </div>
                            <small class="text-muted" id="certificatesInfo">0 files, 0 MB</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Photos</h6>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-warning" id="photosProgress" style="width: 0%"></div>
                            </div>
                            <small class="text-muted" id="photosInfo">0 files, 0 MB</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="loadingSpinner" style="display: none;" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Processing...</p>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load storage statistics
    loadStorageStats();

    // Upload Single File
    $('#uploadSingleFile').click(function() {
        uploadSingleFile();
    });

    // Upload Multiple Files
    $('#uploadMultipleFiles').click(function() {
        uploadMultipleFiles();
    });

    // List Files
    $('#listFiles').click(function() {
        listFiles();
    });

    // Cleanup Files
    $('#cleanupFiles').click(function() {
        cleanupFiles();
    });

    function loadStorageStats() {
        $.ajax({
            url: '/file-management/storage-stats',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#totalStorage').text(response.data.total_storage + ' MB');
                    $('#totalFiles').text(response.data.total_files);
                    $('#totalDirectories').text(response.data.total_directories);
                    $('#recentUploads').text(response.data.recent_uploads);

                    // Update category progress bars
                    updateCategoryProgress('images', response.data.categories.images);
                    updateCategoryProgress('documents', response.data.categories.documents);
                    updateCategoryProgress('certificates', response.data.categories.certificates);
                    updateCategoryProgress('photos', response.data.categories.photos);
                }
            },
            error: function(xhr) {
                console.error('Failed to load storage stats:', xhr);
            }
        });
    }

    function updateCategoryProgress(category, data) {
        const percentage = data.percentage || 0;
        const files = data.files || 0;
        const size = data.size || 0;

        $(`#${category}Progress`).css('width', percentage + '%');
        $(`#${category}Info`).text(`${files} files, ${size} MB`);
    }

    function uploadSingleFile() {
        const file = $('#uploadFile')[0].files[0];
        const category = $('#uploadCategory').val();
        const directory = $('#uploadDirectory').val();

        if (!file) {
            alert('Please select a file to upload.');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('category', category);
        formData.append('directory', directory);

        // Show loading spinner
        $('#loadingSpinner').show();

        $.ajax({
            url: '/file-management/upload',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#loadingSpinner').hide();
                if (response.success) {
                    alert('File uploaded successfully!');
                    $('#uploadFile').val('');
                    loadStorageStats();
                } else {
                    alert('Upload failed: ' + response.message);
                }
            },
            error: function(xhr) {
                $('#loadingSpinner').hide();
                console.error('Upload failed:', xhr);
                alert('Upload failed. Please try again.');
            }
        });
    }

    function uploadMultipleFiles() {
        const files = $('#uploadFile')[0].files;
        const category = $('#uploadCategory').val();
        const directory = $('#uploadDirectory').val();

        if (files.length === 0) {
            alert('Please select files to upload.');
            return;
        }

        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }
        formData.append('category', category);
        formData.append('directory', directory);

        // Show loading spinner
        $('#loadingSpinner').show();

        $.ajax({
            url: '/file-management/upload-multiple',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#loadingSpinner').hide();
                if (response.success) {
                    alert(`Files uploaded successfully! ${response.data.uploaded} files uploaded.`);
                    $('#uploadFile').val('');
                    loadStorageStats();
                } else {
                    alert('Upload failed: ' + response.message);
                }
            },
            error: function(xhr) {
                $('#loadingSpinner').hide();
                console.error('Upload failed:', xhr);
                alert('Upload failed. Please try again.');
            }
        });
    }

    function listFiles() {
        const category = $('#fileCategory').val();
        const directory = $('#fileDirectory').val();

        // Show loading spinner
        $('#loadingSpinner').show();

        $.ajax({
            url: '/file-management/list',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                category: category,
                directory: directory
            },
            success: function(response) {
                $('#loadingSpinner').hide();
                if (response.success) {
                    displayFiles(response.data.files);
                    $('#fileList').show();
                } else {
                    alert('Failed to load files: ' + response.message);
                }
            },
            error: function(xhr) {
                $('#loadingSpinner').hide();
                console.error('Failed to load files:', xhr);
                alert('Failed to load files. Please try again.');
            }
        });
    }

    function displayFiles(files) {
        const tbody = $('#filesTableBody');
        tbody.empty();

        if (files.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center text-muted">No files found</td></tr>');
            return;
        }

        files.forEach(function(file) {
            const row = `
                <tr>
                    <td>${file.name}</td>
                    <td>${file.size}</td>
                    <td>${file.type}</td>
                    <td>${file.upload_date}</td>
                    <td>
                        <button class="btn btn-sm btn-primary me-1" onclick="downloadFile('${file.name}', '${file.directory}')">
                            <i class="bx bx-download"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteFile('${file.name}', '${file.directory}')">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function cleanupFiles() {
        if (!confirm('Are you sure you want to cleanup orphaned files? This action cannot be undone.')) {
            return;
        }

        // Show loading spinner
        $('#loadingSpinner').show();

        $.ajax({
            url: '/file-management/cleanup',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#loadingSpinner').hide();
                if (response.success) {
                    alert(`Cleanup completed! ${response.data.deleted} orphaned files removed.`);
                    loadStorageStats();
                } else {
                    alert('Cleanup failed: ' + response.message);
                }
            },
            error: function(xhr) {
                $('#loadingSpinner').hide();
                console.error('Cleanup failed:', xhr);
                alert('Cleanup failed. Please try again.');
            }
        });
    }
});

// Global functions for file actions
function downloadFile(filename, directory) {
    $.ajax({
        url: '/file-management/download',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            filename: filename,
            directory: directory
        },
        success: function(response) {
            if (response.success) {
                // Create download link
                const link = document.createElement('a');
                link.href = response.data.download_url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                alert('Download failed: ' + response.message);
            }
        },
        error: function(xhr) {
            console.error('Download failed:', xhr);
            alert('Download failed. Please try again.');
        }
    });
}

function deleteFile(filename, directory) {
    if (!confirm('Are you sure you want to delete this file? This action cannot be undone.')) {
        return;
    }

    $.ajax({
        url: '/file-management/delete',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            filename: filename,
            directory: directory
        },
        success: function(response) {
            if (response.success) {
                alert('File deleted successfully!');
                // Refresh file list
                $('#listFiles').click();
                // Refresh storage stats
                loadStorageStats();
            } else {
                alert('Delete failed: ' + response.message);
            }
        },
        error: function(xhr) {
            console.error('Delete failed:', xhr);
            alert('Delete failed. Please try again.');
        }
    });
}
</script>
@endsection 