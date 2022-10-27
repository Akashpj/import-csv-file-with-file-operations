<?php

namespace Drupal\cambridge_import_resource_mapping\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\search_api\Entity\Index;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a 'Search Controller'.
 */
class SollarReindexController extends ControllerBase {

/**
   * {@inheritdoc}
   */
  public function reindex($fileId, $familyId) {
    $solr_index = \Drupal::config('cambridge_external_app.api_data_config_form')->get('solr_index_products');
    $index = Index::load($solr_index);
    $solrResources = $index->query();
    $data['reindexedFileData'] = $solrResources->reindexResources($fileId, $familyId);
  }
}
