var http = (window.XMLHttpRequest ? new XMLHttpRequest : (window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : false));
var operaBrowser = (navigator.userAgent.toLowerCase().indexOf("opera") != -1);
var rsearchphrase_selection="any"
var userName = '';

var clientPC = navigator.userAgent.toLowerCase();
var clientVer = parseInt(navigator.appVersion);

var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;

var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);

var scrollTopPos = 0;
var scrollLeftPos = 0;

if (typeof HTMLElement != "undefined" && !
    HTMLElement.prototype.insertAdjacentElement) {
    HTMLElement.prototype.insertAdjacentElement = function
    (where, parsedNode)
    {
        switch (where) {
            case 'beforeBegin':
                this.parentNode.insertBefore(parsedNode, this)
                break;
            case 'afterBegin':
                this.insertBefore(parsedNode, this.firstChild);
                break;
            case 'beforeEnd':
                this.appendChild(parsedNode);
                break;
            case 'afterEnd':
                if (this.nextSibling)
                    this.parentNode.insertBefore(parsedNode, this.nextSibling);
                else this.parentNode.appendChild(parsedNode);
                break;
        }
    }

    HTMLElement.prototype.insertAdjacentHTML = function
    (where, htmlStr)
    {
        var r = this.ownerDocument.createRange();
        r.setStartBefore(this);
        var parsedHTML = r.createContextualFragment(htmlStr);
        this.insertAdjacentElement(where, parsedHTML)
    }

    HTMLElement.prototype.insertAdjacentText = function
    (where, txtStr)
    {
        var parsedText = document.createTextNode(txtStr)
        this.insertAdjacentElement(where, parsedText)
    }
}

function HTTPParam()
{
}

HTTPParam.prototype.create = function(command, id)
{
    this.result = 'option=com_comment';
    this.insert('no_html', 1);
    this.insert('command', command);
    this.insert('comment_id', id);
    return this.result;
}

HTTPParam.prototype.insert = function(name, value)
{
    this.result += '&' + name + '=' + value;
    return this.result;
}

HTTPParam.prototype.encode = function(name, value)
{
    return this.insert(name, encodeURIComponent(value));
}

function BusyImage()
{
}

BusyImage.prototype.create = function()
{
    var form = document.joomlacommentform;
    var image = document.createElement('img');
    image.setAttribute('src', liveSite + '/images/busy.gif');
    image.setAttribute('id', "busyImage");
    var element = document.getElementById('busy');
    if (!element.innerHTML) element.appendChild(image);
}

BusyImage.prototype.destroy = function()
{
    var image = document.getElementById("busyImage");
    image.parentNode.removeChild(image);
}

var busyImage = new BusyImage();

function ajaxSend(data, onReadyStateChange)
{
    document.joomlacommentform.bsend.disabled = true;
    busyImage.create();
    http.open("POST", 'index2.php', true);
    http.onreadystatechange = onReadyStateChange;
    http.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    http.send(data);
}

function ajaxReady()
{
    if (http.readyState == 4) {
        if (http.status == 200) {
            busyImage.destroy();
            document.joomlacommentform.bsend.disabled = false;
            return true;
        }
    }
    return false;
}

function goToAnchor(name)
{
    clearTimeout(self.timer);
    action = function()
    {
		var url = window.location.toString();
		var index = url.indexOf('#');
		if (index == -1) { window.location = url + '#' + name; }
		else { window.location = url.substring(0, index) + '#' + name; }
        if (operaBrowser) window.location = '##';
    }
    if (operaBrowser) self.timer = setTimeout(action, 50);
    else action();
}

function goToPost(contentid, id)
{
	var form = document.joomlacommentform;
	if (form.content_id.value==contentid) goToAnchor('josc'+id);
	else window.location = 'index.php?option=com_content&task=view&id=' + contentid + '#josc' + id;
	if (operaBrowser) window.location = '##';
}

function modifyForm(formTitle, buttonValue, onClick)
{
    document.getElementById('CommentFormTitle').innerHTML = formTitle;
    button = document.joomlacommentform.bsend;
    button.value = buttonValue;
    button.onclick = onClick;
}

