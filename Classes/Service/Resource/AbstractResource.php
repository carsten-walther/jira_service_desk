<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\Service\Resource;

/**
 * Class AbstractResource
 *
 * @package Walther\JiraServiceDesk\Service\Resource
 * @author Carsten Walther
 */
abstract class AbstractResource implements \Walther\JiraServiceDesk\Service\Resource\ResourceInterface
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
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnexpectedTypeException
     */
    public function __construct(\Walther\JiraServiceDesk\Service\Service $service)
    {
        if (!$this->resource) {
            $this->log('Missing resource name.');
            throw new \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnexpectedTypeException('Missing resource name.', 100219811242);
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
        $syslogWriter = new \TYPO3\CMS\Core\Log\Writer\SyslogWriter();
        $syslogWriter->writeLog(new \TYPO3\CMS\Core\Log\LogRecord(
            $component = '',
            $level = \TYPO3\CMS\Core\Log\LogLevel::ERROR,
            $message,
            $data = [],
            $requestId = ''
        ));
    }
}
