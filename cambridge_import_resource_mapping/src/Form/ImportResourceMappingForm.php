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
   
    $database = \Drupal::database();

    if (!empty($form_state->getValue('file'))){
      // Get the uploaded file FID
      $csv_file = File::load($form_state->getValue('file')[0]);
      // Read the file from files folder and get the contents
      $file = fopen($csv_file->getFileUri(), "r");   
      // skip the header row of the CSV
      while (!feof($file)) {
        $resource[] = fgetcsv($file);
      }
      $res = array_slice($resource,1);
      foreach($res as $row){
       //insert query goes here 
       $fields["familyId"] = $row[0];
       $fields["resourceId"] = $row[1];
       $fields["isbn/familyId"] = $row[2];
       $fields["unit description"] = $row[3];
       $fields["lesson description"] = $row[4];
       $fields["page number"] = $row[5];

       $id_exists = $conn->select('cambridge_import_resource_mapping')
       ->condition('isbn/familyId', $fields["isbn/familyId"], '=')
       ->execute();
       
       if(count($id_exists) == 0){
          $fileId = $fields["resourceId"];        
          $familyId = $fields["familyId"];
       $conn->insert('cambridge_import_resource_mapping')
          ->fields(['familyId' => $fields["familyId"] ,
                    'resourceId'=> $fields["resourceId"] ,
                    'isbn/familyId'=> $fields["isbn/familyId"] ,
                    'unit description'=> $fields["unit description"] ,
                    'lesson description'=> $fields["lesson description"] ,
                    'page number'=> $fields["page number"] ])
          ->execute();
       }
       $reindexservicecall = \Drupal::service('cambridge_import_resource_mapping.services');
      }
      fclose($file);
    }
    \Drupal::messenger()->addStatus(t(' Successfull.'));
    return $form;
  }
}
