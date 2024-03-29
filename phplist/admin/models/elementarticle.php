<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class PhplistModelElementArticle extends DSCModelElement {
	var $title_key = 'title';
	var $select_title_constant = 'COM_PHPLIST_SELECT_AN_ARTICLE';
	var $select_constant = 'COM_PHPLIST_SELECT';
	var $clear_constant = 'COM_PHPLIST_CLEAR_SELECTION';

	function getTable($name = '', $prefix = null, $options = array()) {
		$table = JTable::getInstance('Content', 'DSCTable');
		return $table;
	}

	/* Compatibility wrappers*/
	function _fetchElement($name, $value = '', $control_name = '', $js_extra = '', $fieldName = '') {
		return $this -> fetchElement($name, $value, $control_name, $js_extra , $fieldName );
	}

	function _clearElement($name, $value = '', $control_name = '') {
		return $this -> clearElement($name, $value, $control_name );
	}

	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	function fetchElement($name, $value = '', $control_name = '', $js_extra = '', $fieldName = '', $clear = NULL) {
		$doc = JFactory::getDocument();

		if (empty($fieldName)) {
			$fieldName = $control_name ? $control_name . '[' . $name . ']' : $name;
		}

		if ($value) {
			$table = $this -> getTable();
			$table -> load($value);
			$title_key = $this -> title_key;
			$title = $table -> $title_key;
		} else {
			$title = JText::_($this -> select_title_constant);
		}

		$close_window = '';
		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			$close_window = "window.parent.SqueezeBox.close();";
		} else {
			$close_window = "document.getElementById('sbox-window').close();";
		}

		$js = "Dsc.select" . $this -> getName() . " = function(id, title, object) {
                        document.getElementById(object + '_id').value = id;
                        document.getElementById(object + '_name').value = title;
                        document.getElementById(object + '_name_hidden').value = title;
        $close_window
        $js_extra
                   }";
		$doc -> addScriptDeclaration($js);

		if (!empty($this -> option)) {
			$option = $this -> option;
		} else {
			$r = null;

			if (!preg_match('/(.*)Model/i', get_class($this), $r)) {
				JError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'));
			}

			$option = 'com_' . strtolower($r[1]);
		}
		$link = 'index.php?option=' . $option . '&view=' . $this -> getName() . '&tmpl=component&object=' . $name;

		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n" . '<div class="pull-left"><input type="hidden" style="background: #ffffff;" type="text" id="' . $name . '_name" value="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" disabled="disabled" /></div>';
		$html .= '<a class="modal btn btn-primary" style=""  title="' . JText::_($this -> select_title_constant) . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">' . JText::_($this -> select_constant) . '</a>' . "\n";
		$html .= "\n" . '<input type="hidden" id="' . $name . '_id" name="' . $fieldName . '" value="' . $value . '" />';
		$html .= "\n" . '<input type="hidden" id="' . $name . '_name_hidden" name="' . $name . '_name_hidden" value="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" />';

		return $html;
	}

	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	function clearElement($name, $value = '', $control_name = '') {
		$doc = JFactory::getDocument();
		$fieldName = $control_name ? $control_name . '[' . $name . ']' : $name;

		$js = "
            Dsc.reset" . $this -> getName() . " = function(id, title, object) {
                document.getElementById(object + '_id').value = id;
                document.getElementById(object + '_name').value = title;
            }";
		$doc -> addScriptDeclaration($js);

		$html = '<a class="btn btn-danger"  style="color:#fff;" href="javascript:void(0);" onclick="Dsc.reset' . $this -> getName() . '( \'' . $value . '\', \'' . JText::_($this -> select_title_constant) . '\', \'' . $name . '\' )">' . JText::_($this -> clear_constant) . '
                    </a>';

		return $html;
	}

}
?>
