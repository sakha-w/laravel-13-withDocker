<!DOCTYPE html>
<html>
<head>
    <title>Show Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Show Post</h1>
        <div class="card">
            <div class="card-header">
                {{ $post->title }}
            </div>
            <div class="card-body">
                <p class="card-text">{{ $post->content }}</p>
            </div>
        </div>
        <a href="{{ route('posts.index') }}" class="btn btn-primary mt-3">Back</a>
    </div>
</body>
</html>