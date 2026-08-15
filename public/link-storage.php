<?php
$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

if (file_exists($link)) {
    echo "Storage link already exists.";
} else {
    // Attempt symlink creation
    if (symlink($target, $link)) {
        echo "Storage link created successfully!";
    } else {
        // Fallback to Artisan link execution
        try {
            require __DIR__.'/../bootstrap/app.php';
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
            $status = \Illuminate\Support\Facades\Artisan::call('storage:link');
            echo "Storage link created via Artisan!";
        } catch (\Exception $e) {
            echo "Failed to create link: " . $e->getMessage() . "<br>Please run 'php artisan storage:link' manually in your command terminal.";
        }
    }
}
