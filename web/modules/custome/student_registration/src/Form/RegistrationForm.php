<?php
/**
 * @file
 * Contains \Drupal\student_registration\Form\RegistrationForm.
 */
namespace Drupal\student_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Link;

use Drupal\Core\Url;
use Drupal\Core\Routing;


class RegistrationForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'student_registration_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['student_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter Name:'),
      '#required' => TRUE,
    );
    $form['student_roll'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter Enrollment Number:'),
      '#required' => TRUE,
    );
    $form['student_mail'] = array(
      '#type' => 'email',
      '#title' => t('Enter Email ID:'),
      '#required' => TRUE,
    );
    $form['student_phone'] = array (
      '#type' => 'tel',
      '#title' => t('Enter Contact Number'),
    );
    $form['student_dob'] = array (
      '#type' => 'date',
      '#title' => t('Enter DOB:'),
      '#required' => TRUE,
    );
    $form['student_gender'] = array (
      '#type' => 'select',
      '#title' => ('Select Gender:'),
      '#options' => array(
        'Male' => t('Male'),
        'Female' => t('Female'),
        'Other' => t('Other'),
      ),
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Register'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if(strlen($form_state->getValue('student_roll')) < 8) {
      $form_state->setErrorByName('student_roll', $this->t('Please enter a valid Enrollment Number'));
    }
    if(strlen($form_state->getValue('student_phone')) < 10) {
      $form_state->setErrorByName('student_phone', $this->t('Please enter a valid Contact Number'));
    }
  }
  public function submitForm(array & $form, FormStateInterface $form_state) {
    try{
      $conn = Database::getConnection();

      $field = $form_state->getValues();

      $fields["student_name"] = $field['student_name'];
      $fields["student_roll"] = $field['student_roll'];
      $fields["student_mail"] = $field['student_mail'];
      $fields["student_phone"] = $field['student_phone'];
      $fields["student_dob"] = $field['student_dob'];
      $fields["student_gender"] = $field['student_gender'];

      $conn->insert('students')
        ->fields($fields)->execute();
      \Drupal::messenger()->addMessage($this->t('The Student data has been successfully saved'));


    } catch(Exception $ex){
      \Drupal::logger('student_registration')->error($ex->getMessage());
    }

  }

  public function display()
  {
    /**return [
     * '#type' => 'markup',
     * '#markup' => $this->t('Implement method: display with parameter(s): $name'),
     * ];*/
    //create table header
    $header_table = array(
      'id' => t('SrNo'),
      'student_name' => t('student_name'),
      'student_roll' => t('student_roll'),
      'student_phone'=> t('student_phone'),
      'student_mail'=> t('student_mail'),
      'action'=>$this->t('action')
    );

//select records from table
    $query = \Drupal::database()->select('students', 'm');
    $query->fields('m', ['id', 'student_name', 'student_roll', 'student_phone','student_mail']);


    $results = $query->execute()->fetchAll();
    $rows = array();
    foreach ($results as $data) {
//      $delete = Url::fromUserInput('/student-registration/form/delete' . $data->id);
//      $edit = Url::fromUserInput('/mydata/form/mydata?num=' . $data->id);
      $delete_link = Link::createFromRoute($this->t('Delete'),'student_registration.delete_form',['id'=>$data->id], ['absolute' => TRUE]);
      $build_link_action = [
        'action_delete' => [
          '#type' => 'html_tag',
          '#value' => $delete_link->toString(),
          '#tag' => 'div',
          '#attributes'=>['class'=>['action-edit']]
        ]
      ];
//      $rows[] = ['data' => \Drupal::service('renderer')->render($build_link_action)];


      $rows[] = array(
        'id' => $data->id,
        'student_name' => $data->student_name,
        'student_roll' => $data->student_roll,
        'student_phone' => $data->student_phone,
        'student_mail' => $data->student_mail,
        'action'=>\Drupal::service('renderer')->render($build_link_action)  
    );


    }
    //display data in site
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No users found'),
    ];

    return $form;
  }


}







