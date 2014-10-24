---
title: 'Creating Link Posts with Statamic'
categories: [technology]
tags: [statamic, blogging]
---

I recently switched my blogging engine (yes, [*again*](http://themindfulbit.com/blog/ruhoh-i-rebuilt-the-blog-again)) to [Statamic](http://statamic.com). The hows and whys of that move deserve their own post, but at the [request](https://alpha.app.net/hcmarks/post/3998364) of an internet colleague I'm taking this opportunity to highlight one of Statamic's nicer features, a templating system that makes extending its functionality quick and easy. 

If you're new to Statamic, you may want to read a little more about it on their [beautiful website](http://statamic.com). Their [documentation section](http://statamic.com/docs) is comprehensive and easy to understand. 

Statamic 1.4.2 (the current shipping version) doesn't offer link posts out-of-the-box, but they're pretty straightforward to build. Like many static blogs, Statamic uses a templating engine ([their own](http://statamic.com/docs/template-language)) that serves as a [TextExpander](http://smilesoftware.com/TextExpander/index.html)-style shortcut to add snippets of functionality within your blog without the hassle of coding it all up in PHP.  

Raw posts are typically written in Markdown, prefaced with YAML [Content Fields](http://statamic.com/docs/custom-content-fields) that look something like this:

	---
	title: 'Creating Link Posts with Statamic'
	categories: [technology]
	tags: [statamic, blogging]
	---

While there are a number of system fields, you can easily create your own just by adding it to the end of the list. The text before the colon becomes a variable that's addressable elsewhere.

To make a normal post into a link post I start by simply adding `link:` and the appropriate URL to the YAML Content Fields:

	---
	title: 'Creating Link Posts with Statamic'
	categories: [technology]
	tags: [statamic, blogging]
	link: http://statamic.com
	---

Before this entry becomes a full link post, the engine needs to be told what to do with the information you've just given it. That requires putting template code in your theme anywhere your title appears. For my site, that's the home page, category page, tag page, single-post page, and RSS feed.

I'll use my home page as an example. Before I had link posts, the relevant post header section looked like this:

    {{ noparse }}<h2 class="title">
        <a href="{{ url }}">{{ title }}</a>
    </h2>{{ /noparse }}

In this case `{{ noparse }}{{ url }}{{ /noparse }}` is the built-in variable for the post's permalink, and `{{ noparse }}{{ title }}{{ /noparse }}` is the value of the `title` variable in the post's YAML content field.

Since Statamic's templating language allows you to use PHP-style conditionals, you can have it check for the presence of the `link` variable and use that value as an alternate URL in the anchor. That way there's no need to tweak your existing posts, you just add `link:` when you need it.

    {{ noparse }}<h2 class="title">
      {{ if link }}
        <a href="{{ link }}">{{ title }}&nbsp;&rArr;</a>
      {{ else }}
        <a href="{{ url }}">{{ title }}</a>
      {{ endif }}
    </h2> {{ /noparse }}

To cause the link posts to stand out a little better, I also added a non-breaking-space and the right double arrow (&rArr;) after the post's title. By putting it in the code there's no need to do any tweaking to the actual title value. 

You'll also want some sort of link back to your own post, in case somebody's looking for it. I added this snippet after my post's content area, with a crazy unicode glyph (&#8251;) for my ["trademark"](http://www.youtube.com/watch?v=Jjbu0kSEuQQ).

	{{ noparse }}{{ if link }}
        <div class="permalink"><a href="{{ url }}">&#8251;</a></div>
    {{ endif }}{{ /noparse }}

It's fairly straightforward to make these changes elsewhere in your theme, but fiddling with the XML of your RSS feed can be a little scary at times. Nevertheless the tweaks work the same way. 

A slice of my `feed.html` template before:

	{{ noparse }}<item>
         <title><![CDATA[{{ title }}]]></title>
         <author>{{ author }}</author>
         <link>
         	{{ permalink }}
         </link>
         <guid>
            {{ permalink }}
         </guid>
         <category>article</category>
         <pubDate>{{ datestamp format="r" }}</pubDate>
         <description><![CDATA[{{ content }}]]></description>
    </item>{{ /noparse }}

And after:

	{{ noparse }}<item>
         <title><![CDATA[{{ if link }}{{ title }} &rArr;{{ else }}{{ title }}{{ endif }}]]></title>
         <author>{{ author }}</author>
         <link>
            {{ if link }}
               {{ link }}
            {{ else }}
               {{ permalink }}
            {{ endif }}
         </link>
         <guid>
            {{ if link }}
               {{ link }}
            {{ else }}
               {{ permalink }}
            {{ endif }}
         </guid>
         <category>article</category>
         <pubDate>{{ datestamp format="r" }}</pubDate>
         <description><![CDATA[{{ content }}{{ if link }}<div class="permalink"><a href="{{ url }}">&#8251;</a></div>{{ endif }}]]></description>
    </item>{{ /noparse }}

Hopefully this has proven helpful to those starting out with Statamic. I've been really pleased with this hybrid blogging engine so far, and it's getting [better all the time](http://refinery.statamic.com/).

If you have any questions on this or anything else I've posted, drop me a line on [Twitter](http://twitter.com/themindfulbit) or [App.net](http://alpha.app.net/themindfulbit) as @themindfulbit.
