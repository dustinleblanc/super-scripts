<?php

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    const COMPOSE_BIN = 'docker-compose';
    const DRUPAL_ROOT = __DIR__ . '/web';
    const DUMP_FILE = __DIR__ . '/dump.sql.gz';
    const BEHAT_BIN = './vendor/bin/behat';
    const SITENAME = 'super-scripts';
    const TERMINUS_BIN = 'terminus';


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

    /**
     * Pull fresh backup from dev site.
     */
    public function backupGet()
    {
        $this->terminusExec('backup:create', [self::SITENAME . '.dev'], ['element', 'db']);
        $this->terminusExec('backup:get', [self::SITENAME . '.dev'], ['to', self::DUMP_FILE]);
    }

    /**
     * Run Behat tests.
     */
    public function test()
    {
        $this->taskExec(self::COMPOSE_BIN)
            ->args(['run', 'testphp', self::BEHAT_BIN])
            ->option('colors')
            ->option('format', 'progress')
            ->run();
    }

    /**
     * Bring containers up, seed files as needed.
     */
    public function up()
    {
        if (!file_exists('mariadb-init/dump.sql') ||
            !file_exists('web/sites/default/settings.local.php')
        ) {
            $this->setup();
        }

        $this->_exec(self::COMPOSE_BIN . ' up -d');
    }

    /**
     * Seed database, shim in settings.local.php
     */
    public function setup()
    {
        $this->terminusLogin();
        $this->backupGet();
        $this->dbSeed();
    }
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

    /**
     * Build Drush tasks with common arguments.
     * @return $this
     */
    private function buildDrushTask()
    {
        return $this->taskDrushStack(self::DRUSH_BIN)
            ->drupalRootDirectory(self::DRUPAL_ROOT);
    }

    /**
     * Build Terminus Command.
     *
     * @param string $command
     * @param array $args
     * @param array $opts
     * @return \Robo\Result
     */
    private function terminusExec($command = '', array $args = [], array $opt = [])
    {
        return $this->taskExec(self::TERMINUS_BIN)
            ->arg($command)
            ->args($args)
            ->option($opt[0], $opt[1])
            ->run();

    }

}