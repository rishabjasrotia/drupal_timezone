<?php

namespace Drupal\drupal_timezone\Services;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\drupal_timezone\ModuleConstants;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TimezoneHelper.
 */
class TimezoneHelper {

  /**
   * Config Factory Interface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * The Date Formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $date_formatter;

  /**
   * A date time instance.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  private $time;

  /**
   * Constructs a new TimezoneHelper object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config Factory Interface.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   A date time instance.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    DateFormatter $date_formatter,
    TimeInterface $time
  ) {
    $this->config = $config_factory->get(ModuleConstants::SETTINGS);
    $this->date_formatter = $date_formatter;
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('date.formatter'),
      $container->get('datetime.time')
    );
  }

   /**
   * Returns current time based on the time zone selection in config.
   *
   * @return string
   *   Returns formatted current date and time.
   */
  public function getFormattedDateTimeDetails() {
    return $this->date_formatter->format(
      $this->getCurrentTime(),
      'custom',
      'dS M Y - g:i A',
      $this->config->get('timezone') ?? NULL
    );
  }

  /**
   * Returns current time.
   *
   * @return string
   *   Returns current time.
   */
  public function getCurrentTime() {
    return $this->time->getCurrentTime();
  }

}