function xmlValue(xmlDocument, tagName)
{
    try {
        var result = xmlDocument.getElementsByTagName(tagName).item(0).firstChild.data;
    }
    catch(e) {
        var result = '';
    }
    return result;
}

function removePost(post)
{
    document.getElementById('Comments').removeChild(post);
}

function deleteComment(id)
{
    if (window.confirm(_JOOMLACOMMENT_MSG_DELETE)) {
        var data = new HTTPParam().create('ajax_delete', id);
        ajaxSend(data, function()
            {
                if (ajaxReady()) {
                    if (http.responseText != '') alert(http.responseText);
                    else removePost(document.getElementById('post' + id));
                }
            }
            );
    }
}

function deleteAll(id)
{
	if (window.confirm(_JOOMLACOMMENT_MSG_DELETEALL)) {
        var form = document.joomlacommentform;
		var param = new HTTPParam();
		param.create('ajax_delete_all', id);
        ajaxSend(param.insert('content_id',form.content_id.value), function()
            {
                if (ajaxReady()) {
                    if (http.responseText != '') alert(http.responseText);
                    else {
                    	addNew();
                    	document.getElementById('Comments').innerHTML='';
					}
                }
            }
            );
    }
}

function editResponse()
{
    if (ajaxReady()) {
        if (http.responseText.indexOf('invalid') == -1) {
            var xmlDocument = http.responseXML;
            var form = document.joomlacommentform;
            userName = form.tname.value;
            form.tname.value = xmlValue(xmlDocument, 'name');
            form.ttitle.value = xmlValue(xmlDocument, 'title');
            form.tcomment.value = xmlValue(xmlDocument, 'comment');
        }
    }
}

function editComment(id)
{
    modifyForm(_JOOMLACOMMENT_EDITCOMMENT, _JOOMLACOMMENT_EDIT,
        function(event)
        {
            editPost(id, -1);}
        );
    goToAnchor('CommentForm');
    var data = new HTTPParam().create('ajax_quote', id);
    ajaxSend(data, editResponse);
}

function quoteResponse()
{
    if (ajaxReady()) {
        if (http.responseText.indexOf('invalid') == -1) {
            var form = document.rsgcommentform;
            var xmlDocument = http.responseXML;
            name = xmlValue(xmlDocument, 'name');
            if (name == '') name = _JOOMLACOMMENT_ANONYMOUS;
            if (form.ttitle.value == '') form.ttitle.value = 're: ' +
                xmlValue(xmlDocument, 'title');
            form.tcomment.value += '[quote=' + name + ']' +
            xmlValue(xmlDocument, 'comment') + '[/quote]';
        }
    }
}

function quote(id)
{
    var data = new HTTPParam().create('ajax_quote', id);
    goToAnchor('CommentForm');
    ajaxSend(data, quoteResponse);
}

function emoticon(icon)
{
  var txtarea = document.rsgcommentform.tcomment;
  scrollToCursor(txtarea, 0);
  txtarea.focus();
  pasteAtCursor(txtarea, ' ' + icon + ' ');
  scrollToCursor(txtarea, 1);
}

function insertTags(bbStart, bbEnd) {
  var txtarea = document.rsgcommentform.tcomment;
  scrollToCursor(txtarea, 0);
  txtarea.focus();

  if ((clientVer >= 4) && is_ie && is_win) {
    theSelection = document.selection.createRange().text;
    if (theSelection) {
      document.selection.createRange().text = bbStart + theSelection + bbEnd;
      theSelection = '';
      return;
    } else {
      pasteAtCursor(txtarea, bbStart + bbEnd);
	}
  } else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0)) {
    var selLength = txtarea.textLength;
    var selStart = txtarea.selectionStart;
    var selEnd = txtarea.selectionEnd;
    var s1 = (txtarea.value).substring(0,selStart);
    var s2 = (txtarea.value).substring(selStart, selEnd)
    var s3 = (txtarea.value).substring(selEnd, selLength);
    txtarea.value = s1 + bbStart + s2 + bbEnd + s3;
    txtarea.selectionStart = selStart + (bbStart.length + s2.length + bbEnd.length);
    txtarea.selectionEnd = txtarea.selectionStart;
    scrollToCursor(txtarea, 1);
    return;
  } else {
    pasteAtCursor(txtarea, bbStart + bbEnd);
	scrollToCursor(txtarea, 1);
  }
}

