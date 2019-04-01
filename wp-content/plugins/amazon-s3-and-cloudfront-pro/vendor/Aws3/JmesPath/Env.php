<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\JmesPath;

/**
 * Provides a simple environment based search.
 *
 * The runtime utilized by the Env class can be customized via environment
 * variables. If the JP_PHP_COMPILE environment variable is specified, then the
 * CompilerRuntime will be utilized. If set to "on", JMESPath expressions will
 * be cached to the system's temp directory. Set the environment variable to
 * a string to cache expressions to a specific directory.
 */
final class Env
{
    const COMPILE_DIR = 'JP_PHP_COMPILE';
    /**
     * Returns data from the input array that matches a JMESPath expression.
     *
     * @param string $expression JMESPath expression to evaluate
     * @param mixed  $data       JSON-like data to search
     *
     * @return mixed|null Returns the matching data or null
     */
    public static function search($expression, $data)
    {
        static $runtime;
        if (!$runtime) {
            $runtime = \DeliciousBrains\WP_Offload_Media\Aws3\JmesPath\Env::createRuntime();
        }
        return $runtime($expression, $data);
    }
    /**
     * Creates a JMESPath runtime based on environment variables and extensions
     * available on a system.
     *
     * @return callable
     */
    public static function createRuntime()
    {
        switch ($compileDir = getenv(self::COMPILE_DIR)) {
            case false:
                return new \DeliciousBrains\WP_Offload_Media\Aws3\JmesPath\AstRuntime();
            case 'on':
                return new \DeliciousBrains\WP_Offload_Media\Aws3\JmesPath\CompilerRuntime();
            default:
                return new \DeliciousBrains\WP_Offload_Media\Aws3\JmesPath\CompilerRuntime($compileDir);
        }
    }
    /**
     * Delete all previously compiled JMESPath files from the JP_COMPILE_DIR
     * directory or sys_get_temp_dir().
     *
     * @return int Returns the number of deleted files.
     */
    public static function cleanCompileDir()
    {
        $total = 0;
        $compileDir = getenv(self::COMPILE_DIR) ?: sys_get_temp_dir();
        foreach (glob("{$compileDir}/jmespath_*.php") as $file) {
            $total++;
            unlink($file);
        }
        return $total;
    }
}
