/*!
 * jQuery insertAtCaret 1.1.4
 * http://www.karalamalar.net/
 *
 * Copyright (c) 2013 İzzet Emre Erkan
 * Licensed under GPLv2 or later.
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Contributors:
 * [@kittsville](https://github.com/kittsville)
 *
 */
!function(e,t){e.fn.insertAtCaret=function(e){return this.each(function(){var a,n,r,o,c=this,l=0,s="selectionStart"in c&&"selectionEnd"in c;(c.tagName&&"textarea"===c.tagName.toLowerCase()||c.tagName&&"input"===c.tagName.toLowerCase()&&"text"===c.type.toLowerCase())&&(a=c.scrollTop,s?l=c.selectionStart:(c.focus(),o=t.selection.createRange(),o.moveStart("character",-c.value.length),l=o.text.length),n=c.value.substring(0,l),r=c.value.substring(l,c.value.length),c.value=n+e+r,l+=e.length,s?(c.selectionStart=l,c.selectionEnd=l):(o=t.selection.createRange(),o.moveStart("character",l),o.moveEnd("character",0),o.select()),c.scrollTop=a)})}}(jQuery,document,window);