/*
FARSI TYPE
This script developed to ease typing Farsi(Persian) in web forms where there is no Farsi Keyboard installed on a PC.Works with Internet Explorer, Gecko family (Mozilla/FireFox) and  Opera.

Author : Kaveh Ahmadi
Author Contact : kaveh@ashoob.net
Version : 1.3.0

Copyright 2002-2007  Kaveh Ahmadi  (email : kaveh@ashoob.net)
Special Thanks to Gonahkar (gonahkar.com), Mani Monajjemi (manionline.org) and Mazdak Aghakhani (mazdakam.com) for their support and their great ideas!

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

> > > USAGE < < <

Step 0. Customize settings
Set ShowChangeLangButton, KeyBoardError, ChangeDir variables values with your own opinon!
	ShowChangeLangButton // Show Change Language Button --> 0: Show / 1: Visible
	KeyBoardError // the action when useres keyboard is farsi --> 0: Disable FarsiType / 1: Show Error
	ChangeDir // change the input or textarea direction when language is change --> 0: No Action / 1: Change direction / 2: set Rtl/Ltr button

Step 1. 
Include the "farsitype.js" file to your HTML <head> part (before </head>):
<script language="javascript" src="farsitype.js" type="text/javascript"></script>

Step 2.
Add "lang" attribute with value "fa" to any <input> or <textarea> tag which you want to have FarsiType enabled!
<input type="text" name="whatever" lang="fa" /> 
or 
<textarea cols="30" rows="7" name="whatever" lang="fa"></textarea>
[Magic : only the lang="fa" is important for FarsiType!]
[As ver 1.2.1 , you can also use lang="fa-IR"]

Step 3. (OPTIONAL)
You can also add an enable/disable checkbox beside your form. 
<input  type="checkbox" id="disableFarsiType" />

Note : You can use F8 keybord button to switch languages instead of using the change language button.

> > > CHANFGELOG < < <

V 1.3.0
- Optional controls is added for users

V 1.2.1
- Comments clean up
- GPL Licence added
- lang = 'fa-IR' support added

V 1.2
- Opera full support added

V 1.1
- Some little Gecko fixes

> > > EXTRA < < <
Mozilla solving problem idea is from: http://forum.persiantools.com/showthread.php?p=667351
Auto Creation idea is from: http://ip.webkar.com/forums/index.php?act=ST&f=19&t=99
with a look at behdad.org/editor and blogfa.com farsi solutions!
*/

// insertAdjacentHTML(), insertAdjacentText() and insertAdjacentElement() for Netscape 6/Mozilla by Thor Larholm me@jscript.dk
if(typeof HTMLElement!="undefined" && ! HTMLElement.prototype.insertAdjacentElement) {
	HTMLElement.prototype.insertAdjacentElement = function (where,parsedNode) {
		switch (where) {
			case 'beforeBegin':
				this.parentNode.insertBefore(parsedNode,this)
				break;
			case 'afterBegin':
				this.insertBefore(parsedNode,this.firstChild);
				break;
			case 'beforeEnd':
				this.appendChild(parsedNode);
				break;
			case 'afterEnd':
				if (this.nextSibling)
					this.parentNode.insertBefore(parsedNode,this.nextSibling);
				else
					this.parentNode.appendChild(parsedNode);
				break;
		}
	}

	HTMLElement.prototype.insertAdjacentHTML = function (where,htmlStr) {
		var r = this.ownerDocument.createRange();
		r.setStartBefore(this);
		var parsedHTML = r.createContextualFragment(htmlStr);
		this.insertAdjacentElement(where,parsedHTML)
	}

	HTMLElement.prototype.insertAdjacentText = function (where,txtStr) {
		var parsedText = document.createTextNode(txtStr)
		this.insertAdjacentElement(where,parsedText)
	}
}
		
