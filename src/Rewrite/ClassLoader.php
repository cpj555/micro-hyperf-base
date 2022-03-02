<?php
declare(strict_types=1);

namespace Losingbattle\MicroBase\Rewrite;

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use Dotenv\Dotenv;
use Dotenv\Repository\Adapter;
use Dotenv\Repository\RepositoryBuilder;
use Hyperf\Di\Annotation\ScanConfig;
use Hyperf\Di\Annotation\Scanner;
use Symfony\Component\Finder\Finder;

class ClassLoader extends \Hyperf\Di\ClassLoader
{
    /**
     * @var \Composer\Autoload\ClassLoader
     */
    protected $composerClassLoader;

    /**
     * The container to collect all the classes that would be proxy.
     * [ OriginalClassName => ProxyFileAbsolutePath ].
     *
     * @var array
     */
    protected $proxies = [];

    public function __construct(ComposerClassLoader $classLoader, string $proxyFileDir, string $configDir)
    {
        $this->setComposerClassLoader($classLoader);
        if (file_exists(BASE_PATH . '/.env')) {
            $this->loadDotenv();
        }
        $this->loadProperties();
        // Scan by ScanConfig to generate the reflection class map
        $config = ScanConfig::instance($configDir);
        $classLoader->addClassMap($config->getClassMap());

        $scanner = new Scanner($this, $config);

        $this->proxies = $scanner->scan($this->getComposerClassLoader()->getClassMap(), $proxyFileDir);
    }

    protected function loadProperties(): void
    {
        $repository = RepositoryBuilder::createWithNoAdapters()
            ->addAdapter(Adapter\PutenvAdapter::class)
            ->immutable()
            ->make();

        $path = env('PROPERTIES_PATH', '/app/config/');

        if (is_dir($path)) {
            $finder = new Finder();
            $finder->files()->in($path)->name('*.properties')->files();
            if ($finder->count()) {
                $properties = [];
                foreach ($finder as $fileInfo) {
                    $properties[] = $fileInfo->getFilename();
                }
                Dotenv::create($repository, [$path], $properties, false)->load();
            }
        }
    }
}
