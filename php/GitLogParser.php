<?php
/**
 * Created by PhpStorm.
 * User: marcel
 * Date: 22.05.17
 * Time: 15:23
 */

namespace php;


class GitLogParser {

    /**
     * GitLogParser constructor.
     */
    public function __construct () {
    }

    /**
     * @param String $repositoryPath
     * @param String $fromBranch
     * @param String $toBranch
     *
     * @return CommitObject[] $commits
     */
    public function getCommits ($repositoryPath, $fromBranch, $toBranch) {
        $commits = [];

        exec('git -C '.$repositoryPath.' log '.$toBranch.' --not '.$fromBranch.' --date=short --no-merges --pretty=format:"%h<<newline>>%x09%an<<newline>>%x09%ad<<newline>>%x09%s<<endline>>" 2> /dev/null', $errorOutput, $gitLogErrorCode);

        if ($gitLogErrorCode) {
            throw new CommandException();
        }

        $gitLogOutput = shell_exec('git -C '.$repositoryPath.' log '.$toBranch.' --not '.$fromBranch.' --date=short --no-merges --pretty=format:"%h<<newline>>%x09%an<<newline>>%x09%ad<<newline>>%x09%s<<endline>>"');

        $gitLogErrorCode = null;
        $errorOutput = null;

        $splittedWholeCommits = explode("<<endline>>", $gitLogOutput);

        foreach ($splittedWholeCommits as $splittedWholeCommit) {
            $commitLines = explode("<<newline>>", $splittedWholeCommit);

            if (isset($commitLines[1])) {
                $commits[] = $this->getParsedCommit($commitLines);
            }
        }

        return $commits;
    }

    private function getParsedCommit ($commitLines) {
        $hash = trim($commitLines[0]);
        $author = trim($commitLines[1]);
        $date = trim($commitLines[2]);
        $message = trim($commitLines[3]);

        return new CommitObject($author, $author, $date, $hash, $message);
    }
}