---
title: 'Switching to Squarespace'
categories: [design, technology]
tags: [mobile-first, compass, sass, 'responsive design', typekit, typography, takitapart, squarespace, 'basic maths']
---
Wordpress has been my blogging engine of choice for a long time. It's always been comfortable to use, custom template styling is straightforward and well documented, and the administrative interface gets better with every release. The open-source license and an extensive library of third-party themes, widgets and extensions can't hurt either.

Yet there's always room for change, even if only for its own sake. The beautiful admin interface and excellent iOS apps of [Squarespace][1] have intrigued me lately. Unlimited bandwidth in the [high-price tier][2] is helpful, since it's always nice to have something to show if a person (and 100,000 of his friends) decides to drop by your virtual doorstep.

   [1]: http://www.squarespace.com
   [2]: http://www.squarespace.com/pricing/

I'll also admit the challenge of tearing apart our digital LEGO fortress and rebuilding it with completely different pieces is tough to resist.

With that gauntlet cast down, we decided to reimplement our site on the Squarespace platform. It cost me about a month of work (with very few blog posts in the interim) but we're quite happy with the results.

In this longish post, I'll cover the basics of how we used [Typekit][3] and the [Takitapart CSS Framework][4] to reimplement our theme in an updated and responsive manner.

   [3]: http://typekit.com
   [4]: http://takitapart.net/framework/

## Sign-up and Content Import

Starting [March 30][5], Squarespace greatly simplified its pricing and plans. 

   [5]: http://blog.squarespace.com/blog/2012/3/30/simplifed-plans-and-pricing.html

You now have two options:

  * **Standard**: $8/mo (billed annually) for 2GB of storage, 20 pages and 500 GB of bandwidth, plus custom domain support
  * **Unlimited**: $16/mo (billed annually) for unlimited storage, pages and bandwidth, plus some other advanced options

