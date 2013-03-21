<?php
/**
 * Statamic_Helper
 * Provides utility functionality for Statamic
 *
 * @author      Mubashar Iqbal
 * @author      Jack McDade
 * @author      Fred LeBlanc
 * @copyright   2012 Statamic
 * @link        http://www.statamic.com
 * @license     http://www.statamic.com
 */
class Statamic_Helper {
  /* commonly used regex patterns */
  static $date_regex              = "/\d{4}[\-_\.](?:\d{2}[\-_\.]){2}/";
  static $datetime_regex          = "/\d{4}[\-_\.](?:\d{2}[\-_\.]){2}\d{4}[\-_\.]/";
  static $date_or_datetime_regex  = "/\d{4}[\-_\.](?:\d{2}[\-_\.]){2}(?:\d{4}[\-_\.])?/";
  static $numeric_regex           = "/\d+[\-_\.]/";
  

  /**
   * starts_with
   * Determines if a given $haystack starts with $needle
   *
   * @param string  $haystack  String to inspect
   * @param string  $needle  Character to look for
   * @return boolean
   */
  public static function starts_with($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
  }


  /**
   * ends_with
   * Determines if a given $haystack ends with $needle
   *
   * @param string  $haystack  String to inspect
   * @param string  $needle  Character to look for
   * @param boolean  $case  Perform a case-sensitive search?
   * @return boolean
   */
  public static function ends_with($haystack, $needle, $case=true) {
    if ($case) {
      return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
    }
    return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
  }


  /**
   * is_date_slug
   * Determines if a given $slug is date-based and is valid or not
   *
   * @param string  $slug  Slug to inspect
   * @return boolean
   */
  public static function is_date_slug($slug) {
    if (preg_match(self::$date_regex, $slug, $m)) {
      return self::is_valid_date($m[0]);
    }

    return false;
  }


  /**
   * is_datetime_slug
   * Determines if a given $slug is datetime-based and is valid or not
   *
   * @param string  $slug  Slug to inspect
   * @return boolean
   */
  public static function is_datetime_slug($slug) {
    if (preg_match(self::$datetime_regex, $slug, $m)) {
      return self::is_valid_date($m[0]);
    }

    return false;
  }


  /**
   * is_valid_date
   * Determines if a given date or datetime represents a real date
   *
   * @param string  $date  A yyyy-mm-dd(-hhii)[-ish] formatted string for checking
   * @return boolean
   */
  public static function is_valid_date($date) {
    // trim string down to just yyyy-mm-dd
    $date = substr($date, 0, 10);

    // grab the delimiter (character after yyyy)
    $delimiter = substr($date, 4, 1);

    // explode that into chunks
    $chunks = explode($delimiter, $date);

    return checkdate((int) $chunks[1], (int) $chunks[2], (int) $chunks[0]);
  }


  /**
   * is_numeric_slug
   * Determines if a given $slug is numeric-based or not
   *
   * @param string  $slug  Slug to inspect
   * @return boolean
   */
  public static function is_numeric_slug($slug) {
    return (bool) preg_match("/\d[\.\-]/i", $slug);
  }


  /**
   * get_datestamp
   * Gets a timestamp from a given $date_slug
   *
   * @deprecated  Use self::get_datetimestamp instead
   *
   * @param string  $date_slug  Date slug to inspect
   * @return integer
   */
  public static function get_datestamp($date_slug) {
    return self::get_datetimestamp($date_slug);
  }


  /**
   * get_datetimestamp
   * Gets a timestamp from a given $date_slug
   *
   * @param string  $date_slug  Date (or datetime) slug to inspect
   * @return integer
   */
  public static function get_datetimestamp($date_slug) {
    if (!preg_match(self::$date_or_datetime_regex, $date_slug, $m) || !self::is_valid_date($m[0])) {
      return false;
    }
    
    $date_string = substr($m[0], 0, 10);
    $delimiter = substr($date_string, 4, 1);
    $date_array = explode($delimiter, $date_string);

    // check to see if this is a full date and time
    $time_string = (strlen($m[0]) > 11) ? substr($m[0], 11, 4) : '0000';

    // construct the stringed time
    $d = $date_array[2] . '-' . $date_array[1] . '-' . $date_array[0];
    $t = substr($time_string, 0, 2) . ":" . substr($time_string, 2);

    return strtotime("{$d} {$t}");
  }


  /**
   * get_numeric
   * Gets the numeric value of the $numeric_slug
   *
   * @param string  $numberic_slug  Numeric slug to inspect
   * @return integer
   */
  public static function get_numeric($numeric_slug) {

    preg_match("/\d*/i", $numeric_slug, $matches);
    return $matches[0];
  }


