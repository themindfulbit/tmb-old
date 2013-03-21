<?php

/**
 * The Routes
 **/

function authenticateForRole($role = 'member') {
    $admin_app = \Slim\Slim::getInstance();
    $user = Statamic_Auth::get_current_user();
    if ($user) {
      if ($user->has_role($role) === false) {
        $admin_app->redirect($admin_app->urlFor('denied'));
      }
    } else {
      $admin_app->redirect($admin_app->urlFor('login'));
    } 
    return true;
} 

function isCurlEnabled() {
  return function_exists('curl_version') ? true : false;
}

function doStatamicVersionCheck($app) {
  // default values
  $app->config['latest_version_url'] = '';
  $app->config['latest_version'] = '';

  if (isCurlEnabled()) {
    $cookie = $app->getEncryptedCookie('stat_latest_version');
    if (!$cookie) {
      $license = Statamic::get_license_key();
      $site_url = Statamic::get_site_url();
      $parts = parse_url($site_url);
      $domain = isset($parts['host']) ? $parts['host'] : '/';

      $url = "http://outpost.statamic.com/check?v=".urlencode(STATAMIC_VERSION)."&l=".urlencode($license)."&d=".urlencode($domain);
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_URL, $url); 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, '3');
      $content = trim(curl_exec($ch));
      curl_close($ch);

      if ($content <> '') {
        $response = json_decode($content);
        if ($response && $response->status == 'ok') {
          $app->setEncryptedCookie('stat_latest_version', $response->current_version);
          $app->setEncryptedCookie('stat_latest_version_url', $response->url);
          $app->config['latest_version_url'] = $response->current_version;
          $app->config['latest_version'] = $response->current_version;
        } else {
          $app->config['latest_version_url'] = '';
          $app->config['latest_version'] = '';
        }
      }
    } else {
      $app->config['latest_version'] = $cookie;
      $app->config['latest_version_url'] = $app->getEncryptedCookie('stat_latest_version_url');
    }
  }
}


/////////////////////////////////////////////////////////////////////////////////////////////////
// ROUTES
/////////////////////////////////////////////////////////////////////////////////////////////////


$admin_app->get('/',  function() use ($admin_app) {
  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);

  if ( ! CP_Helper::show_page('dashboard')) {
    $redirect_to = Statamic::get_setting('_admin_start_page', 'pages');
    $admin_app->redirect($_SERVER['SCRIPT_NAME'].'/'.$redirect_to);
  }

  $template_list = array("dashboard");
  Statamic_View::set_templates(array_reverse($template_list));
  $admin_app->render(null, array('route' => 'root', 'app' => $admin_app));
})->name('home');


// AUTH RELATED FUNCTION
// --------------------------------------------------------
$admin_app->get('/denied', function() use ($admin_app) {
  $template_list = array("denied");
  Statamic_View::set_templates(array_reverse($template_list));
  Statamic_View::set_layout("layouts/login");
  $admin_app->render(null, array('route' => 'login', 'app' => $admin_app));
})->name('denied');



$admin_app->get('/login', function() use ($admin_app) {
  $template_list = array("login");
  Statamic_View::set_templates(array_reverse($template_list));
  Statamic_View::set_layout("layouts/login");
  $admin_app->render(null, array('route' => 'login', 'app' => $admin_app));
})->name('login');



$admin_app->get('/logout', function() use ($admin_app) {
  Statamic_Auth::logout();
  $admin_app->redirect($admin_app->urlFor('home'));
})->name('logout');



$admin_app->post('/login', function() use ($admin_app) {
  $app = \Slim\Slim::getInstance();

  $login = $app->request()->post('login');
  $username = $login['username'];
  $password = $login['password'];

  $errors = array();
  // Auth login
  // if success direct to admin homepage
  if (Statamic_Auth::login($username, $password)) {

    $user = Statamic_Auth::get_user($username);
    
    if ( ! $user->is_password_encrypted()) {
      $user->set_password($user->get_password(), true);
      $user->save();
      $errors = array('login' => 'Password has been encrypted. Please login again.');
    } else {
       $app->redirect($app->urlFor('home'));
    }

  } else {
    $errors = array('login' => 'Incorrect username or password. Try again.');
  }
  // else show login failed page
    // add error message

  $template_list = array("login");
  Statamic_View::set_templates(array_reverse($template_list));
  Statamic_View::set_layout("layouts/login");
  $admin_app->render(null, array('route' => 'login', 'app' => $admin_app, 'errors' => $errors));
})->name('login-submit');



// ERROR FUNCTION
// --------------------------------------------------------
$admin_app->get('/error', function() use ($admin_app) {
  $template_list = array("error");
  Statamic_View::set_templates(array_reverse($template_list));
  Statamic_View::set_layout("layouts/default");
  $admin_app->render(null, array('route' => 'login', 'app' => $admin_app));
})->name('error');



