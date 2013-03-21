<?php
/**
 * Statamic
 *
 * @author      Jack McDade
 * @author      Mubashar Iqbal
 * @author      Fred LeBlanc
 * @copyright   2012 Statamic
 * @link        http://www.statamic.com
 * @license     http://www.statamic.com
 *
 */

if ( ! defined('STATAMIC_VERSION')) define('STATAMIC_VERSION', '1.4.2');

class Statamic {
  protected static $_yaml_cache = array();
  public static $folder_list = array();

  public static $publication_states = array('live' => 'Live', 'draft' => 'Draft', 'hidden' => 'Hidden');

  public static function loadYamlCached($content) {
    $yaml = array();
    $hash = md5($content);

    if (isset(self::$_yaml_cache[$hash])) {
      $yaml = self::$_yaml_cache[$hash];
    } else {
      $yaml = Spyc::YAMLLoad($content);
      self::$_yaml_cache[$hash] = $yaml;
    }
    return $yaml;
  }

  public static function load_all_configs() {
    // do all the other files then do the settings

    $pattern = "_config/*.yaml";
    $list = glob($pattern);

    $config = array();
    $file_config = Spyc::YAMLLoad('_config/settings.yaml');
    $config += $file_config;

    $routes = array();
    if (file_exists('_config/routes.yaml')) {
      $routes['_routes'] = Spyc::YAMLLoad('_config/routes.yaml');
    }

    if ($list) {
      foreach ($list as $name) {
        if ( ! Statamic_Helper::ends_with($name, "_config/settings.yaml")
          && ! Statamic_Helper::ends_with($name, "_config/routes.yaml") ) {
          $file_config = Spyc::YAMLLoad($name);
          $config = array_merge($config, $file_config, $routes);
        }
      }
    }

    // theme configs, allowing override
    $theme_list = glob('_themes/'.$config['_theme'].'/*.yaml');
    foreach ($theme_list as $name) {
      $config = array_merge($config, Spyc::YAMLLoad($name));
    }

    return $config;
  }

  public static function get_folder_list($folder,$future=false,$past=true) {
    if (isset(self::$folder_list[$folder])) {
      $folder_list = self::$folder_list[$folder];
    } else {
      $folder_list = Statamic::get_content_list($folder, null, 0, $future, $past, 'date', 'desc', null, null, false, false, null, null);
      self::$folder_list[$folder] = $folder_list;
    }
    return $folder_list;
  }