  /**
   * reduce_double_slashes
   * Removes instances of "//" from a given $string except for URL protocols
   *
   * @param string  $string  String to reduce
   * @return string
   */
  public static function reduce_double_slashes($string) {
    return preg_replace("#(^|[^:])//+#", "\\1/", $string);
  }


  /**
   * trim_slashes
   * Removes any extra "/" at the beginning or end of a given $string
   *
   * @param string  $string  String to trim
   * @return string
   */
  public static function trim_slashes($string) {
    return trim($string, '/');
  }


  /**
   * remove_numerics_from_path
   * Strips out any instances of a numeric ordering from a given $path
   *
   * @todo this should be named something more generic to incorporate datetimes that it accommodates
   *
   * @param string  $path  String to strip out numerics from
   * @return string
   */
  public static function remove_numerics_from_path($path) {
    if ($path) {
      $parts = explode("/", substr($path, 1));
      $fixedpath = "/";
      foreach ($parts as $part) {
        if (preg_match("/\d[\.\-\_]/i", $part)) {
          if (self::is_date_slug($part)) {
            $part  = preg_replace(self::$date_regex, '', $part);
          } else {
            $part  = preg_replace(self::$numeric_regex, '', $part);
          }
        }
        if ($fixedpath <> '/') $fixedpath .= '/';
        $fixedpath .= $part;
      }
      $path = $fixedpath;
    }
    return $path;
  }


  /**
   * pop_last_segment
   * Pops the last segment off of a given $url and returns the appropriate array.
   *
   * @param string  $url  URL to derive segments from
   * @return string
   */
  public static function pop_last_segment($url) {
    $url_array = explode('/', $url);
    
    array_pop($url_array);

    if (is_array($url_array))
      return implode('/', $url_array);

    return $url_array;
  }


  /**
   * resolve_path
   * Finds the actual path from a URL-friendly $path
   *
   * @param string  $path  Path to resolve
   * @return string
   */
  public static function resolve_path($path) {
    $content_root = Statamic::get_content_root();
    $content_type = Statamic::get_content_type();

    if (strpos($path, "/") === 0) {
      $parts = explode("/", substr($path, 1));
    } else {
      $parts = explode("/", $path);
    }

    $fixedpath = "/";

    foreach ($parts as $part) {
      if (file_exists("{$content_root}{$fixedpath}/{path}.{$content_type}") || is_dir("{$content_root}{$fixedpath}/{$part}")) {
        // don't rename it exists!
      } else {
        // check folders
        $list = Statamic::get_content_tree("{$fixedpath}", 1, 1, false, true, false);
        foreach ($list as $item) {
          $t = basename($item['slug']);
          if (self::is_numeric_slug($t)) {
            $nl = strlen(self::get_numeric($t)) + 1;
            if (strlen($part) >= (strlen($item['slug'])-$nl) && self::ends_with($item['slug'], $part)) {
              $part = $item['slug'];
              break;
            }
          } else {
            if (self::ends_with($item['slug'], $part)) {
              if (strlen($part) >= strlen($t)) {
                $part = $item['slug'];
                break;
              }
            }
          }
        }

        // check files
        $list = Statamic::get_file_list("{$fixedpath}");
        foreach ($list as $key => $item) {
          if (self::ends_with($key, $part)) {
            $t = basename($item);
            if (Statamic::get_entry_timestamps() && self::is_datetime_slug($t)) {
              if (strlen($part) >= (strlen($key)-16)) {
                $part = $key;
                break;
              }
            } else if (self::is_date_slug($t)) {
              if (strlen($part) >= (strlen($key)-11)) {
                $part = $key;
                break;
              }
            } else if (self::is_numeric_slug($t)) {
              $nl = strlen(self::get_numeric($key)) + 1;
              if (strlen($part) >= (strlen($key)-$nl)) {
                $part = $key;
                break;
              }
            } else {
              $t = basename($item);
              if (strlen($part) >= strlen($t)) {
                $part = $key;
                break;
              }
            }
          }
        }
      }

      if ($fixedpath <> '/') $fixedpath .= '/';
      $fixedpath .= $part;
    }
    return $fixedpath;
  }


  /**
   * is_file_newer
   * Checks to see if $file is newer than $compare_to_this_one
   *
   * @param string  $file  File for comparing
   * @param string  $compare_to_this_one  Path and name of file to compare against $file
   * @return boolean
   */
  public static function is_file_newer($file, $compare_to_this_one) {
    return (filemtime($file) > filemtime($compare_to_this_one));
  }


