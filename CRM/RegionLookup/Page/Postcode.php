<?php

/**
 * Returns the matching entries for a given search lookup.
 * Assumes our lookup is a postcode, but could be another field.
 */
class CRM_RegionLookup_Page_Postcode extends CRM_Core_Page {
  function run() {
    $result = NULL;

    // A mostly useless verification to make sure we are called
    // from /civicrm/regionlookup/postcode/[...], but we need
    // to extract the postcode from, for example:
    // civicrm/regionlookup/postcode/h1h2h2.json
    $config = CRM_Core_Config::singleton();
    $urlVar = $config->userFrameworkURLVar;
    $arg = explode('/', $_GET[$urlVar]);

    $fields = CRM_RegionLookup_BAO_RegionLookup::getFields();

    if ($arg[1] == 'regionlookup' && $arg[2] == 'postcode') {
      $postcode = $arg[3];

      // Clean out the .json suffix
      $postcode = str_replace('.json', '', $postcode);

      // Transform to lowercase, and remove anything non-alphanumeric
      $postcode = strtolower($postcode);
      $postcode = preg_replace('/[^a-z0-9]/', '', $postcode);

      $params = array(
        1 => array($postcode, 'String'),
      );

      $dao = CRM_Core_DAO::executeQuery('SELECT * FROM civicrm_regionlookup WHERE postcode = %1', $params);

      if ($dao->fetch()) {
        foreach ($fields as $key => $fieldname) {
          $result[$key] = $dao->$key;
        }
      }
    }

    echo json_encode($result);
    CRM_Utils_System::civiExit();
  }
}