  public static function get_site_root() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_site_root'])) {
      return $app->config['_site_root'];
    }

    return "/";
  }

  public static function get_site_url() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_site_url'])) {
      return $app->config['_site_url'];
    }

    return "";
  }

  public static function get_site_name() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_site_name'])) {
      return $app->config['_site_name'];
    }

    return "";
  }

  public static function get_license_key() {
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_license_key'])) {
      return $app->config['_license_key'];
    }

    return '';
  }

  public static function get_theme_name() {
    // default: default
    $app = \Slim\Slim::getInstance();

    if (isset($app->config['_theme'])) {
      return $app->config['_theme'];
    }

    return "denali";
  }

  public static function get_theme_type() {
    // default: lex
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['theme_type'])) {
      return $app->config['theme_type'];
    }

    return "lex";
  }

  public static function get_theme() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_theme'])) {
      return $app->config['_theme'];
    }

    return "default";
  }

  public static function get_theme_assets_path() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_theme_assets_path'])) {
      return $app->config['_theme_assets_path'];
    }

    return "";
  }

  public static function get_theme_path() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['theme_path'])) {
      return $app->config['theme_path'];
    }

    return "_themes/".self::get_theme_name()."/";
  }

  public static function get_templates_path() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['templates.path'])) {
      return $app->config['templates.path'];
    }

    return "./_themes/".self::get_theme_name()."/";
  }

  public static function get_admin_path() {
   $app = \Slim\Slim::getInstance();
    if (isset($app->config['_admin_path'])) {
      return Statamic_Helper::reduce_double_slashes(ltrim($app->config['_admin_path'], '/').'/');
    }

    return "admin/";
  }

  public static function get_addon_path($addon=null) {
    $addon_path = Statamic_Helper::reduce_double_slashes(self::get_site_root()."_add-ons/".$addon."/");
    return $addon_path;
  }

  public static function get_content_root() {
    // default: content
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_content_root'])) {
      return $app->config['_content_root'];
    }

    return "_content";
  }

  public static function get_content_type() {
    $app = \Slim\Slim::getInstance();

    $content_type = 'md'; # default: markdown
    if (isset($app->config['_content_type']) && $app->config['_content_type'] != 'markdown') {
      $content_type = $app->config['_content_type'] == "markdown_edge" ? "md" : $app->config['_content_type'];
    }
    return $content_type;
  }

  public static function get_date_format() {
    // default: Ymd
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_date_format'])) {
      return $app->config['_date_format'];
    }

    return "Y-m-d";
  }

  public static function get_time_format() {
    // default: Ymd
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_time_format'])) {
      return $app->config['_time_format'];
    }

    return "h:ia";
  }


  public static function get_entry_timestamps() {
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_entry_timestamps'])) {
      return (bool) $app->config['_entry_timestamps'];
    }

    return false;
  }

  public static function get_setting($setting, $default = false){

    $app = \Slim\Slim::getInstance();

    if (isset($app->config[$setting])) {
      return $app->config[$setting];
    }

    return $default;
  }

  public static function get_entry_type($path) {
    $type = 'none';

    $content_root = Statamic::get_content_root();
    if (file_exists("{$content_root}/{$path}/fields.yaml")) { 

      $fields_raw = file_get_contents("{$content_root}/{$path}/fields.yaml");
      $fields_data = Spyc::YAMLLoad($fields_raw);

      if (isset($fields_data['type']) && ! is_array($fields_data['type'])) {
        $type = $fields_data['type']; # simplify, no "prefix" necessary
      } else if (isset($fields_data['type']['prefix'])) {
        $type = $fields_data['type']['prefix'];
      }
    }

    return $type;
  }

  # @todo: make recursive/helper
  public static function get_templates_list() {
    $templates = array();
    $folder = "_themes/".self::get_theme_name()."/templates/*";
    $list = glob($folder);
    if ($list) {
      foreach ($list as $name) {
        if (is_dir($name)) {
          $folder_array = explode('/',rtrim($name,'/'));
          $folder_name = end($folder_array);

          $sub_list = glob($name.'/*');
          
          foreach ($sub_list as $sub_name) {
            $start = strrpos($sub_name, "/")+1;
            $end = strrpos($sub_name, ".");
            $templates[] = $folder_name.'/'.substr($sub_name, $start, $end-$start);
          }
        } else {
          $start = strrpos($name, "/")+1;
          $end = strrpos($name, ".");
          $templates[] = substr($name, $start, $end-$start);
        }
      }
    }
    return $templates;
  }

  public static function get_layouts_list() {
    $templates = array();
    $folder = "_themes/".self::get_theme_name()."/layouts/*";
    $list = glob($folder);
    if ($list) {
      foreach ($list as $name) {
        $start = strrpos($name, "/")+1;
        $end = strrpos($name, ".");
        $templates[] = substr($name, $start, $end-$start);
      }
    }
    return $templates;
  }

  public static function get_pagination_variable() {
    $app = \Slim\Slim::getInstance();

    $var = 'page'; # default: page
    if (isset($app->config['_pagination_variable'])) {
      $var = $app->config['_pagination_variable'];
    }
    return $var;
  }

  public static function get_pagination_style() {
    $app = \Slim\Slim::getInstance();

    $var = 'prev_next'; # default: prev_next
    if (isset($app->config['_pagination_style'])) {
      $var = $app->config['_pagination_style'];
    }
    return $var;
  }

    public static function get_parse_order() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_parse_order'])) {
      return $app->config['_parse_order'];
    }

    return array('tags', 'content');
  }

  public static function is_content_writable() {
    $content_root = self::get_content_root();
    if (is_writable($content_root)) {
      return true;
    }
    return false;
  }

    public static function are_users_writable() {

    if (is_writable('_config/users/')) {
      return true;
    }
    return false;
  }

  public static function content_exists($slug, $folder=null) {
    $app = \Slim\Slim::getInstance();
    $initialize = $app->config;

    $site_root    = self::get_site_root();
    $content_root = self::get_content_root();
    $content_type = self::get_content_type();

    $meta_raw = "";
    if ($folder) {
      if (file_exists("{$content_root}/{$folder}/{$slug}.{$content_type}")) { 
        return true;
      }
    } else {
      if (file_exists("{$content_root}/{$slug}.{$content_type}")) { 
        return true;
      }
    }

    return false;
  }

  public static function get_content_meta($slug, $folder=null, $raw=false, $parse=true) {
    $app = \Slim\Slim::getInstance();
    $initialize = $app->config;

    $site_root    = self::get_site_root();
    $content_root = self::get_content_root();
    $content_type = self::get_content_type();

    $file = $folder ? "{$content_root}/{$folder}/{$slug}.{$content_type}" : "{$content_root}/{$slug}.{$content_type}";
    $file = Statamic_Helper::reduce_double_slashes($file);

    $meta_raw = file_exists($file) ? file_get_contents($file) : '';

    if (Statamic_Helper::ends_with($meta_raw, "---")) {
      $meta_raw .= "\n"; # prevent parse failure
    }
    # Parse YAML Front Matter
    if (stripos($meta_raw, "---") === FALSE) {
      //$meta = Spyc::YAMLLoad($meta_raw);
      $meta = array_merge(self::loadYamlCached($meta_raw), $app->config);
      $meta['content'] = "";
      if ($raw) {
        $meta['content_raw'] = "";
      }
    } else {
      list($yaml, $content) = preg_split("/---/", $meta_raw, 2, PREG_SPLIT_NO_EMPTY);
      $meta = self::loadYamlCached($yaml);
      
      if ($raw) {
        $meta['content_raw'] = $content;
      }
      
      // Parse the content if necessary
      $meta['content'] = $parse ? self::parse_content($content, $meta) : $content;
    }
    if (file_exists($file)) {
      $meta['last_modified'] = filemtime($file);  
    }
    
    if ( ! $raw) {
      $meta['homepage'] = self::get_site_root();
      $meta['raw_url']  = $app->request()->getResourceUri();
      $meta['page_url'] = $app->request()->getResourceUri();

      # Is date formatted correctly?
      if (self::get_entry_timestamps() && Statamic_Helper::is_datetime_slug($slug)) {
        $datetimestamp = Statamic_Helper::get_datetimestamp($slug);
        $datestamp = Statamic_Helper::get_datestamp($slug);

        $meta['datetimestamp'] = $datetimestamp;
        $meta['datestamp'] = $datestamp;
        $meta['date']      = date(self::get_date_format(), $datestamp);
        $meta['time']      = date(self::get_time_format(), $datetimestamp);
        $meta['page_url']  = preg_replace(Statamic_Helper::$datetime_regex, '', $meta['page_url']); # clean url override

      } else if (Statamic_Helper::is_date_slug($slug)) {
        $datestamp = Statamic_Helper::get_datestamp($slug);
        
        $meta['datestamp'] = $datestamp;
        $meta['date']      = date(self::get_date_format(), $datestamp);
        $meta['page_url']  = preg_replace(Statamic_Helper::$date_regex, '', $meta['page_url']); # clean url override

      } else if (Statamic_Helper::is_numeric_slug($slug)) {
        $meta['numeric'] = Statamic_Helper::get_numeric($slug);
      }
      
      $meta['permalink'] = Statamic_Helper::reduce_double_slashes(Statamic::get_site_url().'/'.$meta['page_url']);


      $taxonomy_slugify = false;
      if (isset($app->config['_taxonomy_slugify'])) {
        if ($app->config['_taxonomy_slugify']) {
          $taxonomy_slugify = true;
        }
      }

      # Jam it all together, brother.
      # @todo: functionize/abstract this method for more flexibility and readability
      foreach($meta as $key => $value) {

        if (! is_array($value) && self::is_taxonomy($key)) {
          $value = array($value);
          $meta[$key] = $value;
        }

        if (is_array($value)) {
          $list = array();
          $url_list = array();

          $i = 1;
          $total_results = count($meta[$key]);
          foreach ($meta[$key] as $k => $v) {
            
            $url = null;
            if (self::is_taxonomy($key)) {

              // DO NOT DO numerical regex replace on the actual taxonomy item
              $url = Statamic_Helper::reduce_double_slashes(strtolower($site_root.'/'.$folder.'/'.$key));
              $url = preg_replace(Statamic_Helper::$numeric_regex, '', $url);
              if($taxonomy_slugify) {
                $url .= "/".(strtolower(Statamic_Helper::slugify($v)));
              } else {
                $url .= "/".(strtolower($v));
              }


              $list[] = array(
                'name'  => $v,
                'count' => $i,
                'url'   => $url,
                'total_results' => $total_results,
                'first' => $i == 1 ? TRUE : FALSE,
                'last' => $i == $total_results ? TRUE : FALSE
              );
              
              $url_list[] = '<a href="'.$url.'">'.$v.'</a>'; 
            
            } elseif ( ! is_array($v)) {
              
              $list[] = array(
                'name'  => $v,
                'count' => $i,
                'url'   => $url,
                'total_results' => $total_results,
                'first' => $i == 1 ? TRUE : FALSE,
                'last' => $i == $total_results ? TRUE : FALSE
              );
            }

            $i++;

          }

          if ($url) {
            $meta[$key.'_url_list'] = implode(', ', $url_list);
          }
          if ( isset($meta[$key][0]) && ! is_array($meta[$key][0])) {
            $meta[$key.'_list'] = implode(', ', $meta[$key]);
            $meta[$key.'_option_list'] = implode('|', $meta[$key]);
            $meta[$key] = $list;
          }
        }
      }
    }  
    return $meta;
  }

  public static function get_content_list($folder=null,$limit=null,$offset=0,$future=false,$past=true,$sort_by='date',$sort_dir='desc',$conditions=null,$switch=null,$skip_status=false,$parse=true,$since=null,$until=null) {
    $app = \Slim\Slim::getInstance();

    $content_type = self::get_content_type();

    $folder_list = Statamic_Helper::explode_options($folder);

    $list = array();
    foreach ($folder_list as $list_item) {
      $results = self::get_content_all($list_item, $future, $past, $conditions, $skip_status, $parse, $since, $until);
      $list = $list+$results;
    }

    // default sort is by date
    if ($sort_by == 'date') {
      uasort($list, 'statamic_sort_by_datetime');
    } else if ($sort_by == 'title') {
      uasort($list, "statamic_sort_by_title");
    } else if ($sort_by == 'random') {
      shuffle($list);
    } else if ($sort_by == 'numeric' || $sort_by == 'number') {
      ksort($list);
    } else if ($sort_by != 'date') {
      # sort by any other field
      uasort($list, function($a, $b) use ($sort_by) {
        if (isset($a[$sort_by]) && isset($b[$sort_by])) {
          return strcmp($b[$sort_by], $a[$sort_by]);
        }
      });
    }

    // default sort is asc
    if ($sort_dir == 'desc') {
      $list = array_reverse($list);
    }

    // handle offset/limit
    if ($offset > 0) {
      $list = array_splice($list, $offset);
    }

    if ($limit) {
      $list = array_splice($list, 0, $limit);
    }

    if ($switch) {
      $switch_vars = explode('|',$switch);
      $switch_count = count($switch_vars);

      $count = 1;
      foreach ($list as $key => $post) {
        $list[$key]['switch'] = $switch_vars[($count -1) % $switch_count];
        $count++;
      }
    }

    return $list;
  }

  public static function fetch_content_by_url($path) {
      $data = null;
      $content_root = Statamic::get_content_root();
      $content_type = Statamic::get_content_type();

      if (file_exists("{$content_root}/{$path}.{$content_type}") || is_dir("{$content_root}/{$path}")) {
        // endpoint or folder exists!
      } else {
        $path = Statamic_Helper::resolve_path($path);
      }

      if (file_exists("{$content_root}/{$path}.{$content_type}")) {

        $page     = basename($path);
        $folder   = substr($path, 0, (-1*strlen($page))-1);

        $data = Statamic::get_content_meta($page, $folder);
      } else if (is_dir("{$content_root}/{$path}")) {
        $data = Statamic::get_content_meta("page", $path);
      }

      return $data;
  }

  public static function get_next_numeric($folder=null) {
    $next = '01';

    $list = self::get_content_all($folder, true, true, null, true);
    if (sizeof($list) > 0) {
      $item = array_pop($list);
      $current = $item['numeric'];
      if ($current <> '') {
        $next = $current + 1;
        $format= '%1$0'.strlen($current).'d';
        $next = sprintf($format, $next);
      }
    }

    return $next;
  }

  public static function get_next_numeric_folder($folder=null) {
    $next = '01';

    $list = self::get_content_tree($folder,1,1,true,false,true);
    if (sizeof($list) > 0) {
      $item = array_pop($list);
      if (isset($item['numeric'])) {
        $current = $item['numeric'];
        if ($current <> '') {
          $next = $current + 1;
          $format= '%1$0'.strlen($current).'d';
          $next = sprintf($format, $next);
        }
      }
    }

    return $next;
  }

  public static function get_content_count($folder=null,$future=false,$past=true,$conditions=null,$since=null,$until=null) {

    $folder_list = Statamic_Helper::explode_options($folder);

    $list = array();
    foreach ($folder_list as $list_item) {
      $results = self::get_content_all($list_item, $future, $past, $conditions, false, false, $since, $until);
      $list = $list+$results;
    }

    return sizeof($list);
  }

  public static function get_content_all($folder=null,$future=false,$past=true,$conditions=null,$skip_status=false,$parse=true,$since=null,$until=null) {
    $app = \Slim\Slim::getInstance();

    $content_type = self::get_content_type();
    $site_root = self::get_site_root();

    $absolute_folder = Statamic_Helper::resolve_path($folder);

    $posts = self::get_file_list($absolute_folder);
    $list = array();

    foreach ($posts as $key => $post) {
      // starts with numeric value
      unset($list[$key]);

      if ((preg_match(Statamic_Helper::$date_regex, $key) || preg_match(Statamic_Helper::$numeric_regex, $key)) && file_exists($post.".{$content_type}")) {

        $data = Statamic::get_content_meta($key, $absolute_folder, false, $parse);

        $list[$key] = $data;

        $list[$key]['slug']    = $site_root.'/'.$key;
        $list[$key]['url']     = $folder ? $site_root.$folder."/".$key : $site_root.$key;

        $list[$key]['raw_url'] = $list[$key]['url'];
        
        $date_entry = false;
        if (self::get_entry_timestamps() && Statamic_Helper::is_datetime_slug($key)) {
          $datestamp = Statamic_Helper::get_datestamp($key);
          $date_entry = true;

          # strip the date

          $list[$key]['slug'] = ltrim(preg_replace(Statamic_Helper::$datetime_regex, '', $key),'/');
          $list[$key]['url']  = preg_replace(Statamic_Helper::$datetime_regex, '', $list[$key]['url']); #override
          
          $list[$key]['datestamp'] = $data['datestamp'];
          $list[$key]['date'] = $data['date'];

        } else if (Statamic_Helper::is_date_slug($key)) {          
          $datestamp = Statamic_Helper::get_datestamp($key);
          $date_entry = true;

          # strip the date
          $list[$key]['slug'] = substr($key, 11);
          $list[$key]['slug'] = ltrim(preg_replace(Statamic_Helper::$date_regex, '', $key),'/');

          $list[$key]['url']  = preg_replace(Statamic_Helper::$date_regex, '', $list[$key]['url']); #override

          $list[$key]['datestamp'] = $data['datestamp'];
          $list[$key]['date'] = $data['date'];

        } else {
          $list[$key]['slug'] = ltrim(preg_replace(Statamic_Helper::$numeric_regex, '', $key),'/');
          $list[$key]['url']  = preg_replace(Statamic_Helper::$numeric_regex, '', $list[$key]['url'], 1); #override
        }

        $list[$key]['url'] = Statamic_Helper::reduce_double_slashes('/'.$list[$key]['url']);

        # fully qualified url
        $list[$key]['permalink'] = Statamic_Helper::reduce_double_slashes(Statamic::get_site_url().'/'.$list[$key]['url']);

        /* $content  = preg_replace('/<img(.*)src="(.*?)"(.*)\/?>/', '<img \/1 src="'.Statamic::get_asset_path(null).'/\2" /\3 />', $data['content']); */
        //$list[$key]['content'] = Statamic::transform_content($data['content']);

        if ( ! $skip_status) {
          if (isset($data['status']) && $data['status'] != 'live') {
            unset($list[$key]);
          }
        }

        // Remove future entries
        if ($date_entry && $future === FALSE && $datestamp > time()) {
          unset($list[$key]);
        }

        // Remove past entries
        if ($date_entry && $past === FALSE && $datestamp < time()) {
          unset($list[$key]);
        }

        // Remove entries before $since
        if ($date_entry && !is_null($since) && $datestamp < strtotime($since)) {
          unset($list[$key]);
        }

        // Remove entries after $until
        if ($date_entry && !is_null($until) && $datestamp > strtotime($until)) {
          unset($list[$key]);
        }

        if ($conditions) {
          $keepers = array();
          $conditions_array = explode(",", $conditions);
          foreach ($conditions_array as $condition) {
            $condition = trim($condition);
            $inclusive = true;

            list($condition_key, $condition_values) = explode(":", $condition);
            
            # yay php!
            $pos = strpos($condition_values, 'not ');
            if ($pos === FALSE) {
            } else { 
              if ($pos == 0) {
                $inclusive = false;
                $condition_values = substr($condition_values, 4);
              }
            }

            $condition_values = explode("|", $condition_values);

            foreach ($condition_values as $k => $condition_value) {
              $keep = false;
              if (isset($list[$key][$condition_key])) {
                if (is_array($list[$key][$condition_key])) {
                  foreach ($list[$key][$condition_key] as $key2 => $value2) {
                    #todo add regex driven taxonomy matching here

                    if ($inclusive) {

                      if (strtolower($value2['name']) == strtolower($condition_value)) {
                        $keepers[$key] = $key;
                        break;
                      }
                    } else {

                      if (strtolower($value2['name']) != strtolower($condition_value)) {
                        $keepers[$key] = $key;
                      } else {
                        // EXCLUDE!
                        unset($keepers[$key]);
                        break;
                      }
                    }
                  }
                } else {
                  if ($list[$key][$condition_key] == $condition_value)  {
                    if ($inclusive) {
                      $keepers[$key] = $key;
                    } else {
                      unset($keepers[$key]);
                    }

                 } else {
                    if ( ! $inclusive) {
                      $keepers[$key] = $key;
                    }
                  }
                }
              } else {
                $keep = false;
              }
            }
            if ( ! $keep && ! in_array($key, $keepers)) {
              unset($list[$key]);
            }
          }
        }
      }
    }

    return $list;
  }


  public static function get_content_tree($directory='/',$depth=1,$max_depth=5,$folders_only=false,$include_entries=false,$hide_hidden=true,$include_content=false,$site_root=false) {
    // $folders_only=true only page.md 
    // folders_only=false includes any numbered or non-numbered page (excluding anything with a fields.yaml file)
    // if include_entries is true then any numbered files are included
    $app = \Slim\Slim::getInstance();

    $content_root = self::get_content_root();
    $content_type = self::get_content_type();
    $site_root = $site_root ? $site_root : self::get_site_root();

    $current_url = Statamic_Helper::reduce_double_slashes($site_root.'/'.$app->request()->getResourceUri());

    $taxonomy_url = FALSE;
    if (self::is_taxonomy_url($current_url)) {
      list($taxonomy_type, $taxonomy_name) = self::get_taxonomy_criteria($current_url);
      $taxonomy_url = self::remove_taxonomy_from_path($current_url, $taxonomy_type, $taxonomy_name);
    }

    $directory = '/'.$directory.'/'; #ensure proper slashing

    $base = ''; 
    if ($directory <> '/') {
      $base = Statamic_Helper::reduce_double_slashes("{$content_root}/{$directory}");
    } else if ($directory == '/') {
      $base = "{$content_root}";
    } else {
      $base = "{$content_root}";
    }
    
    $files = glob("{$base}/*");


    $data = array();
    if ($files) {
      foreach($files as $path) {
        $current_name = basename($path);

        if (!Statamic_Helper::starts_with($current_name, '_') && !Statamic_Helper::ends_with($current_name, '.yaml')) {
          $node = array();
          $file = substr($path, strlen($base)+1, strlen($path)-strlen($base)-strlen($content_type)-2);

          if (is_dir($path)) {
            $folder = substr($path, strlen($base)+1);
            $node['type']     = 'folder';
            $node['slug']     = basename($folder);
            $node['title']    = ucwords(basename($folder));

            $node['numeric']  = Statamic_Helper::get_numeric($folder);

            $node['file_path'] = Statamic_Helper::reduce_double_slashes($site_root.'/'.$directory.'/'.$folder.'/page');
            
            if (Statamic_Helper::is_numeric_slug($folder)) {
              $pos = stripos($folder, ".");
              if ($pos !== false) {
                $node['raw_url']      = Statamic_Helper::reduce_double_slashes(
                                      Statamic_Helper::remove_numerics_from_path(
                                        $site_root.'/'.$directory.'/'.$folder
                                      )
                                    );
                $node['url'] = rtrim(preg_replace(Statamic_Helper::$numeric_regex, '', $node['raw_url']),'/');
                $node['title']    = ucwords(basename(substr($folder, $pos+1)));
              } else {
                $node['title']    = ucwords(basename($folder));
                $node['raw_url']      = Statamic_Helper::reduce_double_slashes($site_root.'/'.$directory.'/'.$folder);
                $node['url'] = rtrim(preg_replace(Statamic_Helper::$numeric_regex, '', $node['raw_url']),'/');
              }
            } else {
              $node['title']    = ucwords(basename($folder));
              $node['raw_url']      = Statamic_Helper::reduce_double_slashes($site_root.'/'.$directory.'/'.$folder);
              $node['url'] = rtrim(preg_replace(Statamic_Helper::$numeric_regex, '', $node['raw_url']), '/');
            }

            $node['depth']    = $depth;
            $node['children'] = $depth < $max_depth ? self::get_content_tree($directory.$folder.'/', $depth+1, $max_depth, $folders_only, $include_entries, $hide_hidden, $include_content, $site_root) : null;
            $node['is_current'] = $node['raw_url'] == $current_url || $node['url'] == $current_url ? TRUE : FALSE;
          
            $node['is_parent'] = FALSE;
            if ($node['url'] == Statamic_Helper::pop_last_segment($current_url) || ($taxonomy_url && $node['url'] == $taxonomy_url)) {
              $node['is_parent'] = TRUE;
            }

            $node['has_children'] = $node['children'] ? TRUE : FALSE;

            // has entries?
            if (file_exists(Statamic_Helper::reduce_double_slashes($path."/fields.yaml"))) {
              $node['has_entries'] = TRUE;
            } else {
              $node['has_entries'] = FALSE;
            }

            $meta = self::get_content_meta("page", Statamic_Helper::reduce_double_slashes($directory."/".$folder), false, true); 
            //$meta = self::get_content_meta("page", Statamic_Helper::reduce_double_slashes($directory."/".$folder)); 

            if (isset($meta['title'])) {
              $node['title'] = $meta['title'];
            }

            if (isset($meta['last_modified'])) {
              $node['last_modified'] = $meta['last_modified'];
            }

            if ($hide_hidden === true && (isset($meta['status']) && (($meta['status'] == 'hidden' || $meta['status'] == 'draft')))) {
              // placeholder condition
            } else {
              $data[] = $include_content ? array_merge($meta, $node) : $node;
              // print_r($data);
            }

          } else {
            if (Statamic_Helper::ends_with($path, $content_type)) {
              if ($folders_only == false) {
                if ($file == 'page' || $file == 'feed' || $file == '404') {
                  // $node['url'] = $directory;
                  // $node['title'] = basename($directory);

                  // $meta = self::get_content_meta('page', substr($directory, 1));
                  // $node['depth'] = $depth;
                } else {
                  $include = true;

                  // date based is never included
                  if (self::get_entry_timestamps() && Statamic_Helper::is_datetime_slug(basename($path))) {
                    $include = false;
                  } else if (Statamic_Helper::is_date_slug(basename($path))) {
                      $include = false;
                  } else if (Statamic_Helper::is_numeric_slug(basename($path))) {
                    if ($include_entries == false) {
                      if (file_exists(Statamic_Helper::reduce_double_slashes(dirname($path)."/fields.yaml"))) {
                        $include = false;
                      }
                    }
                  }

                  if ($include) {
                    $node['type'] = 'file';
                    $node['raw_url'] = Statamic_Helper::reduce_double_slashes($directory).basename($path);

                    $pretty_url = rtrim(preg_replace(Statamic_Helper::$numeric_regex, '', $node['raw_url']),'/');
                    $node['url'] = substr($pretty_url, 0, -1*(strlen($content_type)+1));
                    $node['is_current'] = $node['url'] == $current_url || $node['url'] == $current_url ? TRUE : FALSE;

                    $node['slug'] = substr(basename($path), 0, -1*(strlen($content_type)+1));

                    $meta = self::get_content_meta(substr(basename($path), 0, -1*(strlen($content_type)+1)), substr($directory, 1), false, true);

                    //$node['meta'] = $meta;

                    if (isset($meta['title'])) $node['title'] = $meta['title'];
                    $node['depth'] = $depth;
                    
                    if ($hide_hidden === true && (isset($meta['status']) && (($meta['status'] == 'hidden' || $meta['status'] == 'draft')))) {
                    } else {
                      $data[] = $include_content ? array_merge($meta, $node) : $node;
                    }
                  }
                }
              }
            }
          }
        }
      }
    }

    return $data;
  }

  public static function get_file_list($directory=null) {
    $content_root = self::get_content_root();
    $content_type = self::get_content_type();

    if ($directory) {
      $files = glob("{$content_root}{$directory}/*.{$content_type}");
    } else {
      $files = glob('{$content_root}*.{$content_type}');
    }
    $posts = array();

    if ($files) {
      foreach ($files as $file) {
        $len = strlen($content_type);
        $len = $len + 1;
        $len = $len * -1;

        $key = substr(basename($file), 0, $len);
        // Statamic_helper::reduce_double_slashes($key = '/'.$key);
        $posts[$key] = substr($file, 0, $len);
      }
    }
    return $posts;
  }

  public static function find_prev($current,$folder=null,$future=false,$past=true) {
    if ($folder == '') {
      $folder = '/';
    }

    $list = self::get_folder_list($folder, $future, $past);
    $keys = array_keys($list);
    $current_key = array_search($current, $keys);
    if ($current_key !== FALSE) {
      while (key($keys) !== $current_key) next($keys);
        return next($keys);
    }
    return FALSE;
  }

  public static function find_next($current,$folder=null,$future=false,$past=true) {
    if ($folder == '') {
      $folder = '/';
    }
    $list = self::get_folder_list($folder, $future, $past);
    $keys = array_keys($list);
    $current_key = array_search($current, $keys);
    if ($current_key !== FALSE) {
      while (key($keys) !== $current_key) next($keys);
        return prev($keys); 
    }

    return FALSE;
  }

  public static function get_asset_path($asset) {
    $content_root = self::get_content_root();
    $app = \Slim\Slim::getInstance();
    return "{$content_root}".$app->request()->getResourceUri().''.$asset;
  }

  public static function parse_content($template_data, $data) {

    $app = \Slim\Slim::getInstance();

    $data = array_merge($data, $app->config);

    $parser = new Lex_Parser();
    $parser->cumulative_noparse(true);
    $parser->scope_glue(':');

    $parse_order = Statamic::get_parse_order();
    
    if ($parse_order[0] == 'tags') {
      $output = $parser->parse($template_data, $data);
      $output = self::transform_content($output);
    } else {
      $output = self::transform_content($template_data);
      $output = $parser->parse($output, $data);
    }

    return $output;
  }

  public static function yamlize_content($meta_raw, $content_key = 'content') {

    if (Statamic_Helper::ends_with($meta_raw, "---")) {
      $meta_raw .= "\n"; # prevent parse failure
    }
    # Parse YAML Front Matter
    if (stripos($meta_raw, "---") === FALSE) {
      $meta = Spyc::YAMLLoad($meta_raw);
      $meta['content'] = "";
    } else {

      list($yaml, $content) = preg_split("/---/", $meta_raw, 2, PREG_SPLIT_NO_EMPTY);
      $meta = Spyc::YAMLLoad($yaml);
      $meta[$content_key.'_raw'] = trim($content);
      $meta[$content_key] = trim(Statamic::transform_content($content));

      return $meta;
    }
  }

  public static function transform_content($content) {
    $content_type = self::get_content_type();

    if ($content_type == "markdown" || $content_type == 'md') {
      $content = Markdown($content);

    } else if ($content_type == 'html') {
      // no modifications

    } else if ($content_type == 'txt') {
      $content = nl2br(strip_tags($content));

    } else if ($content_type == 'textile') {
      $textile = new Textile();
      $content = $textile->TextileThis($content);
    }

    if (Statamic::get_setting('_enable_smartypants', true) == true) {
      $content = SmartyPants($content);
    }

    return $content;
  }

  public static function is_taxonomy($tax) {
    $app = \Slim\Slim::getInstance();
    
    if (isset($app->config['_taxonomy'])) {
      $taxonomies = $app->config['_taxonomy'];
      if (in_array($tax, $taxonomies)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  public static function is_taxonomy_url($path) {
    $app = \Slim\Slim::getInstance();

    if (isset($app->config['_taxonomy'])) {
      $taxonomies = $app->config['_taxonomy'];

      $items = explode("/", $path); # get the last 2 segments of the path, format: /<taxonomy_type>/<slug>
      if (sizeof($items) > 2) {
        $slug = array_pop($items);
        $type = array_pop($items);

        foreach ($taxonomies as $key => $taxonomy) {
          if ($type == $taxonomy) {
            return TRUE;
          }
        }
      }
    }

    return FALSE;
  }

  public static function get_taxonomy_criteria($path) {
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_taxonomy'])) {
      $taxonomies = $app->config['_taxonomy'];
      // get the last 2 segments of the path
      // format: /<taxonomy_type>/<slug>
      $items = explode("/", $path);
      if (sizeof($items) > 2) {
        $slug = array_pop($items);
        $type = array_pop($items);

        foreach ($taxonomies as $key => $taxonomy) {

          if ($type == $taxonomy) {
            return array($type, $slug);
          }
        }
      }
    }

    return FALSE;
  }

  public static function remove_taxonomy_from_path($path, $type, $slug) {
    $return = $path;

    $return = substr($path, 0, -1 * strlen("/{$type}/{$slug}"));

    return $return;
  }

  public static function detect_environment(array $environments, $uri) {
    foreach ($environments as $environment => $patterns) {
      foreach ($patterns as $pattern) {
        if (Statamic_Helper::is($pattern, $uri)) {
          return $environment;
        }
      }
    }
  }
}

function statamic_sort_by_title($a, $b) {
  return strcmp($a['title'], $b['title']);
}

function statamic_sorty_by_field($field, $a, $b) {
  if (isset($a[$field]) && isset($b[$field])) {
    return strcmp($a[$field], $b[$field]);  
  } else {
    return strcmp($a['title'], $b['title']);
  }
}

function statamic_sort_by_datetime($a, $b) {
  if (isset($a['datetimestamp']) && isset($b['datetimestamp'])) {
    return strcmp($a['datetimestamp'], $b['datetimestamp']);  
  } else if (isset($a['datestamp']) && isset($b['datestamp'])) {
    return strcmp($a['datestamp'], $b['datestamp']);
  }
}
