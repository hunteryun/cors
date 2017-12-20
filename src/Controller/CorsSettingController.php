<?php

namespace Hunter\cors\Controller;

use Zend\Diactoros\ServerRequest;

/**
 * Class CorsSetting.
 *
 * @package Hunter\cors\Controller
 */
class CorsSettingController {

  protected $allowed_origins;

  /**
   * Constructs a cors config.
   */
  public function __construct() {
    $this->allowed_origins = config('cors')->get('allowed_origins');
  }

  /**
   * cors_setting.
   *
   * @return string
   *   Return cors_setting string.
   */
  public function cors_setting(ServerRequest $request) {
    $form['allowed_origins'] = [
      '#type' => 'textarea',
      '#title' => t('Allowed Origins'),
      '#rows' => '5',
      '#default_value' => $this->allowed_origins,
    ];

    $form['save'] = array(
     '#type' => 'submit',
     '#value' => t('Save'),
     '#attributes' => array('lay-submit' => '', 'lay-filter' => 'configSubmit'),
    );

    return view('/admin/cors-setting.html', array('form' => $form));
  }

  /**
   * cors_settting_submit.
   *
   * @return string
   *   Return cors_settting_submit string.
   */
  public function cors_settting_submit(ServerRequest $request) {
    if ($values = $request->getParsedBody()) {
      $config = config('cors');
      $config->set('allowed_origins', $values['allowed_origins']);
      $config->save();
      return true;
    }
  }

}
