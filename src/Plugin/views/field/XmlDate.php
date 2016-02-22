<?php

/**
 * @file
 * Contains \Drupal\views_xml_backend\Plugin\views\field\XmlDate.
 */

namespace Drupal\views_xml_backend\Plugin\views\field;

use Drupal\views_xml_backend\Plugin\views\field\XmlText;
use Drupal\views\ResultRow;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\Component\Utility\SafeMarkup;

/**
 * A handler to provide an XML date field.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("xml_date")
 */
class XmlDate extends XmlText {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['date_format'] = array('default' => 'small');
    $options['custom_date_format'] = array('default' => '');
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {

    parent::buildOptionsForm($form, $form_state);

    $date_formats = array();
    $date_types = DateFormat::loadMultiple();
    foreach ($date_types as $key => $value) {
      $date_formats[$key] = SafeMarkup::checkPlain(t($value->get('label') . ' format')) . ': ' . format_date(REQUEST_TIME, 'custom', $value->getPattern());
    }

    $form['date_format'] = array(
      '#type' => 'select',
      '#title' => t('Date format'),
      '#options' => $date_formats + array(
        'custom' => t('Custom'),
        'raw time ago' => t('Time ago'),
        'time ago' => t('Time ago (with "ago" appended)'),
        'raw time hence' => t('Time hence'),
        'time hence' => t('Time hence (with "hence" appended)'),
        'raw time span' => t('Time span (future dates have "-" prepended)'),
        'inverse time span' => t('Time span (past dates have "-" prepended)'),
        'time span' => t('Time span (with "ago/hence" appended)'),
      ),
      '#default_value' => isset($this->options['date_format']) ? $this->options['date_format'] : 'small',
    );
    $form['custom_date_format'] = array(
      '#type' => 'textfield',
      '#title' => t('Custom date format'),
      '#description' => t('If "Custom", see <a href="http://us.php.net/manual/en/function.date.php" target="_blank">the PHP docs</a> for date formats. Otherwise, enter the number of different time units to display, which defaults to 2.'),
      '#default_value' => isset($this->options['custom_date_format']) ? $this->options['custom_date_format'] : '',
      '#dependency' => array('edit-options-date-format' => array('custom', 'raw time ago', 'time ago', 'raw time hence', 'time hence', 'raw time span', 'time span', 'raw time span', 'inverse time span', 'time span')),
    );
  }

  public function render(ResultRow $values) {
    $value = $values->{$this->options['id']};

    // We assume if the date is numeric, then it already is a unix timestamp.
    if (!is_numeric($value)) {
      $value = strtotime($value);
    }
    $format = $this->options['date_format'];
    if (in_array($format, array('custom', 'raw time ago', 'time ago', 'raw time hence', 'time hence', 'raw time span', 'time span', 'raw time span', 'inverse time span', 'time span'))) {
      $custom_format = $this->options['custom_date_format'];
    }

    if ($value) {
      $time_diff = REQUEST_TIME - $value; // Will be positive for a datetime in the past (ago), and negative for a datetime in the future (hence).
      switch ($format) {
        case 'raw time ago':
          return \Drupal::service('date.formatter')->formatInterval($time_diff, is_numeric($custom_format) ? $custom_format : 2);

        case 'time ago':
          return t('%time ago', array('%time' => \Drupal::service('date.formatter')->formatInterval($time_diff, is_numeric($custom_format) ? $custom_format : 2)));

        case 'raw time hence':
          return \Drupal::service('date.formatter')->formatInterval(-$time_diff, is_numeric($custom_format) ? $custom_format : 2);

        case 'time hence':
          return t('%time hence', array('%time' => \Drupal::service('date.formatter')->formatInterval(-$time_diff, is_numeric($custom_format) ? $custom_format : 2)));

        case 'raw time span':
          return ($time_diff < 0 ? '-' : '') . \Drupal::service('date.formatter')->formatInterval(abs($time_diff), is_numeric($custom_format) ? $custom_format : 2);

        case 'inverse time span':
          return ($time_diff > 0 ? '-' : '') . \Drupal::service('date.formatter')->formatInterval(abs($time_diff), is_numeric($custom_format) ? $custom_format : 2);

        case 'time span':
          return t(($time_diff < 0 ? '%time hence' : '%time ago'), array('%time' => \Drupal::service('date.formatter')->formatInterval(abs($time_diff), is_numeric($custom_format) ? $custom_format : 2)));
        case 'custom':
          if ($custom_format == 'r') {
            return format_date($value, $format, $custom_format, NULL, 'en');
          }
          return format_date($value, $format, $custom_format);

        default:
          return format_date($value, $format);
      }
    }
  }

}