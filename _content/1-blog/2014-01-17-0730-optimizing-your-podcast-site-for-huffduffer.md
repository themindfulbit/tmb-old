---
title: 'Optimizing Your Podcast Site for Huffduffer'
categories: [design, technology]
tags: [podcasting, technical difficulties, huffduffer, statamic]
banner: u-boat.jpg
caption: '<a title="By Augusto Ferrer-Dalmau (Own work) [CC-BY-SA-3.0 (http://creativecommons.org/licenses/by-sa/3.0)], via Wikimedia Commons" href="http://commons.wikimedia.org/wiki/File%3AU-boot_by_Ferrer-Dalmau.jpg">U-Boot by Ferrer-Dalmau, 2002</a>'
---

We've got a [new episode](http://technicaldifficulties.us/episodes/063-intro-to-responsive-design) of [Technical Difficulties](http://technicaldifficulties.us) out today, and if you're interested in making your website more mobile-friendly, you might really enjoy it.

Listener [Nate Ferguson](http://twitter.com/NateTehGreat) had a helpful suggestion the other day:

<blockquote class="twitter-tweet" lang="en"><p><a href="https://twitter.com/techdiffpodcast">@techdiffpodcast</a> The copious show notes are great, but could you truncate them for Huffduffer the way 5by5 does?</p>&mdash; Nathan Ferguson (@NateTehGreat) <a href="https://twitter.com/NateTehGreat/statuses/423989051709214720">January 17, 2014</a></blockquote>
<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>

<blockquote class="twitter-tweet" lang="en"><p><a href="https://twitter.com/techdiffpodcast">@techdiffpodcast</a> I.e. full text RSS, but shortened description? To see what I mean: Huffduff an ep. of B2W, then an ep. of Tech Diff.</p>&mdash; Nathan Ferguson (@NateTehGreat) <a href="https://twitter.com/NateTehGreat/statuses/423989064182669312">January 17, 2014</a></blockquote>
<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>

I've never been a heavy user of [Huffduffer](http://huffduffer.com/), so [@potatowire](http://twitter.com/potatowire) has been handling [the podcast's account](http://huffduffer.com/techdiffpodcast) for us. Still, I think Huffduffer is really cool and it's popular with a lot of smart people like [Merlin Mann](http://huffduffer.com/merlinmann).

Being the curious type (and knowing I had to be the one to code up the solution anyway) I went digging.

There seem to be three ways to get something into Huffduffer. First, you can manually enter an audio file via [their website](http://huffduffer.com/add). You can also use their [Chrome extension](https://chrome.google.com/webstore/detail/fcgfcibjjipmckjohklncgaookceojkn), which puts an icon in your Chrome address bar when you visit a page with an audio file. Finally, you can use their handy bookmarklet. 

### Manual Entry

Manually entering the audio on their website is simple enough&mdash;the listener types the relevant information into the fields and hits submit.

{{ theme:partial src="image" title="Huffduffer Submissions Page" file="huffduff-submissions.png" }}

As a podcaster, there's not much we can do to improve that process besides make the download link, title, and summary easy to find on the episode page.

### The Chrome Extension

The Chrome Extension is a bit more challenging. It pulls information from the audio file download link, but by default it uses the link's text, which in our case is an unhelpful "DOWNLOAD".

{{ theme:partial src="image" title="Not helpful" file="download-title.png" }}

You can fix this by adding a `title` attribute to HTML anchor. Our link now looks like this:

{{ noparse }}
~~~
<a href="{{ download }}" title="Technical Difficulties {{ number }} - {{ title }}">Download</a>
~~~
{{ /noparse }}

Everything in the curly braces is for our CMS, so you'll need to use your own code there. When Huffduffed (is that the right verb?) it now puts out the name and number of the show, plus the episode title.

As near as I can tell, there's no way to automatically populate the description field on the Chrome extension. Thankfully, that's *not* the case when it comes to the Huffduffer bookmarklet. 

### The Bookmarklet

In [this post](https://getsatisfaction.com/huffduffer/topics/tagging_and_descriptions) on the Huffduffer support site, creator [Jeremy Keith](http://adactio.com/) crafted a great solution:

> I've just rolled out some code changes that I hope will help with pre-filling fields when huffduffing. The bookmarklet now tries the grab a description from the meta name="description" element, and tags from the meta name="keywords" element.
>
> Not all websites are providing this metadata, but when they are, it should help to make huffduffing a bit better.

Here's how you can implement these changes to your own podcast site. 

Within your site's HTML `<head>` tag, add the following two lines:

{{ noparse }}
~~~
  <meta name="description" content="{{ if summary }}{{ summary }}{{ endif }}">
  <meta name="keywords" content="{{ if topics }}{{ topics_list }}{{ endif }}">
~~~
{{ /noparse }}

Just like before, everything within the `content=""` attributes is specific to [our CMS](http://statamic.com), so use the appropriate code snippets for your site's templating engine. 

After implementing these small fixes, our site seems to be Huffduffing a lot better than before.

{{ theme:partial src="image" title="Now with more bookmarklet" file="hfdf-bookmarklet.png" }}

### Huffduff It!

I said there were three ways of getting things into Huffduffer, but there's actually one more step we can take to make things as easy as possible for your Huffduffing users. 

We're going to add a "Huffduff It" button *right on your episode page*. 

First, you'll need to add the following lines to the `<head>` of your HTML page, preferably near the bottom:

~~~
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>
  $(document).ready(function(){ 
    $("#huffduffer").click(function() {
      window.open('http://huffduffer.com/add?popup=true&page='+encodeURIComponent(location.href),'huffduff','scrollbars=1,status=0,resizable=1,location=0,toolbar=0,width=360,height=480');
    });
  });
</script>
~~~

Next you'll need to add a link with an `id="huffduffer"` attribute somewhere on each episode page. On our site, it looks something like this:

~~~
<a id="huffduffer">Huffduff It</a>
~~~

Essentially what we're doing is loading the [jQuery](http://jquery.com/) javascript library from Google (a step you can skip if you already load it), then tying the same script the bookmarklet uses to elements with an id of `huffduffer`. 

Now, when your visitors click on the link, it will pop up a box just like the one in the bookmarklet, whether they've got it installed or not. If your visitor isn't logged in, it will give them a login box in the same pop up.

{{ theme:partial src="image" title="Huffduff it!" file="hfdf-it.png" }}

If you have any questions or suggestions, you can find me on [Twitter](http://twitter.com/themindfulbit) or [App.net](http://app.net/themindfulbit). It's always nice hearing from smart people.