var FarsiType = 
{
	// Farsi keyboard map based on Iran Popular Keyboard Layout
	farsiKey : [
		32    , 33    , 34    , 35    , 36    , 37    , 1548  , 1711  ,
		41    , 40    , 215   , 43    , 1608  , 45    , 46    , 47    ,
		48    , 49    , 50    , 51    , 52    , 53    , 54    , 55    ,
		56    , 57    , 58    , 1705  , 44    , 61    , 46    , 1567  ,
		64    , 1616  , 1584  , 125   , 1609  , 1615  , 1609  , 1604  ,
		1570  , 247   , 1600  , 1548  , 47    , 8217  , 1583  , 215   ,
		1563  , 1614  , 1569  , 1613  , 1601  , 8216  , 123   , 1611  ,
		1618  , 1573  , 126   , 1580  , 1688  , 1670  , 94    , 95    ,
		1662  , 1588  , 1584  , 1586  , 1740  , 1579  , 1576  , 1604  ,
		1575  , 1607  , 1578  , 1606  , 1605  , 1574  , 1583  , 1582  ,
		1581  , 1590  , 1602  , 1587  , 1601  , 1593  , 1585  , 1589  ,
		1591  , 1594  , 1592  , 60    , 124   , 62    , 1617
	],
	Type : true,
	counter : 0,
	ShowChangeLangButton : 1, // 0: Show / 1: Visible
	KeyBoardError : 0, // 0: Disable FarsiType / 1: Show Error
	ChangeDir : 2 // 0: No Action / 1: Do Rtl-Ltr / 2: Rtl-Ltr button
}

FarsiType.enable_disable = function(Dis) {
	var invis, obj;
	
	if (!Dis.checked)  {
		FarsiType.Type = true;
		disable = false;
		color = 'darkblue';
	}
	else {
		FarsiType.Type = false;
		disable = true;
		color = '#ECE9D8';
	}

	if (FarsiType.ShowChangeLangButton == 1) { 
		for (var i=1; i<= FarsiType.counter; i++) {
			obj = document.getElementById('FarsiType_button_' + i);
			obj.disabled = disable;
			obj.style.backgroundColor = color;
		}
	}
}

FarsiType.Disable = function(){
	FarsiType.Type = false;
	var Dis = document.getElementById('disableFarsiType')
	if (Dis != null) {
		Dis.checked = true;
	}
	if (FarsiType.ShowChangeLangButton == 1) { 
		for (var i=1; i<= FarsiType.counter; i++) {
			obj = document.getElementById('FarsiType_button_' + i);
			obj.disabled = true;
			obj.style.backgroundColor = '#ECE9D8';
		}
	}
}

FarsiType.init = function() {

	var Inputs = document.getElementsByTagName('INPUT');
	for (var i=0; i<Inputs.length; i++) {
		if (Inputs[i].type.toLowerCase() == 'text' && (Inputs[i].lang.toLowerCase() == 'fa' || Inputs[i].lang.toLowerCase() == 'fa-ir')) {
			FarsiType.counter++;
			new FarsiType.KeyObject(Inputs[i], FarsiType.counter);
		}
	}

	var Areas = document.getElementsByTagName('TEXTAREA');
	for (var i=0; i<Areas.length; i++) {
		if (Areas[i].lang.toLowerCase() == 'fa' || Areas[i].lang.toLowerCase() == 'fa-ir') {
			FarsiType.counter++;
			new FarsiType.KeyObject(Areas[i], FarsiType.counter);
		}
	}
	
	var Dis = document.getElementById('disableFarsiType')
	if (Dis != null) {
		FarsiType.enable_disable (Dis);
		Dis.onclick = new Function( "FarsiType.enable_disable (this);" )
	}
}

