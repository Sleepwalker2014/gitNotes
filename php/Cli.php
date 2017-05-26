<?php
namespace php;

use Exception;
use Twig_Environment;
use Twig_Error;
use Twig_Loader_Filesystem;

require_once 'vendor/autoload.php';

error_reporting(E_ERROR | E_PARSE);

printWelcomeMessage();
$gitLogParser = new GitLogParser();

$outFileParameter = getParameter('--out', $argv);
$inFileParameter = getParameter('--in', $argv);
$repositoryParameter = getParameter('--repository', $argv);
$fromParameter = getParameter('--from', $argv);
$toParameter = getParameter('--to', $argv);

$twigFileSystemBasePath = '/';

$outFile = '/tmp/releaseNotes.html';
$templatePath = 'html/defaultReleaseNote.html.twig';

if (!$repositoryParameter) {
    echo 'Bitte geben Sie mit dem Parameter --repository den Pfad zu ihrem Git-Repository an.'.PHP_EOL;
    exit(1);
}

if (!$fromParameter) {
    echo 'Bitte geben Sie mit dem Parameter --from den Branch/Tag/Commit an, von dem aus die Release Notes erstellt werden sollen.'.PHP_EOL;
    exit(1);
}

if (!$toParameter) {
    echo 'Bitte geben Sie mit dem Parameter --to den Branch/Tag/Commit an, für den Sie die Release Notes erstellen möchten.'.PHP_EOL;
    exit(1);
}

if ($outFileParameter) {
    $outFile = $outFileParameter;
}

if ($inFileParameter) {
    $twigFileSystemBasePath = getDirectoryPathFromFile($inFileParameter);
    $templatePath = getFileNameFromPath($inFileParameter);
}

try {
    $commits = [];
    if (!$commits = $gitLogParser->getCommits($repositoryParameter, $fromParameter, $toParameter)) {
        echo 'Es gibt keine Unterschiedlichen Commits zwischen '.$fromParameter.' und '.$toParameter.PHP_EOL;
        exit(0);
    }
} catch (CommandException $e) {
    echo 'Die Parameter scheinen keine gültigen Tag/Branch/Commithashes zu sein.'.PHP_EOL;
}

if (!generateReleaseNote($commits, $outFile, $templatePath, $twigFileSystemBasePath)) {
    echo 'Der Pfad oder Dateiname '.$outFileParameter.' ist ungültig.';
}

function printWelcomeMessage () {
    echo 'Willkommen bei Git Notes 1.0'.PHP_EOL;
}

/**
 * @param CommitObject[] $commits
 * @param                $outputPath
 * @param                $templatePath
 * @param string         $twigFileSystemBasePath
 *
 * @return boolean
 */
function generateReleaseNote ($commits, $outputPath, $templatePath, $twigFileSystemBasePath = '/') {
    $loader = new Twig_Loader_Filesystem($twigFileSystemBasePath);
    $twig = new Twig_Environment($loader);

    return writeReleaseNotesToFile($twig->render($templatePath, ['commits' => $commits]), $outputPath);
}

/**
 * @param String $fileContent - The binary release note content (html content, pdf content, etc..)
 * @param String $filePath
 *
 * @return boolean
 */
function writeReleaseNotesToFile ($fileContent, $filePath) {
    return file_put_contents($filePath, $fileContent);
}

/**
 * @param $parameterName
 * @param $argv
 *
 * @return null
 */
function getParameter ($parameterName, $argv) {
    if ($parameterId = array_search($parameterName, $argv)) {
        if (isset($argv[$parameterId + 1])) {
            return $argv[$parameterId + 1];
        }
    }

    return null;
}

/**
 * @param String $filePath
 *
 * @return string
 */
function getDirectoryPathFromFile ($filePath) {
    return dirname($filePath);
}

/**
 * @param String $filePath
 *
 * @return string
 */
function getFileNameFromPath ($filePath) {
    return basename($filePath);
}