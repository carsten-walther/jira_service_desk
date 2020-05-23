<?php

namespace Walther\JiraServiceDesk\Service\Resource;

use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Log\Writer\SyslogWriter;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnexpectedTypeException;
use Walther\JiraServiceDesk\Service\Service;

/**
 * Class AbstractResource
 *
 * @package Walther\JiraServiceDesk\Service\Resource
 * @author  Carsten Walther
 */
abstract class AbstractResource implements ResourceInterface
{
    /**
     * The Service object.
     *
     * @var \Walther\JiraServiceDesk\Service\Service
     */
    protected $service;

    /**
     * The resource name.
     *
     * @var string
     */
    protected $resource = '';

    /**
     * AbstractResource constructor.
     *
     * @param \Walther\JiraServiceDesk\Service\Service $service
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnexpectedTypeException
     */
    public function __construct(Service $service)
    {
        if (!$this->resource) {
            $this->log('Missing resource name.');
            throw new UnexpectedTypeException('Missing resource name.', 100219811242);
        }

        $this->service = $service;
    }

    /**
     * Function for logging to the system log.
     *
     * @param string $message
     */
    final public function log(string $message = '') : void
    {
        $syslogWriter = new SyslogWriter();
        $syslogWriter->writeLog(new LogRecord(
            $component = '',
            $level = LogLevel::ERROR,
            $message,
            $data = [],
            $requestId = ''
        ));
    }

    /**
     * @param \Walther\JiraServiceDesk\Service\Service $service
     */
    public function setService(Service $service) : void
    {
        $this->service = $service;
    }
}