  /**
   * is_valid
   * Determines if the given $uuid is valid
   *
   * @param string  $uuid  UUID to validate
   * @return boolean
   */
  public static function is_valid($uuid) {
    return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
  }


  /**
   * random_string
   * Returns a random string $length characters long
   *
   * @param string  $length  Length of random string to return
   * @return string
   */
  public static function random_string($length = 32) {
    $s = '';
    $c = "BCDFGHJKLMNPQRSTVWXYZbcdfghjklmnpqrstvwxwz0123456789";
    for(;$length > 0;$length--) $s .= $c{rand(0,strlen($c)-1)};
    return str_shuffle($s);
  }


  /**
   * build_file_content
   * Creates a file content from $data_array and $content
   * 
   * @param array  $data_array  Data to load into the file's front-matter
   * @param string  $content  Content to append to the file
   * @return string
   */
  public static function build_file_content($data_array, $content) {
    $file_content = "";
    $file_content .= Spyc::YAMLDump($data_array, false, 0);
    $file_content .= "---\n";
    $file_content .= $content;

    return $file_content;
  }


  /**
   * get_template
   * Get a fully-parsed HTML template
   *
   * @param string  $template  Template name to use
   * @param array  $data  Option array of data to incorporate into the template
   * @return string
   */
  public static function get_template($template, $data = array()) {
    $app = \Slim\Slim::getInstance();

    Lex_Autoloader::register();
    $parser = new Lex_Parser();
    $parser->scope_glue(':');

    $template_path = $app->config['templates.path'] . '/templates/' . ltrim($template, '/').'.html';

    if (file_exists($template_path)) {
      $html = $parser->parse(file_get_contents($template_path), $data, false);
    }

    return $html;
  }

  /**
   * is
   * Determine if a given string matches a given pattern
   *
   * @param string  $pattern  Pattern to look for in $value
   * @param string  $value  String to look through
   * @return boolean
   */
  public static function is($pattern, $value) {
    if ($pattern !== '/') {
      $pattern = str_replace('*', '(.*)', $pattern).'\z';
    } else {
      $pattern = '^/$';
    }

    return preg_match('#' . $pattern . '#', $value);
  }


  /**
   * prettify
   * Converts a string from underscore-slug-format to normal-format
   *
   * @param string  $string  String to convert
   * @return string
   */
  public static function prettify($string) {
    return ucwords(preg_replace('/[_]+/', ' ', strtolower(trim($string))));
  }


  /**
   * slugify
   * Converts a string from normal-format to slug-format
   *
   * credit: http://sourcecookbook.com/en/recipes/8/function-to-slugify-strings-in-php
   *
   * @param string  $text  String to convert
   * @return string
   */
  public static function slugify($text) {
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
 
    // trim
    $text = trim($text, '-');
 
    // transliterate
    // if (function_exists('iconv'))
    // {
    //     $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    // }
 
    // lowercase
    // ### $MUBS$ is this necesary
    // $text = strtolower($text);
 
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
 
    if (empty($text)) {
        return 'n-a';
    }
 
    return $text;
  }


  /**
   * deslugify
   * Converts a string from slug-format to normal-format
   *
   * @param string  $string  String to convert
   * @return string
   */
  public static function deslugify($text) {
      // replace dash with a 'space'
      $text = preg_replace('~-~', ' ', $text);
   
      // trim
      $text = trim($text, ' ');
   
      return $text;
  }

    public static function explode_options($string, $keyed = false) {
      
    $options = explode('|', $string);

    if ($keyed) {
      $temp_options = array();
      foreach ($options as $key => $value) {

        if (strpos($value, ':')) {
          # key:value pair present
          list($option_key, $option_value) = explode(':', $value);
        } else {

          # default value is false
          $option_key = $value;
          $option_value = false;
        }
        # set the main options array
        $temp_options[$option_key] = $option_value;
      }
      # reassign and override
      $options = $temp_options;
    }
    return $options;
  }


  /**
   * array_empty
   * Determines if a given $mixed value is an empty array or not
   *
   * @param mixed  $array  Value to check for an empty array
   * @return boolean
   */
  public static function array_empty($mixed) {
    if (is_array($mixed)) {
      foreach ($mixed as $value) {
        if ( ! self::array_empty($value)) {
          return false;
        }
      }
    } elseif ( ! empty($mixed) || $mixed !== '') {
      return false;
    }

    return true;
  }
}
