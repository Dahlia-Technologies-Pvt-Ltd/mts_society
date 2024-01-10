<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width,initial-scale=1"/>
		<!-- Favicon link -->
		<link rel="icon" type="image/x-icon" href="{{ url('/storage/uploads/logos/favicon.ico') }}">
	</head>
	<body>
		<div id="root"></div>
		@viteReactRefresh
		@vite('resources/src/index.tsx')
	</body>
</html>