// PUBLICATION
// --------------------------------------------------------
$admin_app->get('/pages', function() use ($admin_app) {
  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);
  $template_list = array("pages");

  if ( ! Statamic::is_content_writable()) {
    $admin_app->flash('error', 'Content folder not writable');
    $url = $admin_app->urlFor('error')."?code=write_permission";
    $admin_app->redirect($url);
    return;
  }

  $path = "";
  $path = $admin_app->request()->get('path');
  $errors = array();
  $pages = Statamic::get_content_tree('/', 1, 1000, false, false, false, false, '/');

  #######################################################################
  # Fieldsets
  #######################################################################

  $fieldsets = Statamic_Fieldset::get_list();

  foreach ($fieldsets as $key => $fieldset) {
    
    # Remove hidden fieldsets
    if (isset($fieldset['hide']) && $fieldset['hide'] === true) {
      unset($fieldsets[$key]);
    } else if ( ! isset($fieldset['title'])) {
      # set a optional name
      $fieldsets[$key]['title'] = Statamic_Helper::prettify($key);
    }

  }

  # Sort by title
  uasort($fieldsets, function($a, $b) {
    return strcmp($a['title'], $b['title']);
  });

  #######################################################################

  $node['type'] = 'home';
  $node['url'] = "/page";
  $node['slug'] = "/";

  $meta = Statamic::get_content_meta("page", "");

  //$node['meta'] = $meta;

  if (isset($meta['title'])) {
    $node['title'] = $meta['title'];
  }
  if (file_exists(Statamic_helper::reduce_double_slashes(Statamic::get_content_root()."/fields.yaml"))) {
    $node['has_entries'] = TRUE;
  }
  $node['depth'] = 1;
  
  array_unshift($pages, $node);

  Statamic_View::set_templates(array_reverse($template_list));
  $admin_app->render(null, array('route' => 'pages', 'app' => $admin_app
    , 'errors' => $errors
    , 'path' => $path
    , 'pages' => $pages
    , 'fieldsets' => $fieldsets
    , 'are_fieldsets' => count($fieldsets) > 0 ? true : false
    ));
})->name('pages');



$admin_app->get('/entries', function() use ($admin_app) {
  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);
  $content_root = Statamic::get_content_root();
  $template_list = array("entries");

  $path = "";
  $path = $admin_app->request()->get('path');
  $errors = array();

  $path = $admin_app->request()->get('path');
  if ($path) {
    $entry_type = Statamic::get_entry_type($path);
    
    $order = $entry_type == 'date' ? 'desc' : 'asc';

    $entries = Statamic::get_content_list($path,null,0,true,true,$entry_type, $order, null, null, true);
    Statamic_View::set_templates(array_reverse($template_list));

    $admin_app->render(null, array(
       'route'   => 'entries',
       'app'     => $admin_app,
       'errors'  => $errors,
       'path'    => $path,
       'entries' => $entries,
       'type'    => $entry_type
      ));
  }
})->name('entries');


// LOGIC
// - VALIDATE
// - SAVE TO ORIGINAL FILENAME
// - IF NECESSARY: RENAME



