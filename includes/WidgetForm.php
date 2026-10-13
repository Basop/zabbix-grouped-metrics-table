<?php declare(strict_types = 0);
/*
** Copyright (C) 2001-2026 Zabbix SIA
**
** This program is free software: you can redistribute it and/or modify it under the terms of
** the GNU Affero General Public License as published by the Free Software Foundation, version 3.
**
** This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
** without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
** See the GNU Affero General Public License for more details.
**
** You should have received a copy of the GNU Affero General Public License along with this program.
** If not, see <https://www.gnu.org/licenses/>.
**/


namespace Modules\GroupedMetricsTable\Includes;

use Zabbix\Widgets\{
	CWidgetField,
	CWidgetForm
};

use Zabbix\Widgets\Fields\{
	CWidgetFieldIntegerBox,
	CWidgetFieldMultiSelectGroup,
	CWidgetFieldMultiSelectHost,
	CWidgetFieldMultiSelectOverrideHost,
	CWidgetFieldPatternSelectHost,
	CWidgetFieldPatternSelectItem,
	CWidgetFieldRadioButtonList,
	CWidgetFieldTags,
	CWidgetFieldTextBox
};

/**
 * Top items data widget form.
 */
class WidgetForm extends CWidgetForm {
	public const ORDER_ASC = 0;
	public const ORDER_DESC = 1;
	public const COLUMN_HEADER_OFF = 0;
	public const COLUMN_HEADER_HORIZONTAL = 1;
	public const COLUMN_HEADER_VERTICAL = 2;

	public function addFields(): self {
		return $this
			->addField($this->isTemplateDashboard() ? null : new CWidgetFieldMultiSelectGroup('groupids', _('Host groups')))
			->addField($this->isTemplateDashboard() ? null : new CWidgetFieldMultiSelectHost('hostids', _('Hosts')))
			->addField($this->isTemplateDashboard() ? null
				: (new CWidgetFieldRadioButtonList('host_tags_evaltype', _('Host tags'), [
					TAG_EVAL_TYPE_AND_OR => _('And/Or'), TAG_EVAL_TYPE_OR => _('Or')
				]))->setDefault(TAG_EVAL_TYPE_AND_OR))
			->addField($this->isTemplateDashboard() ? null : new CWidgetFieldTags('host_tags'))
			->addField((new CWidgetFieldTextBox('row_tag', _('Group rows by item tag')))
				->setDefault('container')->setFlags(CWidgetField::FLAG_NOT_EMPTY | CWidgetField::FLAG_LABEL_ASTERISK))
			->addField((new CWidgetFieldTextBox('row_include_regex', _('Include tag values matching')))->setDefault('.*'))
			->addField(new CWidgetFieldTextBox('row_strip_regex', _('Remove from displayed row name')))
			->addField((new CWidgetFieldColumnsList('columns', _('Items')))
				->setFlags(CWidgetField::FLAG_NOT_EMPTY | CWidgetField::FLAG_LABEL_ASTERISK))
			->addField((new CWidgetFieldRadioButtonList('row_order', _('Row order'), [
				self::ORDER_ASC => _('Ascending'), self::ORDER_DESC => _('Descending')
			]))->setDefault(self::ORDER_ASC))
			->addField((new CWidgetFieldIntegerBox('row_limit', _('Row limit'), ZBX_MIN_WIDGET_LINES, ZBX_MAX_WIDGET_LINES))
				->setDefault(100)->setFlags(CWidgetField::FLAG_NOT_EMPTY | CWidgetField::FLAG_LABEL_ASTERISK))
			->addField((new CWidgetFieldRadioButtonList('show_column_header', _('Show column header'), [
				self::COLUMN_HEADER_OFF => _('Off'), self::COLUMN_HEADER_HORIZONTAL => _('Horizontal'),
				self::COLUMN_HEADER_VERTICAL => _('Vertical')
			]))->setDefault(self::COLUMN_HEADER_HORIZONTAL))
			->addField(new CWidgetFieldMultiSelectOverrideHost());
	}
}
