<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create GitHub App</title>
</head>
<body>
<form action="{{ $url }}" method="POST" id="githubAppForm">
    <input type="hidden" name="state" value="{{ $state }}">
    <input type="hidden" name="manifest" value="{{ json_encode($manifest) }}">
</form>
<script>
    document.getElementById('githubAppForm').submit();
</script>
</body>
</html>