// POST: PUBLISH
$admin_app->post('/publish', function() use ($admin_app) {
  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);

  $content_root = Statamic::get_content_root();
  $content_type = Statamic::get_content_type();

  $app = \Slim\Slim::getInstance();
  $path = $app->request()->get('path');

  if ($path) {
    $index_file = false;
    $form_data = $app->request()->post('page');

    // 1. Validate
    if ($form_data) {
      // ### Intercept the timestamp and convert to something we can work with
      if (isset($form_data['meta']['publish-time'])) {
        $_ts = $form_data['meta']['publish-time'];
        $ts = strtotime($_ts);
        $form_data['meta']['publish-time'] = date("Hi", $ts);
      }

      if ($form_data['type'] == 'none') {
        $index_file = true;
      }

      if (Statamic_Helper::ends_with($path, 'page')) {
        $index_file = true;
      }

      $errors = array();

      if ( ! $form_data['yaml']['title'] || $form_data['yaml']['title'] == '') {
        $errors['title'] = 'is required';
      }

      if ($index_file) {
        // some different validation rules
        $slug = $form_data['meta']['slug'];
        if ($slug == '') {
          $errors['slug'] = 'is required';
        } else {
          if ($slug != $form_data['original_slug']) {
            if ($form_data['type'] == 'none') {
              $file = $check_file = $content_root."/".$path."/".$slug."/page.".$content_type;
              $folders = Statamic::get_content_tree($path,1,1,false,false,true);
              if (Statamic_Validate::folder_slug_exists($folders, $slug)) {
                $errors['slug'] = 'already exists';
              }
            } else {
              $file = $content_root."/".dirname($path)."/page.".$content_type;
              $check_file = str_replace($form_data['original_slug'], $slug, $file);
              if (file_exists($check_file)) {
                $errors['slug'] = 'already exists';
              }
            }

          }
        }
      } elseif (isset($form_data['type']) && $form_data ['type'] == 'none') {
        $slug = $form_data['meta']['slug'];
        $file = $content_root."/".$path."/".$slug.".".$content_type;
        if (file_exists($file)) {
          $errors['slug'] = 'already exists';
        }
      } else {
        if (isset($form_data['new'])) {
          $entries = Statamic::get_content_list($path,null,0,true,true);
        } else {
          $entries = Statamic::get_content_list(dirname($path),null,0,true,true);
        }

        $slug = $form_data['meta']['slug'];
        if ($slug == '') {
          $errors['slug'] = 'is required';
        } else {
          // do we have this slug already?
          if (isset($form_data['new']) || $slug != $form_data['original_slug']) {
            if (Statamic_Validate::content_slug_exists($entries, $slug)) {
              $errors['slug'] = 'already exists';
            }
          }
        }

        // generate slug & datestamp/number
        $datestamp = '';
        $timestamp = '';
        $numeric = '';
        if ($form_data['type'] == 'date') {
          // STANDARDIZE INPUT
          $datestamp = $form_data['meta']['publish-date'];
          if ($datestamp == '') {
            $errors['datestamp'] = 'is required';
          }

          if (Statamic::get_entry_timestamps()) {
            $timestamp = $form_data['meta']['publish-time'];
            if ($timestamp == '') {
              $errors['timestamp'] = 'is required';
            }
          }
        } else if ($form_data['type'] == 'number') {
          $numeric = $form_data['meta']['publish-numeric'];
          if ($numeric == '') {
            $errors['numeric'] = 'is required';
          }
        }
      }

      if (sizeof($errors) > 0) {
        // REPOPULATE IF THERE IS AN ERROR
        if (isset($form_data['new'])) {
          $data['new'] = $form_data['new'];
        }
        $data['path'] = $path;
        $data['page'] = '';
        $data['title'] = $form_data['yaml']['title'];
        $folder = $form_data['folder'];
        $data['folder'] = $form_data['folder'];
        $data['content'] = $form_data['content'];
        $data['content_raw'] = $form_data['content'];
        $data['type'] = $form_data['type'];
        $data['errors'] = $errors;

        $data['slug'] = $form_data['meta']['slug' ];

        $data['full_slug'] = $form_data['full_slug'];
        $data['original_slug'] = $form_data['original_slug'];
        $data['original_datestamp'] = $form_data['original_datestamp'];
        $data['original_timestamp'] = $form_data['original_timestamp'];
        $data['original_numeric'] = $form_data['original_numeric'];

        if (isset($form_data['fieldset'])) {
          $data['fieldset'] = $form_data['fieldset'];
        }

        if (!$index_file) {
          if (isset($form_data['type']) && $form_data ['type'] != 'none') {
            $data['datestamp'] = strtotime($datestamp);
            $data['timestamp'] = strtotime($datestamp." ".$timestamp);
            $data['numeric'] = $numeric;
          }
        }

        if (isset($form_data['yaml']['_template'])) {
          $data['_template'] = $form_data['yaml']['_template'];
        } else {
          $data['_template'] = '';
        }

        $data['templates'] = Statamic::get_templates_list();
        $data['layouts'] = Statamic::get_layouts_list();

        $fields_data = null;
        $content_root = Statamic::get_content_root();

        // fieldset
        if ($data['type'] == 'none') {
          // load field set

          if (isset($data['fieldset'])) {
            $fieldset = $data['fieldset'];
            $fs = Statamic_Fieldset::load($fieldset);
            $fields_data = $fs->get_data();
            $data['fields'] = isset($fields_data['fields']) ? $fields_data['fields'] : array();
            $data['fieldset'] = $fieldset;
          }
        } else if ($data['type'] != 'none' && file_exists("{$content_root}/{$folder}/fields.yaml")) {

          $fields_raw = file_get_contents("{$content_root}/{$folder}/fields.yaml");
          $fields_data = Spyc::YAMLLoad($fields_raw);
          if (isset($fields_data['_fieldset'])) {
            $fieldset = $fields_data['_fieldset'];
            $fs = Statamic_Fieldset::load($fieldset);
            $fields_data = $fs->get_data();
            $data['fields'] = isset($fields_data['fields']) ? $fields_data['fields'] : array();
            $data['fieldset'] = $fieldset;
          }
        }
        if ($fields_data && isset($fields_data['fields'])) { 
          $data['fields'] = $fields_data['fields'];
          // reload the fields data
          foreach ($data['fields'] as $key => $value) {
            if (isset($form_data['yaml'][$key])) {
              $data[$key] = $form_data['yaml'][$key];
            }
          }
        }

        $template_list = array("publish");
        Statamic_View::set_templates(array_reverse($template_list));
        $admin_app->render(null, array('route' => 'publish', 'app' => $admin_app)+$data);
        return;
      }
    } else {
      print "no form data";
    }
  } else {
    print "no form data";
  }

  // if we got here, have no errors
  // save to original file if not new
  if (isset($form_data['new'])) {
    if ($form_data['type'] == 'date') {
      if (Statamic::get_entry_timestamps()) {
        $file = $content_root."/".$path."/".$datestamp."-".$timestamp."-".$slug.".".$content_type;
      } else {
        $file = $content_root."/".$path."/".$datestamp."-".$slug.".".$content_type;
      }
    } else if ($form_data['type'] == 'number') {
      $file = $content_root."/".$path."/".$numeric.".".$slug.".".$content_type;
    } else if ($form_data['type'] == 'none') {
      $numeric = Statamic::get_next_numeric_folder($path);

      $file = $content_root."/".$path."/".$numeric."-".$slug."/page.".$content_type;
      $file = Statamic_Helper::reduce_double_slashes($file);
      if (!file_exists(dirname($file))) {
        mkdir(dirname($file), 0777, true);
      }
    } else {
      $file = $content_root."/".$path."/".$form_data['original_slug'].".".$content_type;
    }
    $folder = $path;
  } else {
    $folder = dirname($path);
    if ($form_data['type'] == 'date') {
      if (Statamic::get_entry_timestamps()) {

        if ($form_data['original_timestamp'] == '') {
          $file = $content_root."/".dirname($path)."/".$form_data['original_datestamp']."-".$form_data['original_slug'].".".$content_type;
        } else {
          $file = $content_root."/".dirname($path)."/".$form_data['original_datestamp']."-".$form_data['original_timestamp']."-".$form_data['original_slug'].".".$content_type;
        }

      } else {
        $file = $content_root."/".dirname($path)."/".$form_data['original_datestamp']."-".$form_data['original_slug'].".".$content_type;
      }
    } else if ($form_data['type'] == 'number') {
      // $file = $content_root."/".dirname($path)."/".$form_data['original_numeric'].".".$form_data['original_slug'].".".$content_type;
      $file = $content_root."/".$path.".".$content_type;
    } else {
      if ($index_file) {
        $file = $content_root."/".dirname($path)."/page.".$content_type;
      } else {
        $file = $content_root."/".dirname($path)."/".$form_data['original_slug'].".".$content_type;
      }
    }
  }

  // load the original yaml
  if (isset($form_data['new'])) {
    $file_data = array();
  } else {
    $page = basename($path);
    $folder = dirname($path);
    $file_data = Statamic::get_content_meta($page, $folder, true);
  }

  # Post-processing for Fieldtypes api


  if (isset($fields_data['fields'])) {
    
    # fields.yaml controls the fields
    $data['fields'] = $fields_data['fields'];
  } elseif (isset($fields_data['_fieldset'])) {
    
    # using a fieldset
    $fieldset = $fields_data['_fieldset'];
    $fs = Statamic_Fieldset::load($fieldset);
    $fieldset_data = $fs->get_data();
    $data['fields'] = $fieldset_data['fields'];
  } else {

    # not set.
    $data['fields'] = array();
  }

  
  $fieldset = null;
  if (file_exists("{$content_root}/{$folder}/fields.yaml")) { 
    $fields_raw = file_get_contents("{$content_root}/{$folder}/fields.yaml");
    $fields_data = Spyc::YAMLLoad($fields_raw);

    if (isset($fields_data['fields'])) {

      #fields.yaml
      $field_settings = $fields_data['fields'];
    } elseif (isset($fields_data['_fieldset'])) {

      # using a fieldset
      $fieldset = $fields_data['_fieldset'];
      $fs = Statamic_Fieldset::load($fieldset);
      $fieldset_data = $fs->get_data();
      $field_settings = $fieldset_data['fields'];
    } else {
      $field_settings = array();
    }
  } elseif (isset($form_data['type']) && $form_data['type'] == 'none') {
    if (isset($form_data['fieldset'])) {
      $fieldset = $form_data['fieldset'];

      $file_data['_fieldset'] = $fieldset;
      $fs = Statamic_Fieldset::load($fieldset);
      $fields_data = $fs->get_data();
      $field_settings = $fields_data['fields'];
    }
  }

  // check for empty checkbox fields
  // unchecked checkbox fields will not be included in the POST array due to
  // being unsuccessful, thus, we need to loop through all expected fields
  // looking for a checkbox type, and if it isn't in POST, set it to 0 manually
  foreach ($field_settings as $field => $settings) {
    if (isset($settings['type']) && $settings['type'] == 'checkbox' && !isset($form_data['yaml'][$field])) {
      $form_data['yaml'][$field] = 0;
    }
  }

  if (isset($_FILES['page'])) {
    foreach ($_FILES['page']['name']['yaml'] as $field => $value) {
      if (isset($field_settings[$field]['type'])) {
        if ($field_settings[$field]['type'] == 'file') {
          if ($value <> '') {
            $file_values = array();
            $file_values['name'] = $_FILES['page']['name']['yaml'][$field];
            $file_values['type'] = $_FILES['page']['type']['yaml'][$field];
            $file_values['tmp_name'] = $_FILES['page']['tmp_name']['yaml'][$field];
            $file_values['error'] = $_FILES['page']['error']['yaml'][$field];
            $file_values['size'] = $_FILES['page']['size']['yaml'][$field];
            $val = Fieldtype::process_field_data('file', $file_values, $field_settings[$field]);
            $file_data[$field] = $val;
            unset($form_data['yaml'][$field]);
          } else {
            if (isset($form_data['yaml'][$field.'_remove'])) {
              $form_data['yaml'][$field] = '';
              $file_data[$field] = '';
            } else {
              $file_data[$field] = isset($form_data['yaml'][$field]) ? $form_data['yaml'][$field] : '';
            }
          }
          // unset the remove column
          if (isset($form_data['yaml']["{$field}_remove"])) {
            unset($form_data['yaml']["{$field}_remove"]);
          }
        }
      }
    }
  }

  // foreach ($_FILES as $field => $value) {
  //   if (isset($form_data['yaml'][$field.'_remove'])) {
  //     $file_data[$field] = '';
  //   }

  //   if (isset($field_settings[$field]['type'])) {
  //     if ($field_settings[$field]['type'] == 'file') {
  //       $value = Fieldtype::process_field_data($field_settings[$field]['type'], $value, $field_settings[$field]);
  //       if ($value <> '') {
  //         $file_data[$field] = $value;
  //       }
  //     } 
  //   }
  // }

  foreach ($form_data['yaml'] as $field => $value) {
    if (isset($field_settings[$field]['type'])) {
      if ($field_settings[$field]['type'] != 'file') {
        $value = Fieldtype::process_field_data($field_settings[$field]['type'], $value, $field_settings[$field]);
      }
    }
    if (is_array($value)) {
      $file_data[$field] = $value;
    } else {
      $file_data[$field] = $value;
    }
  }
 
  unset($file_data['content']);
  unset($file_data['content_raw']);
  unset($file_data['last_modified']);

  $file_content = Statamic_Helper::build_file_content($file_data, $form_data['content']);

  file_put_contents($file, $file_content);

  // ### SEE IF WE NEED TO RENAME THE FILE
  if (isset($form_data['new'])) {
  } else {
    // rename the file
    $new_slug = $form_data['meta']['slug'];

    if ($form_data['type'] == 'date') {
      if (Statamic::get_entry_timestamps()) {
        $new_timestamp = $form_data['meta']['publish-time'];
        $new_datestamp = $form_data['meta']['publish-date'];
        $new_file = $content_root."/".dirname($path)."/".$new_datestamp."-".$new_timestamp."-".$new_slug.".".$content_type;
      } else {
        $new_datestamp = $form_data['meta']['publish-date'];
        $new_file = $content_root."/".dirname($path)."/".$new_datestamp."-".$new_slug.".".$content_type;
      }
    } else if ($form_data['type'] == 'number') {
      $new_numeric = $form_data['meta']['publish-numeric'];
      $new_file = $content_root."/".dirname($path)."/".$new_numeric.".".$new_slug.".".$content_type;
    } else {
      if ($index_file) {
        $new_file = str_replace($form_data['original_slug'], $new_slug, $file);
      } else {
        $new_file = $content_root."/".dirname($path)."/".$new_slug.".".$content_type;
      }
    }

    if ($file != $new_file) {
      if ($index_file) {
        rename(dirname($file), dirname($new_file));
      } else {
        rename($file, $new_file);
      }
    }
  }

  // rediect back to entries
  if ($form_data['type'] == 'none') {
    $app->flash('success', 'Page saved successfully!');
    $url = $app->urlFor('pages')."?path=".$folder;
    $app->redirect($url);
  } else {
    $app->flash('success', 'Entry saved successfully!');
    $url = $app->urlFor('entries')."?path=".$folder;
    $app->redirect($url);
  }

}); 

