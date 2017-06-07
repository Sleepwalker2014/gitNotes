<?php
namespace php;

use Twig_Environment;
use Twig_Loader_Filesystem;

require_once 'vendor/autoload.php';

const RED = "\033[0;31m";
const GREEN = "\033[0;32m";
const PURPLE = "\033[0;35m";
const ORANGE = "\033[0;33m";
const CYAN = "\033[0;36m";
const WHITE = "\033[0;37m";

error_reporting(E_ERROR | E_PARSE);

$gitLogParser = new GitLogParser();

$outFileParameter = getParameter('--out', $argv);
$inFileParameter = getParameter('--in', $argv);
$filterParameter = getParameter('--filter', $argv);
$repositoryParameter = getParameter('--repository', $argv);
$fromParameter = getParameter('--from', $argv);
$toParameter = getParameter('--to', $argv);

$twigFileSystemBasePath = dirname(dirname(__FILE__));

$outFile = '/tmp/releaseNotes.html';
$templatePath ='/html/defaultReleaseNote.html.twig';

if (!$repositoryParameter) {
    $repositoryParameter = getcwd();
}

if (!$fromParameter) {
    echo 'Bitte geben Sie mit dem Parameter --from den Branch/Tag/Commit an, von dem aus die release notes erstellt werden sollen.'.PHP_EOL;
    printWelcomeMessage();
    exit(1);
}

if (!$toParameter) {
    echo 'Bitte geben Sie mit dem Parameter --to den Branch/Tag/Commit an, für den Sie die release notes erstellen möchten.'.PHP_EOL;
    printWelcomeMessage();
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
    if (!$commits = $gitLogParser->getCommits($repositoryParameter, $fromParameter, $toParameter, $filterParameter)) {
        echo 'Es gibt keine unterschiedlichen Commits zwischen '.$fromParameter.' und '.$toParameter.PHP_EOL;
        exit(0);
    }
} catch (CommandException $e) {
    echo $e->getMessage().PHP_EOL;
}

if (!generateReleaseNote($commits, $outFile, $templatePath, $twigFileSystemBasePath)) {
    echo 'Der Pfad '.$outFileParameter.' existiert nicht oder Sie haben keine Rechte Dateien darin anzulegen.'.PHP_EOL;
}

function printWelcomeMessage () {
    setTextColor(PURPLE);

    echo ' _____ _ _     _   _       _'.PHP_EOL;
    echo '|  __ (_) |   | \ | |     | |'.PHP_EOL;
    echo '| |  \/_| |_  |  \| | ___ | |_ ___  ___'.PHP_EOL;
    echo '| | __| | __| | . ` |/ _ \| __/ _ \/ __|'.PHP_EOL;
    echo '| |_\ \ | |_  | |\  | (_) | ||  __/\__ \\'.PHP_EOL;
    echo ' \____/_|\__| \_| \_/\___/ \__\___||___/'.PHP_EOL.PHP_EOL;

    echo 'Willkommen bei Git Notes 1.0'.PHP_EOL.PHP_EOL;

    setTextColor(ORANGE);

    echo 'Verwendung:';

    setTextColor(WHITE);
    echo ' gitnotes --from <path> --to <path>  [--repository <path>] [--in <path>] [--out <path>]'.PHP_EOL.PHP_EOL;

    setTextColor(ORANGE);
    echo 'Parameter:'.PHP_EOL;
    setTextColor(WHITE);

    setTextColor(CYAN);
    echo '  --from        ';
    setTextColor(WHITE);
    echo 'Branch/Commit/Tag, von dem die Unterschiede festgestellt werden sollen'.PHP_EOL;

    setTextColor(CYAN);
    echo '  --to          ';
    setTextColor(WHITE);
    echo 'Branch/Commit/Tag, zu dem die release notes erstellt werden sollen'.PHP_EOL;

    setTextColor(CYAN);
    echo '  --filter      ';
    setTextColor(WHITE);
    echo 'Ein regulärer Ausdruck, welcher alle Commits herausfiltert, die nicht diesem Ausdruck entsprechen (optional)'.PHP_EOL;

    setTextColor(CYAN);
    echo '  --repository  ';
    setTextColor(WHITE);
    echo 'Pfad zu dem Git-Repository, aus welchem die release notes erstellt werden sollen (optional) (Standard: aktuelles Arbeitsverzeichnis)'.PHP_EOL;

    setTextColor(CYAN);
    echo '  --in  ';
    setTextColor(WHITE);
    echo '        Pfad zu einem .twig Template, welches als Vorlage für die release notes gilt (optional)'.PHP_EOL;

    setTextColor(CYAN);
    echo '  --out  ';
    setTextColor(WHITE);
    echo '       Dateiname (Pfad) für die generierten release notes (optional)'.PHP_EOL;
}

/**
 * @param CommitObject[] $commits
 * @param string         $outputPath
 * @param string         $templatePath
 * @param string         $twigFileSystemBasePath
 *
 * @return boolean
 */
function generateReleaseNote ($commits, $outputPath, $templatePath, $twigFileSystemBasePath = '/') {
    $loader = new Twig_Loader_Filesystem($twigFileSystemBasePath);
    $twig = new Twig_Environment($loader);
   // echo print_r(retreiveRedmineTicket(),true);
    return writeReleaseNotesToFile($twig->render($templatePath, ['commits' => $commits, 'redmine' => retreiveRedmineTicket()]), $outputPath);
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

/**
 * @param String $textColor
 */
function setTextColor ($textColor) {
    echo $textColor;
}

function retreiveRedmineTicket () {
    $redmine = new RedmineParser('http://192.168.18.60/redmine', "df3cfc883d54e50e2e67a0461de5219a2fc61305");

    return $redmine->retreiveTicketAsArray();
}