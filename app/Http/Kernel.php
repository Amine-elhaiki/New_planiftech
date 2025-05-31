// Dans app/Http/Kernel.php (Laravel 10)
protected $middlewareAliases = [
    // ... autres middlewares
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];
