<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create GitHub App</title>
</head>
<body>
<form action="https://github.com/settings/apps/new" method="POST" id="githubAppForm">
    <input type="hidden" name="state" value="{{ $state }}">
    <input type="hidden" name="manifest" value="{{ $manifest }}">
</form>

<script>
    // Automatically submit the form after the page loads
    document.getElementById('githubAppForm').submit();
</script>
</body>
</html>
