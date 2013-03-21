<?php
class Plugin_lorem extends Plugin {

  var $meta = array(
    'name'       => 'Lorem Ipsum Generator',
    'version'    => '0.9',
    'author'     => 'Jack McDade',
    'author_url' => 'http://jackmcdade.com'
  );

  public function index()
  {
    $params = array(
      'paragraphs'   => $this->fetch_param('paragraphs', '3', 'is_numeric'),
      'length'       => $this->fetch_param('length', 'short'),
      'decorate'     => $this->fetch_param('decorate', false),
      'links'        => $this->fetch_param('links', false),
      'ul'           => $this->fetch_param('ul', false),
      'ol'           => $this->fetch_param('ol', false),
      'dl'           => $this->fetch_param('dl', false),
      'bq'           => $this->fetch_param('bq', false),
      'code'         => $this->fetch_param('code', false),
      'headers'      => $this->fetch_param('headers', false),
      'allcaps'      => $this->fetch_param('allcaps', false)
    );

    $request_url = 'http://loripsum.net/api';
   
    foreach ($params as $key => $value)
    {
      if ($key == 'paragraphs' || $key == 'length')
        $request_url .= '/'.$value;
      elseif ($value == "yes")
        $request_url .= '/'.$key;
    }
    
    return file_get_contents($request_url);
  }
}