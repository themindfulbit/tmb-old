---
title: 'Fixing the Slow File Open Dialog in Mavericks'
categories: [technology]
tags: [apple, os x, mavericks, terminal]
banner: beach-ball.jpg
caption:
---

Overall, I think OS X 10.9 Mavericks has been a positive step for the Mac, but there's one bug that's been there from the beginning, still isn't fixed, and has caused me quite a bit of frustration. 

The File Open/Save dialog is *incredibly slow*.

Luckily [Snaggletooth_DE](https://discussions.apple.com/people/Snaggletooth_DE) posted a workaround in an [Apple Support Communities thread](https://discussions.apple.com/thread/5495797?start=30&tstart=0) that offers a quick fix to this frustrating problem.

In the terminal type:

~~~
sudo vim /etc/auto_master
~~~

Follow that up with your password. For those who haven't used the command-line text editor [vim](http://www.vim.org/) before, you'll need to type `i` to start editing. Use your cursors to move to the line with:

~~~
/net
~~~

Comment that line out by putting `#` in front of it, so it reads:

~~~
#/net
~~~

Save your file by typing `Shift-Z Shift-Z` (that's capital Z twice in a row) then enter the following command:

~~~
sudo automount -vc
~~~

You should see something like this:

~~~
automount: /home updated
automount: /net unmounted
~~~

You're done. No reboot or logout required.

More importantly you shouldn't have to worry about that silly delay anymore. Maybe Apple will fix this bug in 10.9.2, but I'm not holding my breath.