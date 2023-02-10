<?php

require_once 'aegirbackups.civix.php';
use CRM_Aegirbackups_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/ 
 */
function aegirbackups_civicrm_config(&$config) {
  _aegirbackups_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function aegirbackups_civicrm_install() {
  _aegirbackups_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function aegirbackups_civicrm_enable() {
  _aegirbackups_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function aegirbackups_civicrm_entityTypes(&$entityTypes) {
  _aegirbackups_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function aegirbackups_civicrm_navigationMenu(&$menu) {
  _aegirbackups_civix_insert_navigation_menu($menu, 'Administer', [
    'label' => E::ts('Backups'),
    'name' => 'aegir_backups',
    'url' => 'civicrm/admin/backups',
    'permission' => 'administer CiviCRM',
    'operator' => 'OR',
    'separator' => 0,
  ]);
  _aegirbackups_civix_navigationMenu($menu);
}
