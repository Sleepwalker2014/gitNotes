<?php
namespace php;

/**
 * Created by PhpStorm.
 * User: marcel
 * Date: 22.05.17
 * Time: 14:37
 */
class CommitObject {
    private $authorName;
    private $authorEmail;
    private $date;
    private $hash;
    private $message;
    private $messageBody;
    /**
     * @var mixed[]|null
     */
    private $redmineTicket;

    /**
     * CommitObject constructor.
     *
     * @param string       $authorName
     * @param string       $authorEmail
     * @param string       $date
     * @param string       $hash
     * @param string       $message
     * @param string       $messageBody
     * @param mixed[]|null $redmineTicket
     */
    public function __construct ($authorName,
                                 $authorEmail,
                                 $date,
                                 $hash,
                                 $message,
                                 $messageBody,
                                 $redmineTicket = null) {
        $this->authorName = $authorName;
        $this->authorEmail = $authorEmail;
        $this->date = $date;
        $this->hash = $hash;
        $this->message = $message;
        $this->messageBody = $messageBody;
        $this->redmineTicket = $redmineTicket;
    }

    /**
     * @return string
     */
    public function getAuthorName () {
        return $this->authorName;
    }

    /**
     * @return string
     */
    public function getAuthorEmail () {
        return $this->authorEmail;
    }

    /**
     * @return string
     */
    public function getDate () {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getHash () {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getMessage () {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getMessageBody ()
    {
        return $this->messageBody;
    }

    /**
     * @return mixed[]|null
     */
    public function getRedmineTicket ()
    {
        return $this->redmineTicket;
    }
}