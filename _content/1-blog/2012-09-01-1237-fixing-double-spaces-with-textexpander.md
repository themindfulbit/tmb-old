---
title: 'Fixing double-spaces with TextExpander'
categories: [technology]
tags: [textexpander, typography, fanaticism]
---
Hi, I’m Erik and I have a problem.

For 25 years, I’ve put two spaces after every period. 

I know, I know. In the era of proportional typesetting this is considered to be [deeply][1], [deeply][2] [wrong][3] for any published work. What can I say? I learned to type on an ancient device known as a _typewriter_.

   [1]: http://blog.apastyle.org/apastyle/2009/07/on-two-spaces-following-a-period.html
   [2]: http://www.chicagomanualofstyle.org/CMS_FAQ/OneSpaceorTwo/OneSpaceorTwo03.html
   [3]: http://www.slate.com/articles/technology/technology/2011/01/space_invaders.html

Luckily, we’re living in a high-tech age. These old limits have been transcended. We’ve got fancy proportional fonts and typewriters are [almost][4] [completely][5] a thing of the past. 

   [4]: http://www.usbtypewriter.com/
   [5]: http://dvice.com/archives/2012/08/typewriter-ipad.php

Heck, in this day and age I don’t even have to relearn my old typing habits. That’s what [TextExpander](http://www.smilesoftware.com/TextExpander/index.html) is for.

At first glance, it seems simple to have TextExpander swap out a “period, space, space” sequence for just a “period, space”. As I discovered though, TextExpander treats a space as a delimiter by default, which means the double space isn’t recognized as such.

No worries, there’s a workaround. You need to create a new group, and that lets you set custom (or in this case no) delimiter character. Name your group something clever and in the “Expand after” drop-down, select “Any character”. Now any snippet in that group will fire immediately once it’s typed.

{{ theme:partial src="image" title="" file="whitespace.png" }}

Create a new snippet in your group with “Content” set to “period, space” and “Abbreviation” set to “period, space, space”. That’s it.

Ahh, technology. Is there anything you _can’t_ do?