// GET: DELETE ENTRY
$admin_app->get('/deletentry', function() use ($admin_app) {
  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);
  $content_root = Statamic::get_content_root();
  $content_type = Statamic::get_content_type();

  $path = $admin_app->request()->get('path');
  $folder = dirname($path);
  $file = $content_root."/".$path.".".$content_type;
  if (file_exists($file)) {
    // rediect back to entries
    unlink($file);
    $admin_app->flash('success', 'Entry successfully deleted!');
    $url = $admin_app->urlFor('entries')."?path=".$folder;
    $admin_app->redirect($url);
  }
})->name('deleteentry');

// GET: PUBLISH
$admin_app->get('/publish', function() use ($admin_app) {
  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);
  $content_root = Statamic::get_content_root();
  $app = \Slim\Slim::getInstance();

  $data     = array();
  $path     = $app->request()->get('path');
  $new      = $app->request()->get('new');
  $fieldset = $app->request()->get('fieldset');
  $type     = $app->request()->get('type');

  if ($path) {

    if ($new) {
      $data['new'] = 'true';
      $page     = 'new-slug';
      $folder   = $path;
      $data['full_slug'] = dirname($path);
      $data['slug'] = '';
      $data['path'] = $path;
      $data['page'] = '';
      $data['title'] = '';
      $data['folder'] = $folder;
      $data['content'] = '';
      $data['content_raw'] = '';

      $data['datestamp'] = time();
      $data['timestamp'] = time();

      $data['original_slug'] = '';
      $data['original_datestamp'] = '';
      $data['original_timestamp'] = '';
      $data['original_numeric'] = '';

      if ($type == 'none') {
        $data['folder'] = $path;
        $data['full_slug'] = $path;
      }

    } else {
      $page   = basename($path);
      $folder = substr($path, 0, (-1*strlen($page))-1);

      if ( ! Statamic::content_exists($page, $folder)) {
        $app->flash('error', 'Content not found!');
        $url = $app->urlFor('pages');
        $app->redirect($url);
        return;
      }

      $data = Statamic::get_content_meta($page, $folder, true);

      $data['title'] = isset($data['title']) ? $data['title'] : '';
      $data['slug'] = basename($path);
      $data['full_slug'] = $folder."/".$page;
      $data['path'] = $path;
      $data['folder'] = $folder;
      $data['page'] = $page;
      $data['type'] = 'none';
      $data['original_slug'] = '';
      $data['original_datestamp'] = '';
      $data['original_timestamp'] = '';
      $data['original_numeric'] = '';
      $data['datestamp'] = 0;

      if ($page == 'page') {
        $page = basename($folder);
        if ($page == '') $page = '/';
        $folder = dirname($folder);
        $data['full_slug'] = $page;
      }
    }

    $content_root = $content_root;
    if ($data['slug'] != 'page' && file_exists("{$content_root}/{$folder}/fields.yaml")) { 
      
      $fields_raw = file_get_contents("{$content_root}/{$folder}/fields.yaml");
      $fields_data = Spyc::YAMLLoad($fields_raw);

      if (isset($fields_data['fields'])) {
        # fields.yaml controls the fields
        $data['fields'] = $fields_data['fields'];
      } elseif (isset($fields_data['_fieldset'])) {
        # using a fieldset
        $fieldset = $fields_data['_fieldset'];
        $fs = Statamic_Fieldset::load($fieldset);
        $fieldset_data = $fs->get_data();
        $data['fields'] = $fieldset_data['fields'];
      } else {
        # not set.
        $data['fields'] = array();
      }
      
      $data['type'] = isset($fields_data['type']) && ! is_array($fields_data['type']) ? $fields_data['type'] : $fields_data['type']['prefix'];

      if ($data['type'] == 'date') {
        if (Statamic::get_entry_timestamps() && Statamic_Helper::is_datetime_slug($page)) {
          $data['full_slug'] = $folder;
          $data['original_slug'] = substr($page, 16);
          $data['slug'] = substr($page, 16);
          $data['original_datestamp'] = substr($page, 0, 10);
          $data['original_timestamp'] = substr($page, 11, 4);
          if (!$new) {
            $data['datestamp'] = strtotime(substr($page, 0, 10));
            $data['timestamp'] = strtotime(substr($page, 0, 10) . " " . substr($page, 11, 4));

            $data['full_slug'] = $folder."/".$data['original_slug'];
          }
        } else {
          $data['full_slug'] = $folder;
          $data['original_slug'] = substr($page, 11);
          $data['slug'] = substr($page, 11);
          $data['original_datestamp'] = substr($page, 0, 10);
          $data['original_timestamp'] = "";
          if (!$new) {
            $data['datestamp'] = strtotime(substr($page, 0, 10));
            $data['full_slug'] = $folder."/".$data['original_slug'];
            $data['timestamp'] = "0000";
          }
        }
      } else if ($data['type'] == 'number') {
        if ($new) {
          $data['original_numeric'] = Statamic::get_next_numeric($folder);
          $data['numeric'] = Statamic::get_next_numeric($folder);
          $data['full_slug'] = $folder;
        } else {
          $numeric = Statamic_Helper::get_numeric($page);
          $data['slug'] = substr($page, strlen($numeric)+1);
          $data['original_slug'] = substr($page, strlen($numeric)+1);
          $data['numeric'] = $numeric;
          $data['original_numeric'] = $numeric;
          $data['full_slug'] = $folder."/".$data['original_slug'];
        }
      }
    } else {

      if ($new) {
        if ($fieldset) {
          $fs = Statamic_Fieldset::load($fieldset);
          $fields_data = $fs->get_data();
          $data['fields'] = isset($fields_data['fields']) ? $fields_data['fields'] : array();
          $data['type'] = 'none';
          $data['fieldset'] = $fieldset;
        }
      } else {
        if (isset($data['_fieldset'])) {
          $fs = Statamic_Fieldset::load($data['_fieldset']);
          $fields_data = $fs->get_data();
          $data['fields'] = isset($fields_data['fields']) ? $fields_data['fields'] : array();
          $data['fieldset'] = $data['_fieldset'];
        }
        $data['type'] = 'none';
      }

      $data['slug'] = $page;
      $data['original_slug'] = $page;
    }

  } else {
    print "NO PATH";
  }

  $data['templates'] = Statamic::get_templates_list();
  $data['layouts'] = Statamic::get_layouts_list();

  $template_list = array("publish");
  Statamic_View::set_templates(array_reverse($template_list));
  $admin_app->render(null, array('route' => 'publish', 'app' => $admin_app)+$data);
})->name('publish');


