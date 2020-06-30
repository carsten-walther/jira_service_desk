<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\ViewHelpers\Be\Widget;

/**
 * Class PaginateViewHelper
 *
 * @package Walther\JiraServiceDesk\ViewHelpers\Be\Widget
 * @author Carsten Walther
 */
class PaginateViewHelper extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper
{
    /**
     * The PaginateController object.
     *
     * @var \Walther\JiraServiceDesk\ViewHelpers\Be\Widget\Controller\PaginateController
     */
    protected $controller;

    /**
     * Injection for PaginateController object.
     *
     * @param \Walther\JiraServiceDesk\ViewHelpers\Be\Widget\Controller\PaginateController $controller
     */
    public function injectPaginateController(\Walther\JiraServiceDesk\ViewHelpers\Be\Widget\Controller\PaginateController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * initialize arguments function.
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('configuration', 'array', 'configuration', false, ['currentPage' => 0, 'itemsPerPage' => 20, 'maxItems' => 0]);
    }

    /**
     * Render the pagination widget.
     *
     * @return string|mixed
     */
    public function render()
    {
        return $this->initiateSubRequest();
    }
}
