<?php

test('no Vue files call the Ziggy route() helper', function () {
    $vueFiles = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(resource_path('js'))
    );

    $violations = [];

    foreach ($vueFiles as $file) {
        if ($file->getExtension() !== 'vue') {
            continue;
        }

        $contents = file_get_contents($file->getPathname());
        $relativePath = str_replace(resource_path('js') . DIRECTORY_SEPARATOR, '', $file->getPathname());

        // Match route( but not things like "router." or "routes/" or comments
        // Look for the pattern: route( preceded by non-word char (or start), not preceded by "r" as in "router"
        if (preg_match('/(?<![a-zA-Z])route\s*\(/', $contents)) {
            // Exclude lines that are purely comments
            $lines = explode("\n", $contents);
            foreach ($lines as $lineNumber => $line) {
                $trimmed = ltrim($line);
                if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '*') || str_starts_with($trimmed, '<!--')) {
                    continue;
                }
                if (preg_match('/(?<![a-zA-Z])route\s*\(/', $line)) {
                    $violations[] = $relativePath . ':' . ($lineNumber + 1);
                }
            }
        }
    }

    expect($violations)->toBeEmpty(
        "These Vue files use the Ziggy route() helper which is not available in this project.\n" .
        "Use Wayfinder-generated helpers from @/routes/** instead.\n" .
        "Run `php artisan wayfinder:generate` if route files are missing.\n\n" .
        "Violations:\n  " . implode("\n  ", $violations)
    );
});
