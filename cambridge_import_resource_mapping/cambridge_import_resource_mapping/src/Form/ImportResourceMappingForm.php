<?php

namespace Drupal\cambridge_import_resource_mapping\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use \Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides the form for adding isbn.
 */
class ImportResourceMappingForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ImportResourceMappingForm';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('cambridge_localization.countriescreationadminform');
    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div id="result-message"></div>'
    ];
  
    $form['file'] = array(
      '#type' => 'managed_file',
      '#title' => $this->t('Upload the CSV'),
      '#upload_location' => 'public://',
      '#description' => t('upload the CSV file to import the Family and Resourceinfo'),
      '#upload_validators'  => array(
        'file_validate_extensions' => array('csv'),
        'file_validate_name' => array(),
      ),
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $solr_index =
    \Drupal::config('cambridge_external_app.api_data_config_form')->get('solr_index_products');
    $count = 0;

    if (!empty($form_state->getValue('file'))){
      // Get the uploaded file FID
      $csv_file = File::load($form_state->getValue('file')[0]);
      // Read the file from files folder and get the contents
      $file = fopen($csv_file->getFileUri(), "r");    
      while (($fields = fgetcsv($file,2, ",")) !== FALSE) {
        $count++;
        if ($count == 1) { continue; } 
     // while (!feof($file)) {
        echo "ready to read";
        $resource = fgetcsv($file);
        
        print_r($resource);
        //die;
        $database = \Drupal::database();
        
      }
      fclose($file);
      die;
    }
    \Drupal::messenger()->addStatus(t(' Successfull.'));
    return $form;
  }
}
