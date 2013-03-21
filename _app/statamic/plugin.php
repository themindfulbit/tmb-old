<?php
class Plugin {

	public $attributes;
	public $content;

  public function __construct() {
    Lex_Autoloader::register();
    $this->parser = new Lex_Parser();
    $this->parser->scope_glue(':');
    $this->parser->cumulative_noparse(true);
  }

	/**
     * Fetch Parameter
     *
     * This method fetches tag parameters if they exist, and returns their value
     * or a given default if not found
     *
     * @author  Jack McDade
     * @param   string    $param       Parameter to be checked
     * @param   string    $default     Default value
     * @param   boolean   $is_valid    Allows a boolean callback function to validate parameter
     * @param   boolean   $is_boolean  Indicates parameter is boolean (yes/no)
     * @return  mixed     Returns the parameter's value if found, default if not, or boolean if yes/no style
     */
	public function fetch_param($param, $default=null, $is_valid=false, $is_boolean=false, $force_lower=true) {
		
    if (isset($this->attributes[$param])) {

      $found_param = $force_lower ? strtolower($this->attributes[$param]) : $this->attributes[$param];

			if ($is_valid == false || ($is_valid !== false && function_exists($is_valid) && $is_valid($found_param) === true)) {

        # Yes/no parameters
				if ($is_boolean === true) {
          return $found_param === 'yes';
				}

        # Standard result
				return $found_param;
			}
		}

    # Not found
		return $default;
	}

  public function explode_options($string, $keyed = false) {
      
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

  public function parse_loop($content, $assoc_array) {

    $output = "";

    $count = 1;
    $total_results = count($assoc_array);
    foreach ($assoc_array as $key => $post) {
      
      $assoc_array[$key]['count'] = $count;
      
      $assoc_array[$key]['total_results'] = $total_results;

      if ($count === 1) {
        $assoc_array[$key]['first'] = true;
      }

      if ($count == $total_results) {
        $assoc_array[$key]['last'] = true;
      }
      $count++;
    }

    foreach ($assoc_array  as $item) {
      $c = $content;

      // replace all instances of { variable } with variable
      $regex = '/\{(?!\{)\s*(([|a-zA-Z0-9_\.]+))\s*\}(?!\})/im';
      if (preg_match_all($regex, $c, $data_matches, PREG_SET_ORDER + PREG_OFFSET_CAPTURE)) {
        foreach ($data_matches as $match) {
          $tag = $match[0][0];
          $name = $match[1][0];
          if (isset($item[$name])) {
            $c = str_replace($tag, $item[$name], $c);
          }
       }
      }

      $output .= $this->parser->parse($c, $item);
    }

    return $output;
  }

}