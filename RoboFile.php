<?php

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    use \Boedah\Robo\Task\Drush\loadTasks;
    const DRUPAL_ROOT = __DIR__ . '/web';
    const DRUSH_BIN = __DIR__ . '/vendor/bin/drush';
    const BEHAT_BIN = __DIR__ . '/vendor/bin/behat';
    const TERMINUS_BIN = 'terminus';

    public function theme()
    {
        // run watcher on theme assets

    }

    public function buildDev()
    {
        // Compile SASS/JS

    }

    /**
     * Build assets for Production Release.
     *
     * Install front-end dependencies and production PHP dependencies
     */
    public function buildProduction()
    {

    }

    public function bootstrap()
    {

        // Init DB
        // Build Assets
        $this->buildAssets();
    }

    /**
     * Provision the database seed for Docker.
     */
    public function dbSeed()
    {
        $this->_exec('gunzip dump.sql.gz');
        $this->taskFilesystemStack()
            ->mkdir('mariadb-init')
            ->remove('mariadb-init/dump.sql')
            ->rename('dump.sql', 'mariadb-init/dump.sql')
            ->run();
    }

    public function syncContent()
    {
        // Grab db copy from Pantheon and pull into site.
    }

    /**
     * Export Terminus token to the environment if not already and authenticate with Terminus.
     */
    public function terminusLogin()
    {
        if (!getenv('TERMINUS_TOKEN')) {
            putenv('TERMINUS_TOKEN=' . $this->ask(
                    'Please insert your Terminus Machine Token (https://dashboard.pantheon.io/machine-token/create)',
                    true
                )
            );
        }
        $token = getenv('TERMINUS_TOKEN');
        $this->_exec(self::TERMINUS_BIN . " login --machine-token={$token}");
    }

    private function buildDrushTask()
    {
        return $this->taskDrushStack(self::DRUSH_BIN)
            ->drupalRootDirectory(self::DRUPAL_ROOT);
    }

}