<?php

namespace Walther\JiraServiceDesk\Utility;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Class AccessUtility
 *
 * @package Walther\JiraServiceDesk\Utility
 * @Carsten Walther
 */
class AccessUtility
{
    /**
     * Checks if the user has access.
     *
     * @return bool
     */
    public static function hasAccess() : bool
    {
        $extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk'];

        if (!$extensionConfiguration['serviceDeskId']) {
            return false;
        }

        if (!$extensionConfiguration['serviceDeskUrl']) {
            return false;
        }

        if (self::getBackendUser()->user['serviceDeskPassword'] && self::getBackendUser()->user['serviceDeskUsername']) {
            if ($extensionConfiguration['adminAccessOnly']) {
                if (self::getBackendUser()->isAdmin() && !(bool)self::getBackendUser()->getTSConfig()['backendModule.']['serviceDesk.']['disabled']) {
                    return true;
                }
            } elseif (!(bool)self::getBackendUser()->getTSConfig()['backendModule.']['serviceDesk.']['disabled']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the backend user object.
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    public static function getBackendUser() : BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
