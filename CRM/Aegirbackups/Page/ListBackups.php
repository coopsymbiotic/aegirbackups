<?php

use CRM_Aegirbackups_ExtensionUtil as E;

class CRM_Aegirbackups_Page_ListBackups extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle(E::ts('Backups'));

    $action = CRM_Utils_Request::retrieveValue('action', 'String');
    $new = ($action == 'new' ? true : false);

    if ($action == 'download') {
      $id = CRM_Utils_Request::retrieveValue('id', 'Positive');
      $backups = CRM_Aegirbackups_BAO_Aegirbackups::download($id);

      # $redirectUrl = CRM_Utils_System::url('civicrm/admin/backups', 'reset=1');
      # CRM_Utils_System::redirect($redirectUrl);
    }

    $backups = CRM_Aegirbackups_BAO_Aegirbackups::getBackupList($new);
    $this->assign('backups', $backups);

    if ($new) {
      // FIXME: E::ts not working?
      CRM_Core_Session::setStatus(E::ts('A new backup has been scheduled. Please wait a few minutes and refresh this page.'), '', 'info');
      $redirectUrl = CRM_Utils_System::url('civicrm/admin/backups', 'reset=1');
      CRM_Utils_System::redirect($redirectUrl);
    }

    parent::run();
  }

}
