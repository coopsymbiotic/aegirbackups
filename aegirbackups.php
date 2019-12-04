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
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function aegirbackups_civicrm_xmlMenu(&$files) {
  _aegirbackups_civix_civicrm_xmlMenu($files);
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
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function aegirbackups_civicrm_postInstall() {
  _aegirbackups_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function aegirbackups_civicrm_uninstall() {
  _aegirbackups_civix_civicrm_uninstall();
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
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function aegirbackups_civicrm_disable() {
  _aegirbackups_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function aegirbackups_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _aegirbackups_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function aegirbackups_civicrm_managed(&$entities) {
  _aegirbackups_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function aegirbackups_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _aegirbackups_civix_civicrm_alterSettingsFolders($metaDataFolders);
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
 * Implements hook_civicrm_thems().
 */
function aegirbackups_civicrm_themes(&$themes) {
  _aegirbackups_civix_civicrm_themes($themes);
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
