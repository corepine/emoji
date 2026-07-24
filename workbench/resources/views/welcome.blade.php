<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Corepine Emoji</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <x-corepine.emoji.styles />
</head>
<body>
    <main style="padding: 48px;">
        <textarea id="body" rows="3" style="width: 420px;"></textarea>
        <x-corepine.emoji target="body" />
        <x-corepine.emoji.reaction />
    </main>
</body>
</html>
