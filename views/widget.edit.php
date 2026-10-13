<?php declare(strict_types = 0);
use Modules\GroupedMetricsTable\Includes\CWidgetFieldColumnsListView;
$form = new CWidgetFormView($data);
$groupids = array_key_exists('groupids', $data['fields']) ? new CWidgetFieldMultiSelectGroupView($data['fields']['groupids']) : null;
$form
	->addField($groupids)
	->addField(array_key_exists('hostids', $data['fields'])
		? (new CWidgetFieldMultiSelectHostView($data['fields']['hostids']))->setFilterPreselect([
			'id' => $groupids->getId(), 'accept' => CMultiSelect::FILTER_PRESELECT_ACCEPT_ID, 'submit_as' => 'groupid'
		]) : null)
	->addField(array_key_exists('host_tags_evaltype', $data['fields'])
		? new CWidgetFieldRadioButtonListView($data['fields']['host_tags_evaltype']) : null)
	->addField(array_key_exists('host_tags', $data['fields']) ? new CWidgetFieldTagsView($data['fields']['host_tags']) : null)
	->addField(new CWidgetFieldTextBoxView($data['fields']['row_tag']))
	->addField(new CWidgetFieldTextBoxView($data['fields']['row_include_regex']))
	->addField(new CWidgetFieldTextBoxView($data['fields']['row_strip_regex']))
	->addField((new CWidgetFieldColumnsListView($data['fields']['columns']))->addClass(ZBX_STYLE_TABLE_FORMS_SEPARATOR))
	->addFieldset((new CWidgetFormFieldsetCollapsibleView(_('Advanced configuration')))
		->addField(new CWidgetFieldRadioButtonListView($data['fields']['row_order']))
		->addField(new CWidgetFieldIntegerBoxView($data['fields']['row_limit']))
		->addField(new CWidgetFieldRadioButtonListView($data['fields']['show_column_header'])))
	->includeJsFile('widget.edit.js.php')
	->initFormJs('widget_form.init('.json_encode(['templateid' => $data['templateid']], JSON_THROW_ON_ERROR).');')
	->show();
