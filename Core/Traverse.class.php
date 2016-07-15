<?php

/**
 * PESCMS for PHP 5.4+
 *
 * Copyright (c) 2015 PESCMS (http://www.pescms.com)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 * @version 2.5
 */

namespace Core;

use Core\Abnormal\Abnormal as Abnormal,
    Core\Route\Route as Route;

/**
 * 初始化系统控制层
 * @author LuoBoss
 * @version 1.0
 */
class Traverse {

    private $unixPath;

    public function __construct() {

        //自动注册类
        spl_autoload_register(array($this, 'loader'));
        //实体化控制层
        $this->start();
    }

    /**
     * 执行指定模块
     */
    public function start() {
        $this->db()->query('TRUNCATE pes_class');
        $this->db()->query('TRUNCATE pes_class_restful');
        $this->db()->query('TRUNCATE pes_method');
        $this->listDir(PES_TRUE_PATH . 'Traverse');
    }

    function listDir($dir) {
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ((is_dir($dir . "/" . $file)) && $file != "." && $file != "..") {
                        $this->listDir($dir . "/" . $file);
                    } else {
                        //只分析符合格式的类
                        if ($file != "." && $file != ".." && $file != "index.html" && substr($file, -10, 10) == '.class.php') {

                            $analysisResult = $this->analysisClass($dir, $file);
                            if ($analysisResult === false) {
                                continue;
                            } else {
                                $this->recordRestful($analysisResult);
                                $this->recordMethod($analysisResult);
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
    }


    private $reflectionClass, $readFile, $className;

    /**
     * 分析类
     */
    private function analysisClass($dir, $file) {

        $data = [];
        $data['class_path'] = str_replace(PES_TRUE_PATH . 'Traverse/', '', $dir);
        $data['class_name'] = str_replace('.class.php', '', $file);

        $this->className = str_replace("/", "\\", "{$data['class_path']}/{$data['class_name']}");

        $restful = 4;
        if (strpos($this->className, ITEM) !== false) {
            $data['class_type'] = 'Controller';
            foreach (['GET', 'POST', 'PUT', 'DELETE'] as $key => $value) {
                if (strpos($this->className, $value) !== false) {
                    $data['class_path'] = str_replace("/{$value}", '', $data['class_path']);
                    $restful = $key;
                }
            }

        } elseif (strpos($this->className, 'Expand') !== false) {
            $data['class_type'] = 'Expand';
        } elseif (strpos($this->className, 'Model') !== false) {
            $data['class_type'] = 'Model';
        } else {
            $data['class_type'] = 'Other';
        }

        try {
            $this->reflectionClass = new \ReflectionClass($this->className);
        } catch (\Exception $e) {
            echo "Abnormal load file: {$dir}/{$file}\n";
            return false;
        }
        $this->readFile = file("{$dir}/{$file}");

        $check = $this->db('class')->where('class_type = :class_type AND class_path = :class_path AND class_name = :class_name  ')->find($data);
        if (empty($check)) {
            $classid = $this->db('class')->insert($data);
        } else {
            $classid = $check['class_id'];
        }

        $this->displaMsg("Analysis class: {$this->className} success! ");

        return [
            'class_id' => $classid,
            'class_restful' => $restful,
        ];
    }

    /**
     * 记录类的restful
     */
    private function recordRestful($data) {
        $checkRestful = $this->db('class_restful')->where('class_id = :class_id AND class_restful = :class_restful ')->find($data);
        if (empty($checkRestful)) {
            $data['class_restful_comment'] = $this->reflectionClass->getDocComment();
            $data['class_restful_code'] = $this->readFile[$this->reflectionClass->getStartLine() - 1];
            $this->db('class_restful')->insert($data);
        }
    }

    /**
     * 记录方法
     */
    private function recordMethod($data) {
        $methods = $this->reflectionClass->getMethods();

        if (!empty($methods)) {

            foreach ($methods as $key => $method) {
                $methodData = [];

                $methodData = array_merge($methodData, $data);

                if ($method->class != $this->className) {
                    continue;
                }
                $methodData['method_name'] = $method->name;

                $checkMethod = $this->db('method')->where('class_id = :class_id AND class_restful = :class_restful AND method_name = :method_name  ')->find($methodData);

                if (empty($checkMethod)) {
                    $methodData['method_comment'] = $this->reflectionClass->getMethod($method->name)->getDocComment();
                    $i = $this->reflectionClass->getMethod($method->name)->getStartLine() - 1;
                    $methodData['method_code'] = '';
                    while ($i <= $this->reflectionClass->getMethod($method->name)->getEndLine() - 1) {
                        $methodData['method_code'] .= $this->readFile[$i];
                        $i++;
                    }
                    $this->db('method')->insert($methodData);

                    $this->displaMsg("Loading Method: {$methodData['method_name']} success!");

                }
            }
        }
    }

    /**
     * 输出提示信息
     * @param $msg
     */
    private function displaMsg($msg) {
        echo "{$msg}\n";
    }

    /**
     * 初始化数据库
     * @param str $name 表名
     * @return obj 返回数据库对象
     */
    protected static function db($name = '', $database = '', $dbPrefix = '') {
        return \Core\Func\CoreFunc::db($name, $database, $dbPrefix);
    }

    /**
     * 加载必须的类名
     * @param type $className 加载类名
     */
    private function loader($className) {
        $unixPath = str_replace("\\", "/", $className);
        if (file_exists(PES_TRUE_PATH . 'Traverse/' . $unixPath . '.class.php')) {
            require PES_TRUE_PATH . 'Traverse/' . $unixPath . '.class.php';
        } elseif (file_exists(PES_TRUE_PATH . $unixPath . '.class.php') && strpos(PES_TRUE_PATH . $unixPath, PES_TRUE_PATH . 'Core/') !== false) {//只加载核心文件，避免生成了外部的文件文档
            require PES_TRUE_PATH . $unixPath . '.class.php';
        } else {
            if (\Core\Func\CoreFunc::$defaultPath == false) {
                return true;
            }
        }
    }

}
