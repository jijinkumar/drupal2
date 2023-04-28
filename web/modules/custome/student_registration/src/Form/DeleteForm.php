<?php

namespace Drupal\student_registration\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Render\Element;

class DeleteForm extends ConfirmFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'delete_form';
  }
  public $id;
//// edited
  public function getQuestion()
  {
    return t('Do you want to delete %id?', array('%cid' => $this->id));
  }

  public function getCancelUrl()
  {
    return new Url('student_registration.display_table_controller_display');
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $query = \Drupal::database();
    $query->delete('students')
      ->condition('id',$this->id)
      ->execute();
    drupal_set_message("succesfully deleted");
    $form_state->setRedirect('student_registration.display_table_controller_display');

  }
}
