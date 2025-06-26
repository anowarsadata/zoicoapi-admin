<!DOCTYPE html>
<html>
<head>
    <title>Preview Email Template</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .preview-container { max-width: 800px; margin: auto; background: #fff; border: 1px solid #ccc; padding: 20px; }
    </style>
</head>
<body>
    <div class="preview-container">
        <h2>{{ $template->subject }}</h2>
        <p><strong>From:</strong> {{ $template->from_email }}</p>
        <p><strong>To:</strong> {{ $template->to_email }}</p>
        <p><strong>Reply To:</strong> {{ $template->reply_to_email }}</p>
        <hr>
        <div>{!! $template->message !!}</div>
    </div>
</body>
</html>
