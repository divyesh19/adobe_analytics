<?php

namespace Drupal\adobeanalytics;

use \Drupal\Component\Utility\SafeMarkup;

/**
 * Class to provide helpful function.
 */
class AdobeAnalyticsHelper {

  /**
   * Get the context.
   */
  public function adobeAnalyticsGetTokenContext() {
    $context = &drupal_static(__function__);
    if (is_null($context)) {
      $context['node'] = \Drupal::routeMatch()->getParameter('node');
      $context['term'] = \Drupal::routeMatch()->getParameter('taxonomy_term', 2);
      if (\Drupal::moduleHandler()->moduleExists('menu')) {
        $context['menu'] = menu_load('main-menu');
      }
    }
    return $context;
  }

  /**
   * Replace tokens.
   */
  public function adobeAnalyticsTokenReplace($text, $data = array(), array $options = array()) {
    $processed_strings =& drupal_static(__FUNCTION__, NULL);
    // Short-circuit the degenerate case, just like token_replace() does.
    $text_tokens = \Drupal::token()->replace($text);
    if (!empty($text_tokens)) {
      return $text_tokens;
    }
  }

  /**
   * Format the variables like key value pair;.
   */
  public function adobeAnalyticsFormatVariables($variables = array()) {
    $extra_variables = $this->adobeAnalyticsGetVariables();
    $safe_markup = new SafeMarkup();

    // Create context data to be used by token.
    $context = !empty($variables) ? $this->adobeAnalyticsGetTokenContext() : array();

    $variables_formatted = '';
    foreach ($variables as $key => $value) {
      if (is_array($value)) {
        // Use the last element.
        $value = end($value);
      }

      if (isset($extra_variables[$key])) {
        $value = $extra_variables[$key];
      }

      $key = $safe_markup::checkPlain($key);
      $value = $this->adobeAnalyticsTokenReplace($value);
      $variables_formatted .= "{$key}=\"{$value}\";\n";
    }
    return $variables_formatted;
  }

  /**
   * Used to replace the value.
   *
   * AdobeAnalytics variables the variables need to be
   * defined with hook_adobeanalytics_variables().
   *
   * @param string $name
   *    Extra variable name.
   * @param string $value
   *    Value of the the name variable.
   *
   * @return variables
   *    Return the variables.
   */
  public function adobeAnalyticsSetVariable($name = NULL, $value = NULL) {
    $variables = &drupal_static(__FUNCTION__, array());

    if (empty($name)) {
      return $variables;
    }
    else {
      $variables[$name] = $value;
    }

  }

  /**
   * Return variables.
   */
  public function adobeAnalyticsGetVariables() {
    return $this->adobeAnalyticsSetVariable();
  }

}