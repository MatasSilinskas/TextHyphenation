<?php
namespace Helper;

class Api extends \Codeception\Module
{
    private $projectConfig;

    public function _beforeSuite($settings = [])
    {
        $dbSettings = $settings['modules']['enabled'][1]['Db'];
        $config = include 'config.php';
        $this->projectConfig = $config;

        $config['databaseConfig']['username'] = $dbSettings['user'];
        $config['databaseConfig']['password'] = $dbSettings['password'];
        $config['databaseConfig']['dsn'] = $dbSettings['dsn'];
        $config['databaseConfig']['dbname'] = null;

        $this->rewriteConfig($config);
        parent::_beforeSuite($settings);
    }

    public function _afterSuite()
    {
        $this->rewriteConfig($this->projectConfig);
        parent::_afterSuite();
    }

    private function rewriteConfig(array $config): void
    {
        $contents = var_export($config, true);
        file_put_contents('config.php', "<?php\n return {$contents};\n ?>");
    }
}
