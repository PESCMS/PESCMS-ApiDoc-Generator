<?php
 $config = array(
'DB_TYPE' => 'mysql',
'DB_HOST' => '数据库地址',
'DB_NAME' => '数据名称',
'DB_USER' => '数据库账号',
'DB_PWD' => '数据库密码',
'DB_PORT' => '3306',
'DB_PREFIX' => 'pes_',
'PRIVATE_KEY' => '213',
'USER_KEY' => '123',
'ERROR_PROMPT' => '/Core/Theme/error.php',
'APP_GROUP_LIST' => 'Team',
'DEFAULT_GROUP' => 'Team',
'FILE_CACHE_PATH' => '/Temp',
'FILE_CACHE_TIME' => '1800',
'LOG_PATH' => '/log',
'LOG_DELETE' => '7',
'UPLOAD_PATH' => '/upload',
'URLMODEL' => array(
'INDEX' => '0',
'SUFFIX' => '1',
),);
$configPath = dirname(__FILE__) . '/Config/';
$configFile = scandir($configPath);
//长度少于等于2结束植入检测.
if (count($configFile) <= '2') {
    return $config;
}

foreach ($configFile as $value) {
    if ($value != '.' && $value != '..' && $value != '.DS_Store') {
        $tmpArray = require $configPath . $value;
        if (is_array($tmpArray)) {
            $config['APP_GROUP_LIST'] = empty($tmpArray['GROUP']) ? $config['APP_GROUP_LIST'] : "{$config['APP_GROUP_LIST']},{$tmpArray['GROUP']}";
            $config = array_merge($config, $tmpArray);
        }
    }
}
return $config;
