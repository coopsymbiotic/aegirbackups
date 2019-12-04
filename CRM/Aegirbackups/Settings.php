<?php

class CRM_Aegirbackups_Settings {

  /**
   * Wrapper to access the CMS-specific settings that are set
   * by Aegir in the CMS.
   * All settings are assumed required.
   */
  public static function get($name) {
    // TODO: if d7 d8 wp..
    $value = variable_get($name);

    if (empty($value)) {
      throw new Exception("Missing setting: $name");
    }

    return $value;
  }

}
