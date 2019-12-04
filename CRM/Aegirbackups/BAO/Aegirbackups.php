<?php

use CRM_Aegirbackups_ExtensionUtil as E;

class CRM_Aegirbackups_BAO_Aegirbackups {

  /**
   *
   */
  public static function getBackupList($new = false) {
    $aegir_server = CRM_Aegirbackups_Settings::get('hosting_restapi_hostmaster');
    $token = CRM_Aegirbackups_Settings::get('hosting_restapi_token');

    $client = new GuzzleHttp\Client();

    $response = $client->request('GET', $aegir_server . '/hosting/api/site/backup', [
      'headers' => [
        'User-Agent' => 'CiviCRM',
      ],
      'query' => [
        'token' => $token,
        'url' => $_SERVER['SERVER_NAME'],
        'new' => (int) $new,
      ],
    ]);

    $output = '';

    if ($response->getStatusCode() != 200) {
      throw new Exception("Get Backups Error Code: " . $response->getStatusCode());
    }

    $data = json_decode($response->getBody(), TRUE);

    if (!empty($data['data'])) {
      return $data['data'];
    }

    return [];
  }

  /**
   *
   */
  public static function download($id) {
    // FIXME We should cache or otherwise avoid doing this call again
    // just to get the filename.
    $backups = self::getBackupList();

    if (!empty($backups[$id])) {
      $file = $backups[$id]['filename'];

      if (file_exists($file)) {
        $contact_id = CRM_Core_Session::singleton()->get('userID');
        Civi::log()->info("Contact {$contact_id} has downloaded a full CiviCRM backup.");

        header('Content-Description: CiviCRM backup');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
      }
      else {
        throw new Exception("The requested file could not be read.");
      }
    }
    else {
      throw new Exception("The requested backup does not exist.");
    }
  }

}
