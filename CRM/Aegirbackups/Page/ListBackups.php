<?php

use CRM_Aegirbackups_ExtensionUtil as E;

class CRM_Aegirbackups_Page_ListBackups extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle(E::ts('Backups'));

    // Weird: if we use 'action', the page behaves very oddly.
    $op = CRM_Utils_Request::retrieveValue('op', 'String');

    if ($op === 'download') {
      $id = CRM_Utils_Request::retrieveValue('id', 'Positive');
      // exits or throws an exception
      CRM_Aegirbackups_BAO_Aegirbackups::download($id);
    }
    elseif ($op === 'sql') {
      // exits or throws an exception
      CRM_Aegirbackups_BAO_Aegirbackups::downloadSQLdump();
    }

    $new = ($op === 'new' ? true : false);
    $backups = CRM_Aegirbackups_BAO_Aegirbackups::getBackupList($new);
    $this->assign('backups', $backups);

    if ($new) {
      CRM_Core_Session::setStatus(E::ts('A new backup has been scheduled. Please wait a few minutes and refresh this page.'), '', 'info');
      $redirectUrl = CRM_Utils_System::url('civicrm/admin/backups', 'reset=1');
      CRM_Utils_System::redirect($redirectUrl);
    }

    parent::run();
  }

}