function scrollToCursor(txtarea, action) {
  if (is_nav) {
    if (action == 0) {
      scrollTopPos = txtarea.scrollTop;
      scrollLeftPos = txtarea.scrollLeft;
    } else {
      txtarea.scrollTop = scrollTopPos;
      txtarea.scrollLeft = scrollLeftPos;
    }
  }
}

function pasteAtCursor(txtarea, txtvalue) {
  if (document.selection) {
    var sluss;
    txtarea.focus();
    sel = document.selection.createRange();
    sluss = sel.text.length;
    sel.text = txtvalue;
    if (txtvalue.length > 0) {
      sel.moveStart('character', -txtvalue.length + sluss);
    }
  } else if (txtarea.selectionStart || txtarea.selectionStart == '0') {
    var startPos = txtarea.selectionStart;
    var endPos = txtarea.selectionEnd;
    txtarea.value = txtarea.value.substring(0, startPos) + txtvalue + txtarea.value.substring(endPos, txtarea.value.length);
    txtarea.selectionStart = startPos + txtvalue.length;
    txtarea.selectionEnd = startPos + txtvalue.length;
  } else {
    txtarea.value += txtvalue;
  }
}


function insertUBBTag(tag)
{
    insertTags('[' + tag + ']', '[/' + tag + ']');
}

function fontColor(){
  var color = document.rsgcommentform.menuColor.selectedIndex;
  switch (color){
    case 0: color=''; break;
    case 1: color='aqua'; break;
    case 2: color='black'; break;
    case 3: color='blue'; break;
    case 4: color='fuchsia'; break;
    case 5: color='gray'; break;
    case 6: color='green'; break;
    case 7: color='lime'; break;
    case 8: color='maroon'; break;
    case 9: color='navy'; break;
    case 10: color='olive'; break;
    case 11: color='purple'; break;
    case 12: color='red'; break;
    case 13: color='silver'; break;
    case 14: color='teal'; break;
    case 15: color='white'; break;
    case 16: color='yellow'; break;
  }
  if (color!='') insertTags('[color='+color+']','[/color]');
}

function fontSize()
{
    var size = document.rsgcommentform.menuSize.selectedIndex;
    switch (size) {
        case 0: size = '';
            break;
        case 1: size = 'x-small';
            break;
        case 2: size = 'small';
            break;
        case 3: size = 'medium';
            break;
        case 4: size = 'large';
            break;
        case 5: size = 'x-large';
            break;
    }
    if (size != '') insertTags('[size=' + size + ']', '[/size]');
}

function clearInputbox()
{
    var form = document.rsgcommentform;
    form.ttitle.value = '';
    form.tcomment.value = '';
}

function getPostClass(post)
{
    return post.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].className;
}

function setPostClass(post, value)
{
    post.getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].className = value;
}

function refreshCaptcha(captcha)
{
    document.getElementById('captcha').innerHTML = captcha;
    document.rsgcommentform.security_try.value = '';
}

function removeSearchResults()
{
    var searchResults = document.getElementById('SearchResults');
    if (searchResults) searchResults.parentNode.removeChild(searchResults);
}

function searchFormResponse()
{
	if (ajaxReady()) {
        form = http.responseText;
        if (form != '')
            document.getElementById('CommentMenu').insertAdjacentHTML('afterEnd', form);
    }
}

function searchForm()
{
	removeSearchResults();
	var form = document.joomlacommentsearch;
    if (form) {
        form.parentNode.removeChild(form);
        if (!operaBrowser) document.joomlacommentsearch = null;
    } else {
        ajaxSend(new HTTPParam().create('ajax_insert_search', 0), searchFormResponse);
    }
}

