<?php

ini_set('display_errors', true);

define('PROJECT_ROOT_PATH', dirname(dirname(__FILE__)));
define('SRC_PATH', PROJECT_ROOT_PATH . '/src');
define('TEST_CASES_PATH', PROJECT_ROOT_PATH . '/test/cases');
define('TEST_PATH', !empty($_GET['path']) && ('/' != $_GET['path'])
        ? TEST_CASES_PATH . $_GET['path']
        : TEST_CASES_PATH);

function listTests($path, $recursive = false)
{
    if (is_file($path))
    {
        return array($path);
    }

    if (!$recursive)
        return glob("$path/*.test.php");

    $dirIterator = new RecursiveDirectoryIterator($path);
    $iterator = new RecursiveIteratorIterator($dirIterator);

    $files = array();
    foreach ($iterator as $file)
    {
        if (is_file($file->getRealpath()) && preg_match('/\.test\.php$/', $file->getFilename()))
        {
            $files[] = $file->getRealpath();
        }
    }

    return $files;
}

function listDirs($path)
{
    if (is_file($path))
    {
        return array($path);
    }

    return glob("$path/*", GLOB_ONLYDIR);
}

function relativePath($root, $path)
{
    if (false !== strpos($path, $root))
    {
        return substr($path, strlen($root));
    }

    return $path;
}

if (empty($_GET['run']))
{
    $currentDir = relativePath(TEST_CASES_PATH, TEST_PATH);
    $testsInPath = listTests(TEST_PATH);
    
    echo '<ul>';

    echo '<li>[ <a href="?path=', $currentDir, '&run=!&recursive=yep">Run all recursively</a> ]</li>';

    if ($testsInPath)
        echo '<li>[ <a href="?path=', $currentDir, '&run=!">Run all</a> ]</li>';

    if (TEST_CASES_PATH != TEST_PATH)
        echo '<li><a href="?path=', dirname($currentDir), '">..</a></li>';

    foreach (array_merge(listDirs(TEST_PATH), $testsInPath) as $file)
    {
        $relativePath = relativePath(TEST_CASES_PATH, $file);

        echo '<li>';
        if (is_dir($file))
        {
            echo '<a href="?path=', $relativePath, '">', $relativePath, '</a>',
                ' [ <a href="?path=', $relativePath, '&run=!">Run</a> ]',
                ' [ <a href="?path=', $relativePath, '&run=!&recursive=yep">Run recursively</a> ]';
        }
        else
        {
            echo '<a href="?path=', $relativePath, '&run=!">', $relativePath, '</a>';
        }
        echo '</li>';
    }
    echo '</ul>';
}
else
{
    require_once dirname(dirname(__FILE__)) . '/simpletest/autorun.php';
    require_once dirname(dirname(__FILE__)) . '/simpletest/mock_objects.php';
    require_once dirname(dirname(__FILE__)) . '/src/OneGoSDK/init.php';
    require_once dirname(__FILE__) . '/functions.php';
    require_once dirname(__FILE__) . '/expectations.php';
    require_once dirname(__FILE__) . '/BaseUnitTestCase.php';
    require_once dirname(__FILE__) . '/BaseOneGoAPITest.php';
    require_once dirname(__FILE__) . '/BaseSimpleAPITest.php';
    
    class AllTests extends TestSuite
    {
        function AllTests()
        {
            $this->TestSuite('All tests');

            foreach (listTests(TEST_PATH, !empty($_GET['recursive'])) as $testFile)
            {
                $this->addFile($testFile);
            }
        }
    }
}