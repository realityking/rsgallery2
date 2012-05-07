<script type="text/javascript">
// Set the "inside" HTML of an element.
function setInnerHTML(element, toValue)
{
	// IE has this built in...
	if (typeof(element.innerHTML) != 'undefined')
		element.innerHTML = toValue;
	// Otherwise, try createContextualFragment().
	else
	{
		var range = document.createRange();
		range.selectNodeContents(element);
		range.deleteContents();
		element.appendChild(range.createContextualFragment(toValue));
	}
}

// Set the "outer" HTML of an element.
function setOuterHTML(element, toValue)
{
	if (typeof(element.outerHTML) != 'undefined')
		element.outerHTML = toValue;
	else
	{
		var range = document.createRange();
		range.setStartBefore(element);
		element.parentNode.replaceChild(range.createContextualFragment(toValue), element);
	}
}

// Get the inner HTML of an element.
function getInnerHTML(element)
{
	if (typeof(element.innerHTML) != 'undefined')
		return element.innerHTML;
	else
	{
		var returnStr = '';
		for (var i = 0; i < element.childNodes.length; i++)
			returnStr += getOuterHTML(element.childNodes[i]);

		return returnStr;
	}
}

function getOuterHTML(node)
{
	if (typeof(node.outerHTML) != 'undefined')
		return node.outerHTML;

	var str = '';

	switch (node.nodeType)
	{
	// An element.
	case 1:
		str += '<' + node.nodeName;

		for (var i = 0; i < node.attributes.length; i++)
		{
			if (node.attributes[i].nodeValue != null)
				str += ' ' + node.attributes[i].nodeName + '="' + node.attributes[i].nodeValue + '"';
		}

		if (node.childNodes.length == 0 && in_array(node.nodeName.toLowerCase(), ['hr', 'input', 'img', 'link', 'meta', 'br']))
			str += ' />';
		else
			str += '>' + getInnerHTML(node) + '</' + node.nodeName + '>';
		break;

	// 2 is an attribute.

	// Just some text..
	case 3:
		str += node.nodeValue;
		break;

	// A CDATA section.
	case 4:
		str += '<![CDATA' + '[' + node.nodeValue + ']' + ']>';
		break;

	// Entity reference..
	case 5:
		str += '&' + node.nodeName + ';';
		break;

	// 6 is an actual entity, 7 is a PI.

	// Comment.
	case 8:
		str += '<!--' + node.nodeValue + '-->';
		break;
	}

	return str;
}

var allowed_attachments = 10 - 1;
function addAttachment() {
    if (allowed_attachments <= 0)
    return alert("Sorry, you aren't allowed to post any more attachments.");

    setOuterHTML(document.getElementById("moreAttachments"), '<br /><?php echo JText::_('Title')?>:&nbsp;<input class="text" type="text" id="title" name="title[]" value="" size="50" maxlength="250" /><br /><br /><?php echo JText::_('File')?>:&nbsp;&nbsp;<input type="file" size="48" id="images" name="images[]" class="required" /><br /><hr /><span id="moreAttachments"></span>');
    allowed_attachments = allowed_attachments - 1;
return true;
}
</script>