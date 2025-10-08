<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nebula | Badge Verification</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional: Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(120deg, #0d6efd10, #e2e8f0);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .verify-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 800px;
            width: 100%;
        }
        .verify-title {
            font-weight: 600;
            color: #0d6efd;
        }
        .badge-img {
            max-width: 500px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>
<div class="verify-card text-center">
    @if($badge)
        <h2 class="verify-title mb-3">✅ Verified Digital Badge</h2>
        <p class="text-muted mb-4">
            This badge has been officially issued by <strong>Nebula Institute of Technology</strong>.
        </p>

        <div class="mb-4">
            @if($badge->badge_image_path)
                <img src="{{ asset('storage/' . $badge->badge_image_path) }}" 
                     alt="Badge Image" 
                     class="img-fluid badge-img">
            @else
                <div class="alert alert-warning mt-3">No badge image found.</div>
            @endif
        </div>

        <table class="table table-bordered w-75 mx-auto text-start">
            <tbody>
                <tr>
                    <th>Student Name</th>
                    <td>{{ $badge->student->first_name }} {{ $badge->student->last_name }}</td>
                </tr>
                <tr>
                    <th>Course</th>
                    <td>{{ $badge->course->course_name }}</td>
                </tr>
                <tr>
                    <th>Course Type</th>
                    <td>{{ ucfirst($badge->course->course_type) }}</td>
                </tr>
                <tr>
                    <th>Intake</th>
                    <td>{{ $badge->intake->batch ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Issued Date</th>
                    <td>{{ \Carbon\Carbon::parse($badge->issued_date)->format('d M Y') }}</td>
                </tr>
                <tr>
                    <th>Verification Code</th>
                    <td><code>{{ $badge->verification_code }}</code></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if($badge->status === 'active')
                            <span class="badge bg-success">Valid</span>
                        @else
                            <span class="badge bg-danger">Revoked</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="mt-4">
            <a href="{{ asset('storage/' . $badge->badge_image_path) }}" class="btn btn-success me-2" download>
                Download Badge
            </a>
            <a href="https://nebula.lk" class="btn btn-outline-primary">
                Visit Nebula
            </a>
        </div>

    @else
        <h3 class="text-danger">❌ Invalid or Expired Badge</h3>
        <p>This badge link is not valid or has been revoked by Nebula Institute.</p>
    @endif
</div>
</body>
</html>