Armed with the 10% discount code `turnsout` from [5by5's][6] latest episode of [Back to Work][7], I signed up for the Unlimited plan.

   [6]: 5by5.tv
   [7]: http://5by5.tv/b2w

{{ theme:partial src="image" title="Blog Importer" file="blog-importer.jpg" }}

Content import is straightforward using their Blog Importer tool, although some Wordpress formatting quirks (like randomly inserting extra `<p></p>` elements between otherwise normal paragraphs) required some editing. You'll probably find a few older posts still lurking in our site's back catalog that still need some cleanup. Let me know and I'll fix them.

## Template Decisions

Squarespace comes with several excellent built-in templates, but offers an unusually high degree of customization using its admin interface.

When our site moved from Tumblr to Wordpress last fall I purchased and tweaked [Khoi Vinh][9] and [Allan Cole's][10] excellent [Basic Maths][11] template, and we wanted to keep our existing look as much as possible.

   [9]: http://www.subtraction.com/
   [10]: http://fthrwght.com/
   [11]: http://basicmaths.subtraction.com/

Still, our design as it stood needed some work. While the original had a nice iPhone layout, my tweaks to Khoi's template broke the interface (badly) when viewing a page in an app's WebView rather than Safari. Since many people come to our links via RSS readers and Twitter clients, this wasn't going to be sustainable.

Also, we felt this was a great opportunity to try out responsive grid-based design. Bob was just finishing up development on his new [hybrid CSS framework][12] and this seemed like the perfect place to try it out. 

   [12]: http://takitapart.net/framework/

Aiming at a fluid, responsive layout for all screen sizes, we started designing.

## Custom Typography with Typekit

If you haven't considered [Typekit][13] for web typography yet, I highly recommend it. The service was recently purchased by Adobe, and despite [my initial][14] [skepticism][15] it's getting better all the time.

   [13]: https://typekit.com/
   [14]: http://themindfulbit.com/articles/2011/10/3/adobe-acquires-typekit.html
   [15]: http://themindfulbit.com/articles/2011/10/3/typekit-follow-up.html

We were using two Typekit fonts on the old site, Futura (sans-serif headers) and Goudy (serif body text). Unfortunately, we ran into a problem with Goudy, since the typeface doesn't include bold or italicized variants. Our body text is now Adobe Caslon, and so far it's working out great.

{{ theme:partial src="image" title="Typekit Editor" file="typekit-editor.jpg" }}

The Typekit editor interface walks you through adding the necessary header code to your site. For Squarespace users, you'll have to add the necessary code in the Admin interface. Choose "Extra Header Code" in:

Structure > Website Settings > Code Injection

Since Bob always says a picture is worth a thousand words, here's how it looks on our site, with the Typekit code highlighted:

{{ theme:partial src="image" title="Typekit Headers" file="typekit-headers.jpg" }}

## Responsive Site Design Using the Takitapart CSS Framework

The current version of Squarespace (5) offers no mobile-optimized themes. While this may change in the near future, for now the mindset seems to be that most mobile devices do a good enough job with standard-layout websites that it's not worth the trouble to go responsive.

We've been trying to go the other route at high90, designing for "mobile-first" whenever possible. This is likely to be the next holy-war in web design and we're too pragmatic to feel strongly either way. We figured it was worth giving responsive design a shot with The Mindful Bit and seeing if we could make it work on a non-optimized platform like Squarespace.

{{ theme:partial src="image" title="Takitapart CSS Framework" file="takitapart.jpg" }}

Bob has been designing the [Takitapart CSS Framework][20] for some time, mashing it up from a number of other grid-based frameworks and integrating [Compass][21] and [Sass][22] to give us something quick, responsive and flexible for our clients' websites. 

   [20]: http://takitapart.net/framework
   [21]: compass-style.org
   [22]: http://sass-lang.com/

I have to say, after using Compass and Sass I'm not sure I can ever happily return to bare CSS again. The detailed use of these two Ruby-based style aids are beyond this (already lengthy) article's scope, but I encourage you to visit the links above and check them out. I will only say that variables and nested selectors have made styling web pages a whole lot easier, and in the end it all compiles down to standards-compliant CSS.

The biggest challenge in using Compass and Sass with Squarespace is that you'll need to upload and link to a separate CSS file. Squarespace has a very robust internal CSS editor, but a project like this would have required repeatedly cutting and pasting generated CSS into a web-based text editor. This was beyond my patience (believe me, I tried).

Luckily, you can upload files to local storage, and the process is quick and easy enough that repeatedly uploading new .css and .js files wasn't a real bother.

You link to your new stylesheet the same way you added the Typekit code to the header. In my case, I just added:

`<link rel="stylesheet" type="text/css" href="/storage/screen.css" media="all"/>`

If you need an additional stylesheet to support older versions of Internet Explorer, you can use [conditional comments][23] to link to an additional stylesheet for tweaks:

   [23]: http://msdn.microsoft.com/en-us/library/ms537512(v=VS.85).aspx

`<!--[if lte IE 8]><link rel="stylesheet" type="text/css" href="/storage/ie.css" media="all"/><![endif]-->`

In our case, older versions of IE degrade to the mobile site. If I get the time, I'll try to make things more robust, but for now everything should work great for IE9 and up.

## Them's the breaks

A major decision for any responsive design is where (in terms of screen width) to break the layout for different screen sizes.

The Takitapart CSS Framework is em-based, and this site's implementation uses a ratio of 1em to 16px with a 12 column grid. This gives the following transition points:

#### Screens less than 480 pixels (30 em) wide

_This view works for most mobile phones_

{{ theme:partial src="image" title="400 Pixels" file="400px.jpg" }}

#### Screens from 480 to 592 pixels (30 to 37 em) wide

_This view allows for a second column of widgets at the bottom of the screen_

{{ theme:partial src="image" title="540 Pixels" file="540px.jpg" }}

#### Screens from 592 to 736 pixels (37 to 46 em) wide

_This view works for most 16 x 9 tablets (like the Nook and Kindle Fire) in portrait mode_

{{ theme:partial src="image" title="600 Pixels" file="600px.jpg" }}

#### Screens from 736 to 960 pixels (46 to 60 em) wide

_This view works for iPads in portrait mode_

{{ theme:partial src="image" title="768 Pixels" file="768px.jpg" }}

#### Screens from 960 to 1200 pixels (60 to 75 em) wide

_This view works for most tablets in landscape mode and gives the final column layout_

{{ theme:partial src="image" title="1024 Pixels" file="1024px.jpg" }}

#### Screens from 1200 to 1600 pixels (75 to 100 em) wide

_This view adds one column of margin to each side to keep the content from getting too wide_

{{ theme:partial src="image" title="1280 Pixels" file="1280px.jpg" }}

#### Screens greater than 1600 pixels (100 em) wide

_This view adds a second column of margin to each side to keep the content from getting too wide_

{{ theme:partial src="image" title="1920 Pixels" file="1920px.jpg" }}

## Things left to do

The site is still a little heavy, taking from one to two seconds to load on a good broadband connection. While most of the site's size comes from our Typekit fonts (237KB), they're some of the fastest resources to load (41ms).

There's plenty of optimization left to do, so load times should improve as I do further updates.

Eventually, I'd also like to give the full 1024px layout to users of earlier IE versions. This will just take a little bit of time and effort. Hopefully I can get these poor souls [a glass of ice water][35] soon.

   [35]: http://allthingsd.com/20111005/the-steve-jobs-i-knew/

If you have any suggestions, questions or comments, I'd love to hear them. Use the form below or get in touch with me via Twitter at [@themindfulbit][36].

   [36]: http://twitter.com/themindfulbit
