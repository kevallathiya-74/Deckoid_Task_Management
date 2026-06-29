<?php

/**
 * Authentication and Authorization Helpers
 */

if (!function_exists('isAdmin')) {
    /**
     * Check if the currently logged-in user is an Admin
     *
     * @return bool
     */
    function isAdmin()
    {
        return isset($_SESSION['user_role']) && strtolower($_SESSION['user_role']) === 'admin';
    }
}

if (!function_exists('isSubAdmin')) {
    /**
     * Check if the currently logged-in user is a Sub Admin
     *
     * @return bool
     */
    function isSubAdmin()
    {
        return isset($_SESSION['user_role']) && strtolower($_SESSION['user_role']) === 'sub_admin';
    }
}

if (!function_exists('isAdminOrSubAdmin')) {
    /**
     * Check if the currently logged-in user is an Admin or Sub Admin
     *
     * @return bool
     */
    function isAdminOrSubAdmin()
    {
        return isAdmin() || isSubAdmin();
    }
}

if (!function_exists('hasRole')) {
    /**
     * Check if the currently logged-in user has any of the specified roles
     *
     * @param array $roles Array of role slugs
     * @return bool
     */
    function hasRole(array $roles)
    {
        if (!isset($_SESSION['user_role'])) {
            return false;
        }
        $currentRole = strtolower($_SESSION['user_role']);
        return in_array($currentRole, array_map('strtolower', $roles));
    }
}

if (!function_exists('canManageUser')) {
    /**
     * Determine if the currently logged-in user can manage a target user based on the target's role slug.
     * Admins can manage anyone.
     * Sub Admins can manage Staff and other Sub Admins, but NOT Admins.
     * Staff cannot manage anyone (typically handled by broader route middleware).
     *
     * @param string $targetRoleSlug The role slug of the user to be managed
     * @return bool
     */
    function canManageUser($targetRoleSlug)
    {
        $targetRoleSlug = strtolower($targetRoleSlug);

        if (isAdmin()) {
            return true;
        }

        if (isSubAdmin()) {
            // Sub Admins CANNOT manage Admins
            return $targetRoleSlug !== 'admin';
        }

        return false;
    }
}
