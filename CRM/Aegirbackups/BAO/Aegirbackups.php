<?php

use CRM_Aegirbackups_ExtensionUtil as E;

class CRM_Aegirbackups_BAO_Aegirbackups {

  /**
   *
   */
  public static function getBackupList($new = false) {
    $aegir_server = Civi::settings()->get('hosting_restapi_hostmaster');
    $token = Civi::settings()->get('hosting_restapi_token');

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

        // Turn off buffering to avoid memory issues, otherwise it's difficult to download
        // a large file (large than the PHP memory limit).
        // In Drupal7, the ob_get_level is initially at 3
        // The 'test' variable is only to avoid infinite loops if something unexpected happens.
        $test = 5;
        while (ob_get_level() && $test > 0) {
          ob_end_clean();
          $test--;
        }

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

  /**
   * Generate a live SQL dump and immediately download it.
   * Based on Aegir's provision/db/Provision/Service/db/mysql.php
   *
   * @todo It would be nicer to queue it and run it throw a Scheduled Job
   * This will most likely fail for large databases.
   */
  public static function downloadSQLdump() {
    $config = CRM_Core_Config::singleton();
    $credentials = DB::parseDSN($config->dsn);
    $cmd = sprintf("mysqldump --defaults-file=/dev/fd/3 --no-tablespaces --no-autocommit --skip-add-locks --single-transaction --quick --hex-blob %s", escapeshellcmd($credentials['database']));
    $dump_filename = $config->uploadDir . '/database-' . date('YmdHis') . '-' . md5(uniqid(rand(), TRUE)) . '.sql';
    $dump_fd = fopen($dump_filename, 'x');

    // Fail if db file already exists.
    if ($dump_fd === FALSE) {
      throw new Exception(E::ts('Could not write database backup file mysqldump: %1 (open failed)', [1 => $dump_filename]));
    }

    $pipes = [];
    $descriptorspec = self::generate_descriptorspec();
    $process = proc_open($cmd, $descriptorspec, $pipes);

    if (is_resource($process)) {
      fwrite($pipes[3], self::generate_mycnf($credentials));
      fclose($pipes[3]);
  
      // At this point we have opened a pipe to that mysqldump command. Now
      // we want to read it one line at a time and do our replacements.
      while (($buffer = fgets($pipes[1], 4096)) !== FALSE) {
        self::filter_line($buffer);
        // Write the resulting line in the backup file.
        if ($buffer && fwrite($dump_fd, $buffer) === FALSE) {
          throw new Exception(E::ts('Could not write database backup file mysqldump: %1 (write failed)', [1 => $dump_filename]));
        }
      }
      // Close stdout.
      fclose($pipes[1]);
      // Catch errors returned by mysqldump.
      $err = fread($pipes[2], 4096);
      // Close stderr as well.
      fclose($pipes[2]);
      if (proc_close($process) != 0) {
        throw new Exception(E::ts('Could not write database backup file mysqldump (command: %1) (error: %2)', [1 => $err, 2 => $cmd]));
      }
    }
    else {
      throw new Exception(E::ts('Could not run mysqldump for backups'));
    }

    $filesize = filesize($dump_filename);
    if ($filesize < 1024) {
      throw new Exception(E::ts('Could not generate database backup from mysqldump. (filesize: %1, error: %2)', [1 => $filesize, 2 => $err]));
    }

    $contact_id = CRM_Core_Session::singleton()->get('userID');
    Civi::log()->info("Contact {$contact_id} has downloaded a full CiviCRM SQL backup.");

    // Turn off buffering to avoid memory issues, otherwise it's difficult to download
    // a large file (large than the PHP memory limit).
    // In Drupal7, the ob_get_level is initially at 3
    // The 'test' variable is only to avoid infinite loops if something unexpected happens.
    $test = 5;
    while (ob_get_level() && $test > 0) {
      ob_end_clean();
      $test--;
    }

    header('Content-Description: CiviCRM backup');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($dump_filename) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . $filesize);
    readfile($dump_filename);
    unlink($dump_filename);
    exit;
  }

  /**
   * Generate the descriptors necessary to open a process with readable and
   * writeable pipes. 
   */
  public static function generate_descriptorspec($stdin_file = NULL) {
    $stdin_spec = is_null($stdin_file) ? ["pipe", "r"] : ["file", $stdin_file, "r"];
    $descriptorspec = [
      0 => $stdin_spec,         // stdin is a pipe that the child will read from
      1 => ["pipe", "w"],  // stdout is a pipe that the child will write to
      2 => ["pipe", "w"],  // stderr is a file to write to
      3 => ["pipe", "r"],  // fd3 is our special file descriptor where we pass credentials
    ];
    return $descriptorspec;
  } 

  /** 
   * Generate the contents of a mysql config file containing database
   * credentials.
   */ 
  public static function generate_mycnf(Array $credentials) : string {
    $hostparts = explode(':', $credentials['hostspec']);
    $port = $hostparts[1] ?? 3306;

    $mycnf = sprintf('[client]
host=%s 
user=%s
password="%s"
port=%s
', $hostparts[0], $credentials['username'], $credentials['password'], $port);

    $mycnf .= "default-character-set=utf8mb4" . PHP_EOL;

    return $mycnf;
  }

  /**
   * Return an array of regexes to filter lines of mysqldumps.
   */
  public static function get_regexes() {
    static $regexes = NULL;
    if (is_null($regexes)) {
      $regexes = [
        // remove DEFINER entries
        '#/\*!50013 DEFINER=.*/#' => FALSE,
        // remove another kind of DEFINER line
        '#/\*!50017 DEFINER=`[^`]*`@`[^`]*`\s*\*/#' => '',
        // remove broken CREATE ALGORITHM entries
        '#/\*!50001 CREATE ALGORITHM=UNDEFINED \*/#' => "/*!50001 CREATE */",
      ];
    }
    return $regexes;
  }     
        
  public static function filter_line(&$line) {
    $regexes = self::get_regexes();
    foreach ($regexes as $find => $replace) {
      if ($replace === FALSE) {
        if (preg_match($find, $line)) {
          // Remove this line entirely.
          $line = FALSE;
        }
      }
      else { 
        $line = preg_replace($find, $replace, $line);
        if (is_null($line)) {
          throw new Exception(E::ts("Error while running regular expression:\n Pattern: %1\n Replacement: %2", [1 => $find, 2 => $replace]));
        }
      }
    }
  }     

}