function searchResponse()
{
    if (ajaxReady()) {
        form = http.responseText;
		if (form != '')
            document.joomlacommentsearch.insertAdjacentHTML('afterEnd', form);
    }
}

function search()
{
	removeSearchResults();
	var keyword = document.joomlacommentsearch.tsearch.value;
	if (keyword=='') return 0;
	param = new HTTPParam();
	param.create('ajax_search', 0);
	param.encode('search_keyword', keyword)
	ajaxSend(param.insert('search_phrase',rsearchphrase_selection), searchResponse);
}

function addNew()
{
    var form = document.rsgcommentform;
    form.bsend.onclick = function(event)
    {
        editPost(-1, -1);
    } ;
    if (form.parentNode.id != 'comment')
        document.getElementById('Comments').insertAdjacentElement('afterEnd', form);
    goToAnchor('CommentForm');
}

function reply(id)
{
    var form = document.rsgcommentform;
    var post = document.getElementById('post' + id);
    modifyForm(_JOOMLACOMMENT_WRITECOMMENT, _JOOMLACOMMENT_SENDFORM,
    function(event)
    {
        editPost(-1, id);
	});
    post.insertAdjacentElement('afterEnd', form);
}

function editPostResponse()
{
    if (ajaxReady()) {
        if (http.responseText.indexOf('invalid') == -1) {
            var form = document.rsgcommentform;
            var element = document.getElementById('Comments');
			var xmlDocument = http.responseXML;

			if (!xmlDocument) {
                alert(http.responseText);
                return 0;
            }
            var id = xmlValue(xmlDocument, 'id');
            var after = xmlValue(xmlDocument, 'after');
            var captcha = xmlValue(xmlDocument, 'captcha');
            if (captcha) {
                refreshCaptcha(captcha);
                if (id == 'captcha') return 0;
            }
            anchor = 'josc' + id;
            id = 'post' + id;
            var body = xmlValue(xmlDocument, 'body');
			var post = document.getElementById(id);
            clearInputbox();
            var published = xmlValue(xmlDocument, 'published');
			if (published==0) {
				alert(_JOOMLACOMMENT_BEFORE_APPROVAL);
				form.tcomment.value=http.responseText;
				return 0;
            }
            if (post) {
				var className = getPostClass(post);
                var indent = post.style.paddingLeft;
                post.insertAdjacentHTML('beforeBegin', body);
                removePost(post);
                newPost = document.getElementById(id);
                setPostClass(newPost, className);
                newPost.style.paddingLeft = indent;
                modifyForm(_JOOMLACOMMENT_WRITECOMMENT, _JOOMLACOMMENT_SENDFORM,
                    function(event)
                    {
                        editPost(-1, -1);}
                    );
                form.tname.value = userName;
            } else {
				if (sortDownward != 0) element.insertAdjacentHTML('afterBegin', body);
                else {
                    if (!after || after == -1) element.insertAdjacentHTML('beforeEnd', body);
                    else document.getElementById('post' + after).insertAdjacentHTML('afterEnd', body);
                }
                setPostClass(document.getElementById(id),
                    'sectiontableentry' + postCSS);
                postCSS == 1 ? postCSS = 2 : postCSS = 1;
            }
            goToAnchor(anchor);
        }
	}
}

function editPost(id, parentid)
{
	var form = document.rsgcommentform;
    if (form.tcomment.value == '') {
        alert(_JOOMLACOMMENT_FORMVALIDATE);
        return 0;
    }
	if (ajaxEnabled) {
        var param = new HTTPParam();
        param.create(id == -1 ? 'ajax_insert' : 'ajax_edit', id);
        param.insert('content_id', form.content_id.value);
        if (captchaEnabled) {
            param.insert('security_try', form.security_try.value);
            param.insert('security_refid', form.security_refid.value);
        }
        if (parentid != -1) param.insert('parent_id', parentid);
        param.encode('tname', form.tname.value);
        param.encode('ttitle', form.ttitle.value);
		ajaxSend(param.encode('tcomment', form.tcomment.value), editPostResponse);
    } else {
        form.action = './index.php';
        form.submit();
    }
}