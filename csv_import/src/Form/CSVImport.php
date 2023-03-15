<?php

namespace Drupal\csv_import\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\node\Entity\Node;

class CSVImport extends FormBase {

  public function getFormId() {
    return 'csv_import_file'; 
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = array(
      '#attributes' => array('enctype' => 'multipart/form-data'),
    );
    
    $form['file_upload_details'] = array(
      '#markup' => ('<b>The File</b>'),
    );
	
    $validators = array(
      'file_validate_extensions' => array('csv'),
    );
    $form['my_file'] = array(
      '#type' => 'managed_file',
      '#name' => 'my_file',
      '#title' => ('File *'),
      '#size' => 20,
      '#description' => ('Upload CSV file'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/',
    );
    
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Import CSV'),
      '#button_type' => 'primary',
    );
    
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {    
    if ($form_state->getValue('my_file') == NULL) {
      $form_state->setErrorByName('my_file', $this->t('File.'));
    }
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $fid = $form_state->getValue('my_file');

    $db = \Drupal::database();
    $data = $db->select('file_managed', 'fe')
      ->fields('fe')
      ->orderBy('fe.fid', 'DESC')
      ->range(0, 1)
      ->condition('fe.fid', $fid, '=')
      ->execute();
    $value = $data->fetchAssoc();
    $filename = $value['filename'];

    $filepath = 'public://my_files/'.$filename;

    $file = fopen($filepath, "r");

    $row = 1; // starting row 
    while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
      if($row == 1){ $row++; continue; } // Skip the first row

      $naslov = $getData[0];
      $tekst = $getData[1];
      $slika = $getData[2];
      $kategorija = $getData[3];

      $node = Node::create([
        'type'        => 'Imported News',
        'title'       => $naslov,
        'tekst'       => $tekst

        ],
      );
      $node->save();
    }
    fclose($file);  

    $this->messenger()->addStatus("CSV File imported.");
  }

}