// MEMBERS
// --------------------------------------------------------
$admin_app->get('/members', function() use ($admin_app) {
  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);

  $members = Statamic_Auth::get_user_list();
  $data['members'] = $members;

  $template_list = array("members");
  Statamic_View::set_templates(array_reverse($template_list));
  $admin_app->render(null, array('route' => 'members', 'app' => $admin_app)+$data);
})->name('members');


// POST: MEMBER
// --------------------------------------------------------
$admin_app->post('/member', function() use ($admin_app) {
  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);

  $data = array();
  $name = $admin_app->request()->get('name');

  $form_data = $admin_app->request()->post('member');
  if ($form_data) {
    $errors = array();
    // VALIDATE
    if (isset($form_data['new'])) {
      $name = $form_data['name'];
      if ($name == '') {
        $errors['name'] = 'is required';
      } else {
        if (Statamic_Auth::user_exists($name)) {
          $errors['name'] = 'is already taken';
        }
      }
      if ((!isset($form_data['yaml']['password'])) || (!isset($form_data['yaml']['password']))) {
        $errors['password'] = 'and confirmation is required';
      } else {
        if ($form_data['yaml']['password'] == '') {
          $errors['password'] = 'must be at least 1 character';
        } else if ($form_data['yaml']['password'] != $form_data['yaml']['password_confirmation']) {
          $errors['password'] = 'and confirmation do not match';
        }
      }
    } else {
      if ($form_data['name'] <> $form_data['original_name']) {
        if (Statamic_Auth::user_exists($form_data['name'])) {
          $errors['name'] = 'is already taken';
        }
      }

      if (isset($form_data['yaml']['password'])) {
        if ((!isset($form_data['yaml']['password'])) || (!isset($form_data['yaml']['password']))) {
          $errors['password'] = 'and confirmation is required';
        } else {
          if ($form_data['yaml']['password'] <> '') {
            if ($form_data['yaml']['password'] != $form_data['yaml']['password_confirmation']) {
              $errors['password'] = 'and confirmation do not match';
            }
          }
        }
      }
    }

    if (sizeof($errors) > 0) {
      // repopulate and re-render
      $data['errors'] = $errors;

      $data['name'] = $form_data['name'];
      $data['first_name'] = $form_data['yaml']['first_name'];
      $data['last_name'] = $form_data['yaml']['last_name'];
      $data['roles'] = $form_data['yaml']['roles'];
      $data['biography'] =  $form_data['biography'];
      $data['original_name'] = $form_data['original_name'];

      $template_list = array("member");
      Statamic_View::set_templates(array_reverse($template_list));
      $admin_app->render(null, array('route' => 'publish', 'app' => $admin_app)+$data);
      return;
    }

    // IF NOT ERRORS SAVE
    if (isset($form_data['new'])) {
      $user = new Statamic_User(array());
      $user->set_name($name);
    } else {
      $user = Statamic_User::load($name);
    }

    $user->set_first_name($form_data['yaml']['first_name']);
    $user->set_last_name($form_data['yaml']['last_name']);
    if ( ! isset($form_data['yaml']['roles'])) {
      $form_data['yaml']['roles'] = '';
    }
    $user->set_roles($form_data['yaml']['roles']);
    $user->set_biography_raw($form_data['biography']);

    
    if (isset($form_data['yaml']['password']) && $form_data['yaml']['password'] <> '') {
      $user->set_password($form_data['yaml']['password'], true);
    }

    $user->save();

    // Rename?
    if (!isset($form_data['new'])) {
      $user->rename($form_data['name']);
    }

    // REDIRECT
    $admin_app->flash('success', 'Member successfully saved!');
    $url = $admin_app->urlFor('members');
    $admin_app->redirect($url);
  }
});


