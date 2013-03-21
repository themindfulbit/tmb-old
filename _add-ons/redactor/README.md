Redactor Fieldtype v1.2
=========================
*Last Updated November 13th, 2012*

## Requirements
- Redactor 1.2 requires Statamic 1.3.

The Redactor fieldtype is a lighting fast, retina-ready WYSIWYG editor fieldtype. It's lightweight, customizable, and powerful.

## Installing
1. Drag the add-ons/redactor folder into the `/_add-ons/` folder.
2. Drag the _config/add-ons/redactor.yaml file into the `/_config/add-ons/` folder.
3. Enjoy.

## Customizing

The `redactor.yaml` config file lets you set any of the available [Redactor settings](http://redactorjs.com/docs/settings/) to create your default configation. Most notable are the `buttons`, `colors`, and `wym` options.

## Image Browing

When configuring your fieldset, you can set the `image_dir` setting to tell Redactor where to look for images, allowing you to browse and drop in images from any folder in your site.

Example:

    content:
      display: Content
      type: redactor
      image_dir: assets/img/