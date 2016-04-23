<?php
######################################################################
# Gospel Powered Bible Verse Tooltip  	            	          	 #
# Copyright (C) 2014 by Gospel Powered 	    	   	   	   	   	   	 #
# Homepage   : www.gosperpowered.com	    	   	   	   	   		 #
# Author     : Gospel Powered	    	   	   	   	   	   	   	   	 #
# Email      : support@gospelpowered.com 	   	   	   	   	   	   	     #
# Version    : 1.0.0                     	   	    	   	   	   	 #
# License    : http://www.gnu.org/copyleft/gpl.html GNU/GPL          #
######################################################################

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.environment.response');


class plgSystembibleversetooltip extends JPlugin {

	function plgSystembibleversetooltip(&$subject, $config) {
		parent::__construct($subject, $config);

		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			$mode = $this->params->def('mode', 1);
		}
		else {
			$this->_plugin = JPluginHelper::getPlugin('system', 'bibleversetooltip');
			$this->_params = new JParameter($this->_plugin->params);
		}
	}

	function onAfterRender() {
		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			$mainframe = JFactory::getApplication();
		}
		else {
			global $mainframe;
		}

		if (version_compare(JVERSION, '3.2.0', 'ge'))
			$headers = $mainframe->getHeaders();
		else
			$headers = JResponse::getHeaders();

		if (count($headers) > 0){
			if ($headers[count($headers) - 1]['name'] == 'Content-Type') {
				if (strpos($headers[count($headers) - 1]['value'], 'text/html') === false) {
					return;
				}
			}
		}

		$doc = JFactory::getDocument();
		if ($doc->getType() !== 'html') { // || JRequest::getCmd('tmpl') === 'component'
			return false;
		}

		if (version_compare(JVERSION, '3.2.0', 'ge'))
			$buffer = $mainframe->getBody();
		else
			$buffer = JResponse::getBody();

		 $translation = $this->params->get('translation');
		 $rounded = $this->params->get('rounded');
		 $dropShadow = $this->params->get('dropShadow');
		 $headingColor = $this->params->get('headingColor');
		 $bodyColor = $this->params->get('bodyColor');
		 $headingBG = $this->params->get('headingBG');
		 $bodyBG = $this->params->get('bodyBG');
		 $bodyLink = $this->params->get('bodyLink');
		 $caseInsensitive = $this->params->get('caseInsensitive');
		 $facebook = $this->params->get('facebook');
		 $twitter = $this->params->get('twitter');
		 $google = $this->params->get('google');
		 $faithlife = $this->params->get('faithlife');


		{
			$output = "\n

<script>
	var refTagger = {
		settings: {
			bibleVersion: \"$translation\",
			caseInsensitive: '$caseInsensitive',	
			socialSharing: ['$facebook','$twitter','$google', '$faithlife'],	
			roundCorners: $rounded,
			dropShadow: $dropShadow,
			tooltipStyle: '$bodyBG',
			customStyle : {
				heading: {color : '$headingColor', backgroundColor: '$headingBG'},
				body   : {color : '$bodyColor', moreLink : {color: 'bodyLink'}}
			}

			
		}
	};
	(function(d, t) {
		var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
		g.src = \"//api.reftagger.com/v2/RefTagger.js\";
		s.parentNode.insertBefore(g, s);
	}(document, \"script\"));
</script>

			\n";
			$pos = strrpos($buffer, "</body>");
			if ($pos) {
				$buffer = substr($buffer, 0, $pos) . $output . substr($buffer, $pos);
//				JResponse::setBody($buffer);
			}
		}

		if ( $output || $output ) {
			if (version_compare(JVERSION, '3.2.0', 'ge'))
				$mainframe->setBody($buffer);
			else
				JResponse::setBody($buffer);
		}

		return true;
	}

}