// GET: MEMBER
// --------------------------------------------------------
$admin_app->get('/member', function() use ($admin_app) {
  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);
  $data = array();

  if ( ! Statamic::are_users_writable()) {
    $admin_app->flash('error', 'The Users directory is not writable.');
    $url = $admin_app->urlFor('error')."?code=write_permission";
    $admin_app->redirect($url);
    return;
  }

  $name = $admin_app->request()->get('name');
  $new  = $admin_app->request()->get('new');

  if ($new) {
    $data['name']           = '';
    $data['new']            = 'true';
    $data['content_raw']    = '';
    $data['original_name']  = '';
    $data['first_name']     = '';
    $data['last_name']      = '';
    $data['roles']          = '';
    $data['biography']      = '';
    $data['is_password_encrypted'] = false;

  } else {
    $user = Statamic_Auth::get_user($name);

    if (!$user) {
      die("Error");
    }

    $data['name'] = $name;
    $data['first_name'] = $user->get_first_name();
    $data['last_name'] = $user->get_last_name();
    $data['roles'] = $user->get_roles_list();
    $data['is_password_encrypted'] = $user->is_password_encrypted();

    $data['biography'] =  $user->get_biography_raw();

    $data['original_name'] = $name;
  }

  $template_list = array("member");
  Statamic_View::set_templates(array_reverse($template_list));
  $admin_app->render(null, array('route' => 'members', 'app' => $admin_app)+$data);
})->name('member');


