<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\Domain\Model;

/**
 * Class Attachment
 *
 * @package Walther\JiraServiceDesk\Domain\Model
 * @author Carsten Walther
 */
class Attachment
{
    /**
     * temporary attachment ids
     *
     * @var array
     */
    public $temporaryAttachmentIds;

    /**
     * TRUE if attachment is public
     *
     * @var boolean
     */
    public $public = true;

    /**
     * additional comment
     *
     * @var array
     */
    public $additionalComment = [
        'body' => ''
    ];

    /**
     * Returns the temporary attachment ids
     *
     * @return array
     */
    public function getTemporaryAttachmentIds() : array
    {
        return $this->temporaryAttachmentIds;
    }

    /**
     * Setter for the temporary attachment ids
     *
     * @param array $temporaryAttachmentIds
     *
     * @return Attachment
     */
    public function setTemporaryAttachmentIds(array $temporaryAttachmentIds) : Attachment
    {
        $this->temporaryAttachmentIds = $temporaryAttachmentIds;
        return $this;
    }

    /**
     * Setter for one temporary attachment id
     *
     * @param string $temporaryAttachmentId
     *
     * @return Attachment
     */
    public function addTemporaryAttachmentId(string $temporaryAttachmentId) : Attachment
    {
        $this->temporaryAttachmentIds[] = $temporaryAttachmentId;
        return $this;
    }

    /**
     * Returns TRUE if is public, else FALSE
     *
     * @return bool
     */
    public function getPublic() : bool
    {
        return $this->public;
    }

    /**
     * Setter for public state
     *
     * @param bool $public
     *
     * @return Attachment
     */
    public function setPublic(bool $public) : Attachment
    {
        $this->public = $public;
        return $this;
    }

    /**
     * Returns the additional comment
     *
     * @return string
     */
    public function getAdditionalComment() : string
    {
        return $this->additionalComment['body'];
    }

    /**
     * Setter for the additional comment
     *
     * @param string $comment
     *
     * @return Attachment
     */
    public function setAdditionalComment(string $comment) : Attachment
    {
        $this->additionalComment['body'] = $comment;
        return $this;
    }
}
