<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\field\XmlMarkup.
 */

namespace Drupal\views_xml_backend\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\views\ResultRow;
use Drupal\views_xml_backend\Sorter\StringSorter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A handler to provide an XML markup field.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("xml_markup")
 */
class XmlMarkup extends XmlText {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new XmlMarkup object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['format'] = ['default' => ''];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    foreach (filter_formats($this->currentUser) as $id => $format) {
      $options[$id] = $format->get('name');
    }

    $form['format'] = [
      '#title' => $this->t('Format'),
      '#description' => $this->t('The filter format'),
      '#type' => 'select',
      '#default_value' => $this->options['format'],
      '#required' => TRUE,
      '#options' => $options,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $row) {
    $values = $this->getXmlListValue($row);

    $output_values = [];

    foreach ($values as $value) {
      $value = str_replace('<!--break-->', '', $value);

      $output_values[] = check_markup($value, $this->options['format']);
    }

    return $this->renderXmlRow($output_values);
  }

  /**
   * {@inheritdoc}
   */
  public function sanitizeValue($value, $type = NULL) {
    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function clickSort($order) {
    $this->query->addSort(new StringSorter($this->realField, $order));
  }

}