// GET: DELETE MEMBER
$admin_app->get('/deletemember', function() use ($admin_app) {
  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);

  $name = $admin_app->request()->get('name');
  if (Statamic_Auth::user_exists($name)) {
    $user = Statamic_Auth::get_user($name);
    $user->delete();
  }

  // REDIRECT
  $admin_app->flash('info', 'Member deleted');
  $url = $admin_app->urlFor('members');
  $admin_app->redirect($url);
})->name('deletemember');


// Account
// --------------------------------------------------------
$admin_app->get('/account', function() use ($admin_app) {
  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);

  $template_list = array("account");
  Statamic_View::set_templates(array_reverse($template_list));
  $admin_app->render(null, array('route' => 'members', 'app' => $admin_app));
})->name('account');


// System
// --------------------------------------------------------
$admin_app->get('/system', function() use ($admin_app) {
  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);

  $template_list = array("system");
  Statamic_View::set_templates(array_reverse($template_list));

  $data = array();

  if (isCurlEnabled()) {

    $user = Statamic_Auth::get_current_user();
    $username = $user->get_name();

    $tests = array(
      '_app'                                                => "Your application folder is accessable to the web. While not critical, it's best practice to protect this folder.",
      '_config'                                             => "Your config folder is accessable to the web. It is critical that you protect this folder.",
      '_config/settings.yaml'                               => "Your settings files are accessable to the web. It is critical that you protect this folder.",
      '_config/users/'.$username.'.yaml'                    => "Your member files are accessable to the web. It is critical that you protect this folder.",
      Statamic::get_setting('_content_root', '_content')    => "Your content folder is accessable to the web. While not critical, it is best practice to protect this folder.",
      Statamic::get_templates_path().'layouts/default.html' => "Your theme template files are accessable to the web. While not critical, it is best practice to protect this folder."
    );

    $site_url = 'http://'.$_SERVER['HTTP_HOST'].'/';

    foreach ($tests as $url => $message) {
      $test_url = $site_url.$url;

      $http = curl_init($test_url);
      curl_setopt($http, CURLOPT_RETURNTRANSFER, 1);
      $result = curl_exec($http);
      $http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);
      curl_close($http);

      $data['system_checks'][$url]['status_code'] = $http_status;
      $data['system_checks'][$url]['status'] = $http_status !== 200 ? 'good' : 'warning';
      $data['system_checks'][$url]['message'] = $message;
    }
  }

  $data['users'] = Statamic_Auth::get_user_list();

  $admin_app->render(null, array('route' => 'system', 'app' => $admin_app)+$data);
})->name('system');



// GET: IMAGES
// DEPRICATED in 1.3
// --------------------------------------------------------
$admin_app->get('/images',  function() use ($admin_app) {
  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);

  $path = $admin_app->request()->get('path');

  $image_list = glob($path."*.{jpg,jpeg,gif,png}", GLOB_BRACE);
  $images = array();

  if (count($image_list) > 0) {
    foreach ($image_list as $image) {
      $images[] = array(
        'thumb' => '/'.$image,
        'image' => '/'.$image
      );
    }
  }


  echo json_encode($images);
  
})->name('images');

// GET: 404
// --------------------------------------------------------
$admin_app->notFound(function() use ($admin_app) {

  authenticateForRole('admin');
  doStatamicVersionCheck($admin_app);

  $admin_app->flash('error', "That page did not exist, so we sent you here instead.");
  $redirect_to = Statamic::get_setting('_admin_start_page', 'pages');
  $admin_app->redirect($redirect_to);

});
