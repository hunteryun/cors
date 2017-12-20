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
    $this->allowed_origins = config('cors.cors')->get('allowed_origins');
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
      '#title' => t('允许的域'),
      '#rows' => '5',
      '#default_value' => implode("\n", array_keys($this->allowed_origins)),
      '#description' => t('一行一个地址, 比如：http://example.com.'),
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
      $settings = array();
      if(!empty($values['allowed_origins'])){
        $domains = explode("\n", $values['allowed_origins']);
        foreach ($domains as $domain) {
          $settings[$domain] = true;
        }
      }

      $config = config('cors.cors');
      $config->set('allowed_origins', $settings);
      $config->save();
      return true;
    }
  }

}
