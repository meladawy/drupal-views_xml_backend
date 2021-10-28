<?php

namespace Drupal\views_xml_backend;

/**
 * The default messenger.
 */
class Messenger implements MessengerInterface {

  /**
   * {@inheritdoc}
   */
  public function setMessage($message = NULL, $type = 'status', $repeat = FALSE) {
    \Drupal::messenger()->addMessage($message, $type, $repeat);
  }

}