FarsiType.KeyObject = function(z,x) {

	GenerateStr = "";
	if (FarsiType.ShowChangeLangButton == 1) {
		GenerateStr = GenerateStr + "<input type='button' id=FarsiType_button_"+x+" style='border: none; background-color:darkblue; font-size:11; color:white; font-family:tahoma; padding: 1px; margin: 1px; width: auto; height: auto;' value='FA' />&nbsp;";
	}
	if (FarsiType.ChangeDir == 2) {
		GenerateStr = GenerateStr  + "<input type='button' id=FarsiType_ChangeDir_"+x+" style='border: none; background-color:darkblue; font-size:11; color:white; font-family:tahoma; padding: 1px; margin: 1px; width: auto; height: auto;' value='RTL' />"
	}
	z.insertAdjacentHTML("afterEnd", GenerateStr);

	if (FarsiType.ShowChangeLangButton == 1) { 
		z.bottelm = document.getElementById ('FarsiType_button_' + x);
		z.bottelm.title = 'Change lang to english';
	}
	if (FarsiType.ChangeDir == 2) {
		z.Direlm = document.getElementById ('FarsiType_ChangeDir_' + x); 
	}

	z.farsi = true;
	z.dir = "rtl";
	z.align = "right";

	z.style.textAlign = z.align;
	z.style.direction = z.dir;

	setSelectionRange = function(input, selectionStart, selectionEnd) {
		input.focus()
		input.setSelectionRange(selectionStart, selectionEnd)
	}

	ChangeDirection = function(e) {
		if (z.dir == "rtl") {
			z.dir = "ltr";
			z.align = "left";
			z.Direlm.value = "LTR";
			z.Direlm.title = "Change direction: Right to Left"
		}
		else {
			z.dir = "rtl";
			z.align = "right";
			z.Direlm.value = "RTL";
			z.Direlm.title = "Change direction: Left to Right"
		}
		z.style.textAlign = z.align;
		z.style.direction = z.dir;
		z.focus();
	}

	ChangeLang = function(e) {
		if (e == null) e = window.event;
		var key = e.keyCode ? e.keyCode : e.charCode;

		if (FarsiType.Type) {
			if (key==119 || !key) {
				if (z.farsi) {
					z.farsi = false;
					if (FarsiType.ShowChangeLangButton == 1) { 
						z.bottelm.value = "EN";
						z.bottelm.title = 'Change lang to persian';
					}
					if (FarsiType.ChangeDir == 1) {
						z.style.textAlign = "left";
						z.style.direction = "ltr";
					}
				}
				else {
					z.farsi = true;
					if (FarsiType.ShowChangeLangButton == 1) { 
						z.bottelm.value = "FA";
						z.bottelm.title = 'Change lang to english';
					}
					if (FarsiType.ChangeDir == 1) {
						z.style.textAlign = "right";
						z.style.direction = "rtl";
					}
				}
				z.focus();
			}
		}
	}
	
	Convert = function(e) {

		if (FarsiType.Type) {

			if (e == null) e = window.event;
			eElement = (e.srcElement) ? e.srcElement : e.originalTarget;
			
			var key = e.keyCode ? e.keyCode : e.charCode;
			if (navigator.userAgent.toLowerCase().indexOf('opera')>-1) key = e.which;
			
			if ( (e.charCode != null) && (e.charCode != key) )	return;
			if (e.ctrlKey || e.altKey || e.metaKey || key == 13 || key == 27 || key == 8) return;

			//check windows lang
			if (key>128){
				if (FarsiType.KeyBoardError == 0) {
					FarsiType.Disable();
				}
				else {
					alert("Please change your windows language to English");
					return false;
				}
			}

			// if Farsi
			if (z.farsi && key > 31 && key < 128) {

				//check CpasLock
				if ( (key >= 65 && key <= 90) && !e.shiftKey ) {
					alert("Caps Lock is On. To prevent entering farsi incorrectly, you should press Caps Lock to turn it off.");
					return false;
				}
				else if ( (key >= 97 && key <= 122 ) && e.shiftKey ) {
					alert("Caps Lock is On. To prevent entering farsi incorrectly, you should press Caps Lock to turn it off.");
					return false;
				}

				// Shift-space -> ZWNJ
				if (key == 32 && e.shiftKey)
					key = 8204;
				else
					key = FarsiType.farsiKey[key-32];

				// to farsi
				try {
					// IE
					e.keyCode = key
				}
				catch(error) {
					try {
						// Gecko before
						e.initKeyEvent("keypress", true, true, document.defaultView, false, false, true, false, 0, key, eElement);
					}
					catch(error) {
						try {
							// Gecko & Opera now
							var nScrollTop = eElement.scrollTop;
							var nScrollLeft = eElement.scrollLeft;
							var nScrollWidth = eElement.scrollWidth;

							replaceString = String.fromCharCode(key);

							var selectionStart = eElement.selectionStart;
							var selectionEnd = eElement.selectionEnd;
							eElement.value = eElement.value.substring(0, selectionStart) + replaceString + eElement.value.substring(selectionEnd);
							setSelectionRange(eElement, selectionStart + replaceString.length, selectionStart + replaceString.length);

							var nW = eElement.scrollWidth - nScrollWidth;
							if (eElement.scrollTop == 0) { eElement.scrollTop = nScrollTop }

							e.preventDefault()
						}
						catch(error) {
							// else no farsi type!
							alert('Sorry! no FarsiType support')
							FarsiType.Disable();
							var Dis = document.getElementById('disableFarsiType')
							if (Dis != null) {
								Dis.disabled = true;
							}
							return false;
						}
					}
				}
			}
		}
		return true;
	}

	if (FarsiType.ShowChangeLangButton == 1) { z.bottelm.onmouseup = ChangeLang; }
	if (FarsiType.ChangeDir == 2) { z.Direlm.onmouseup = ChangeDirection; }
	z.onkeydown = ChangeLang;
	z.onkeypress = Convert;
}

if (window.attachEvent) {
	window.attachEvent('onload', FarsiType.init)
}
else if (window.addEventListener) {
	window.addEventListener('load', FarsiType.init, false)
}
