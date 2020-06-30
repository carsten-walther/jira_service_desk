<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\ViewHelpers\Be\Widget\Controller;

/**
 * Class PaginateController
 *
 * @package Walther\JiraServiceDesk\ViewHelpers\Be\Widget\Controller
 * @author Carsten Walther
 */
class PaginateController extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $configuration = ['currentPage' => 0, 'itemsPerPage' => 20, 'maxItems' => 10];

    /**
     * The current page.
     *
     * @var int
     */
    protected $currentPage = 0;

    /**
     * How many items should be displayed.
     *
     * @var int
     */
    protected $itemsPerPage = 0;

    /**
     * How many items are in the request.
     *
     * @var int
     */
    protected $maxItems = 0;

    /**
     * Initializes necessary variables for all actions.
     *
     * @return void
     */
    public function initializeAction()
    {
        \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($this->configuration, $this->widgetConfiguration['configuration'], false);

        $this->currentPage = (int)$this->configuration['currentPage'];
        $this->itemsPerPage = (int)$this->configuration['itemsPerPage'];
        $this->maxItems = (int)$this->configuration['maxItems'];
    }

    /**
     * The main index action.
     *
     * @param int $currentPage
     */
    public function indexAction() : void
    {
        $this->view->assign('configuration', $this->configuration);
        $this->view->assign('pagination', $this->buildPagination());
    }

    /**
     * Returns an array with the keys "currentPage"
     *
     * @return array
     */
    protected function buildPagination() : array
    {
        $pagination = [
            'currentPage' => $this->currentPage,
            'hasLessPages' => $this->currentPage > 0,
            'startRecord' => ($this->currentPage * $this->itemsPerPage + 1) <= $this->maxItems ? ($this->currentPage * $this->itemsPerPage + 1) : $this->maxItems,
            'endRecord' => ((($this->currentPage * $this->itemsPerPage) + $this->itemsPerPage) <= $this->maxItems) ? (($this->currentPage * $this->itemsPerPage) + $this->itemsPerPage) : $this->maxItems
        ];

        if ($this->currentPage >= 1) {
            $pagination['previousPage'] = $this->currentPage - 1;
        }

        if ($this->maxItems >= $this->itemsPerPage) {
            $pagination['nextPage'] = $this->currentPage + 1;
        }

        return $pagination;
    }
}
