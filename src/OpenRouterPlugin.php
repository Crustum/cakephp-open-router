<?php
declare(strict_types=1);

namespace Crustum\OpenRouter;

use Cake\Core\BasePlugin;
use Crustum\PluginManifest\Manifest\ManifestInterface;
use Crustum\PluginManifest\Manifest\ManifestTrait;

/**
 * OpenRouterPlugin provides OpenRouter API integration for CakePHP applications.
 *
 * @uses \Crustum\PluginManifest\Manifest\ManifestTrait
 */
class OpenRouterPlugin extends BasePlugin implements ManifestInterface
{
    use ManifestTrait;

    /**
     * Get the manifest for the plugin.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function manifest(): array
    {
        $pluginPath = dirname(__DIR__);

        return array_merge(
            static::manifestConfig(
                $pluginPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'openrouter.php',
                CONFIG . 'openrouter.php',
                false,
            ),
            static::manifestBootstrapAppend(
                "if (file_exists(CONFIG . 'openrouter.php')) {\n    Configure::load('openrouter', 'default');\n}",
                '// OpenRouter Plugin Configuration',
            ),
        );
    }
}
