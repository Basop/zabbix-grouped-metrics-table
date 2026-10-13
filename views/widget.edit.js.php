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

use Modules\GroupedMetricsTable\Includes\WidgetForm;

?>


window.widget_form = new class extends CWidgetForm {

	/**
	 * Widget form.
	 *
	 * @type {HTMLFormElement}
	 */
	#form;

	/**
	 * Template id.
	 *
	 * @type {string}
	 */
	#templateid;

	/**
	 * Column list container.
	 *
	 * @type {HTMLElement}
	 */
	#list_columns;

	/**
	 * Column list entry template.
	 *
	 * @type {Template}
	 */
	#list_column_tmpl;

	init({templateid}) {
		this.#form = this.getForm();
		this.#list_columns = document.getElementById('list_columns');
		this.#list_column_tmpl = new Template(this.#list_columns.querySelector('template').innerHTML);
		this.#templateid = templateid;


		this.#list_columns.addEventListener('click', (e) => this.#processColumnsAction(e));
		this.ready();
	}

	/**
	 * Updates widget column configuration form field visibility, enable/disable state and available options.
	 */
	#triggerUpdate() {
		this.#form.dispatchEvent(new CustomEvent('form_fields.changed', {detail: {}}));

		this.registerUpdateEvent();
	}

	#processColumnsAction(e) {
		const target = e.target;
		const form_fields = getFormFields(this.#form);

		let column_popup;

		switch (target.getAttribute('name')) {
			case 'add':
				column_popup = PopUp(
					'widget.groupedmetricstable.column.edit',
					{
						templateid: this.#templateid,
						groupids: form_fields.groupids,
						hostids: form_fields.hostids
					},
					{
						dialogueid: 'groupedmetricstable-column-edit-overlay',
						dialogue_class: 'modal-popup-generic'
					}
				).$dialogue[0];

				column_popup.addEventListener('dialogue.submit', (e) => {
					const last_row = this.#list_columns.querySelector(`tbody > tr:last-child`);
					const index = last_row.previousSibling !== null
						? parseInt(last_row.previousSibling.dataset.index) + 1
						: 0;

					last_row.insertAdjacentElement('beforebegin', this.#makeColumnRow(e.detail, index));
					this.#triggerUpdate();
				});

				break;

			case 'edit':
				const column_index = e.target.closest('tr').dataset.index;
				column_popup = PopUp(
					'widget.groupedmetricstable.column.edit',
					{
						...form_fields.columns[column_index],
						edit: 1,
						templateid: this.#templateid,
						groupids: form_fields.groupids,
						hostids: form_fields.hostids
					}, {
						dialogueid: 'groupedmetricstable-column-edit-overlay',
						dialogue_class: 'modal-popup-generic'
					}
					).$dialogue[0];

				column_popup.addEventListener('dialogue.submit', (e) => {
					const row = this.#list_columns.querySelector(`tbody > tr[data-index="${column_index}"]`);
					row.replaceWith(this.#makeColumnRow(e.detail, column_index));
					this.#triggerUpdate();
				});

				break;

			case 'remove':
				target.closest('tr').remove();
				this.#triggerUpdate();
				break;
		}
	}

	#makeColumnRow(data, index) {
		const row = this.#list_column_tmpl.evaluateToElement({
			...data,
			rowNum: index,
			items: Object.values(data.items).join(', '),
			display_name: data.name && data.name.trim() !== '' ? data.name : Object.values(data.items).join(', ')
		});

		row.dataset.index = index;
		const column_data = row.querySelector('.js-column-data');
		for (const [data_key, data_value] of Object.entries(data)) {
			switch (data_key) {
				case 'edit':
					continue;

				case 'thresholds':
					for (const [key, value] of Object.entries(data.thresholds)) {
						column_data.append(this.#makeVar(`columns[${index}][thresholds][${key}][color]`, value.color));
						column_data.append(this.#makeVar(
							`columns[${index}][thresholds][${key}][threshold]`,
							value.threshold
						));
					}

					break;

				case 'highlights':
					for (const [key, value] of Object.entries(data.highlights)) {
						column_data.append(this.#makeVar(`columns[${index}][highlights][${key}][color]`, value.color));
						column_data.append(this.#makeVar(
							`columns[${index}][highlights][${key}][pattern]`,
							value.pattern
						));
					}

					break;

				case 'items':
					for (const [key, value] of Object.entries(data.items)) {
						column_data.append(this.#makeVar(`columns[${index}][items][${key}]`, value));
					}

					break;

				case 'time_period':
					for (const [key, value] of Object.entries(data.time_period)) {
						column_data.append(this.#makeVar(`columns[${index}][time_period][${key}]`, value));
					}

					break;

				case 'sparkline':
					for (const [key, value] of Object.entries(data_value)) {
						if (key === 'time_period') {
							for (const [k, v] of Object.entries(value)) {
								column_data.append(this.#makeVar(`columns[${index}][sparkline][time_period][${k}]`, v));
							}
						}
						else {
							column_data.append(this.#makeVar(`columns[${index}][sparkline][${key}]`, value));
						}
					}
					break;

				case 'item_tags':
					for (const [key, {operator, tag, value}] of Object.entries(data.item_tags)) {
						column_data.append(this.#makeVar(`columns[${index}][item_tags][${key}][operator]`, operator));
						column_data.append(this.#makeVar(`columns[${index}][item_tags][${key}][tag]`, tag));
						column_data.append(this.#makeVar(`columns[${index}][item_tags][${key}][value]`, value));
					}

					break;

				default:
					column_data.append(this.#makeVar(`columns[${index}][${data_key}]`, data_value));

					break;
			}
		}

		return row;
	}

	#makeVar(name, value) {
		const input = document.createElement('input');

		input.setAttribute('type', 'hidden');
		input.setAttribute('name', name);
		input.setAttribute('value', value);

		return input;
	}
};
