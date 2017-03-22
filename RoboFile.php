<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
  public function theme() {
    // run watcher on theme assets

  }

  public function buildDev() {
    // Compile SASS/JS

  }

    /**
     * Build assets for Production Release.
     *
     * Install front-end dependencies and production PHP dependencies
     */
  public function buildProduction() {

  }

  public function bootstrap() {

    // Init DB
    // Build Assets
    $this->buildAssets();
  }

  public  function syncContent() {
    // Grab db copy from Pantheon and pull into site.
  }

}