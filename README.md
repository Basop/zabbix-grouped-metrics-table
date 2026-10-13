# Grouped Metrics Table for Zabbix

A native-style dashboard widget for **Zabbix 7.4.15** that organises item values into rows using an item tag and displays selected metrics as configurable columns.

The widget is derived from Zabbix's built-in **Top items** widget. It retains the native column editor, item patterns, item-tag filters, aggregation functions, time-period controls, history and trends support, thresholds, colours, bars, indicators, sparklines, and numeric or text display options.

## Features

- Group rows by any item tag.
- Filter grouped rows with a regular expression.
- Transform displayed row labels with a regular expression.
- Select hosts, host groups, and host tags using native Zabbix controls.
- Add, edit, and remove metric columns through a native-style editor.
- Assign a friendly name to each column.
- Select items by item-name pattern and optional item-tag filters.
- Calculate values using:
  - Latest value
  - Minimum
  - Maximum
  - Average
  - Count
  - Sum
  - First value
  - Last value
- Use the dashboard, another widget, or a custom time period.
- Select history, trends, or automatic data-source selection.
- Display values as plain values, bars, indicators, or sparklines.
- Configure thresholds, colours, highlights, decimal places, minimums, and maximums.
- Sort grouped rows in ascending or descending order.
- Apply a row limit.

## Compatibility

- Zabbix frontend: **7.4.15**
- Module type: Dashboard widget
- Author: **Chris Waldmeyer**

The widget is built for Zabbix 7.4.15. Other releases may require changes because frontend classes and widget APIs can vary between Zabbix versions.

## Installation

1. Download the latest release ZIP.
2. Remove any previous installation:

```bash
sudo rm -rf /usr/share/zabbix/ui/modules/groupedmetricstable
```

3. Extract the release into the Zabbix frontend modules directory:

```bash
sudo unzip grouped_metrics_table.zip \
  -d /usr/share/zabbix/ui/modules/
```

4. Confirm the directory structure:

```text
/usr/share/zabbix/ui/modules/groupedmetricstable/
├── actions/
├── includes/
├── views/
├── manifest.json
└── Widget.php
```

5. Validate the PHP files:

```bash
find /usr/share/zabbix/ui/modules/groupedmetricstable \
  -name '*.php' \
  -exec php -l {} \;
```

6. Restart PHP-FPM if required by the installation:

```bash
sudo systemctl restart php8.3-fpm
```

7. In the Zabbix web interface, open:

```text
Administration → General → Modules
```

8. Select **Scan directory**.
9. Enable **Grouped metrics table**.
10. Edit a dashboard, select **Add widget**, and choose **Grouped metrics table**.

## Configuration

### Data source

Use the native selectors to scope the widget to the required:

- Host groups
- Hosts
- Host tags

### Row grouping

The widget converts distinct values of an item tag into table rows.

| Setting | Purpose | Generic example |
|---|---|---|
| Group rows by item tag | Item tag whose value becomes the row key | `service` |
| Include tag values matching | Regular expression limiting included tag values | `^production-` |
| Remove from displayed row name | Regular expression removed from the visible row label | `^production-` |

For example, items tagged with:

```text
service = production-api
service = production-web
service = production-worker
```

can be displayed as:

```text
api
web
worker
```

The grouping tag can represent any suitable entity, such as a service, application, site, queue, certificate, database, interface, or workload.

## Adding columns

Under **Items**, select **Add** to create a metric column.

### Name

Enter a friendly column heading, for example:

```text
Requests
Errors
Latency
Availability
```

If **Name** is blank, the widget uses the item pattern as the column heading.

### Item patterns

Enter one or more Zabbix item-name patterns that identify the item for the column.

Example:

```text
Application: Requests*
```

Each column should ideally match one item for each grouped row.

### Item tags

Item-tag filters are optional. Use them when an item-name pattern is not sufficiently specific or when tags provide a more stable selection method.

Example:

```text
Tag: metric
Operator: Equals
Value: requests
```

If the item pattern already identifies the correct item uniquely, the item-tag filter can be left empty.

### Value calculation

Open **Advanced configuration** in the column editor to choose how values are calculated.

Available aggregation functions include:

```text
Not used / latest
Minimum
Maximum
Average
Count
Sum
First
Last
```

For event items that store `1` for each event, a common configuration is:

```text
Aggregation function: Sum
Time period: Dashboard
History data: History
Decimal places: 0
```

Changing the dashboard time selector then changes the period used for the displayed total.

For gauges or continuously sampled measurements, functions such as average, minimum, maximum, first, or last may be more appropriate.

### Time period

Each column can use:

- Dashboard time period
- Time period supplied by another compatible widget
- Custom time period

This allows different columns to use independent calculations and periods where required.

### History source

For numeric data, each column can use:

- Auto
- History
- Trends

Select the source according to the required precision, retention, and date range.

### Display options

Columns can be displayed as:

- Plain value
- Bar
- Indicators
- Sparkline
- Numeric value
- Text value

Additional options include:

- Base colour
- Thresholds
- Highlights
- Decimal places
- Minimum and maximum values
- Sparkline colour, width, fill, and period

## Generic example

The following example groups application metrics by the `service` item tag.

### Widget settings

```text
Group rows by item tag:
service

Include tag values matching:
^production-

Remove from displayed row name:
^production-
```

### Requests column

```text
Name: Requests
Item patterns: Application: Requests*
Aggregation function: Sum
Time period: Dashboard
History data: History
Decimal places: 0
```

Optional item-tag filter:

```text
metric Equals requests
```

### Errors column

```text
Name: Errors
Item patterns: Application: Errors*
Aggregation function: Sum
Time period: Dashboard
History data: History
Decimal places: 0
```

### Average latency column

```text
Name: Average latency
Item patterns: Application: Latency*
Aggregation function: Average
Time period: Dashboard
History data: Auto
Decimal places: 2
```

### Expected layout

```text
Group     Requests    Errors    Average latency
api          12450        32              18.42
web           8720         7              11.06
worker        4310        15              24.81
```

## Advanced widget settings

The widget-level Advanced configuration contains:

- Row order: ascending or descending
- Row limit
- Column-header display

Column-specific calculation and display options remain inside each column's editor.

## Behaviour and limitations

- The grouping tag must exist on each item that should appear in the table.
- Each configured column should ideally match one item per grouped row.
- If multiple items match the same column for one group, make the item pattern more specific or add item-tag filters.
- Missing item and group combinations appear as empty cells.
- Numeric aggregation requires numeric items.
- Text display and highlighting depend on the selected item value type and column configuration.
- Regular-expression fields must contain valid expressions accepted by PHP's regular-expression engine.

## Upgrading

1. Remove the existing module directory.
2. Extract the new release into the modules directory.
3. Run **Scan directory** in Zabbix.
4. Enable or refresh the module.
5. Restart PHP-FPM if required.

When a release changes widget fields or storage structure, remove and re-add the dashboard widget so the updated configuration initialises cleanly.

## Troubleshooting

### Widget remains on the loading spinner

Validate all PHP files:

```bash
find /usr/share/zabbix/ui/modules/groupedmetricstable \
  -name '*.php' \
  -exec php -l {} \;
```

Review PHP-FPM logs:

```bash
sudo journalctl -u php8.3-fpm -n 100 --no-pager
```

### No data found

Check that:

- The selected hosts contain matching items.
- The configured item patterns match the actual item names.
- Matched items contain the configured grouping tag.
- The include regular expression matches the grouping-tag values.
- The selected period contains item history.
- Item-tag filters match the exact tag names and values.
- The current Zabbix user has permission to view the selected hosts and items.

### Empty cells

An empty cell normally means that the group exists but no matching item or value was found for that column and period.

Check the column's:

- Item pattern
- Item-tag filters
- Value type
- Aggregation function
- Time period
- History source

### Column name is rejected as an unexpected parameter

Install version **2.2.1 or later**, which includes the custom column-name field in the column validation and persistence schema.

## Development

The module is derived from the Zabbix 7.4.15 **Top items** widget. The primary behavioural change is that table rows represent distinct item-tag values rather than hosts.

### Module structure

```text
actions/ColumnEdit.php
  Handles the column configuration popup and validation.

actions/WidgetView.php
  Retrieves items, calculates values, groups results by item-tag value, and builds table data.

includes/CWidgetFieldColumnsList.php
  Defines column configuration storage, validation, and API serialisation.

includes/CWidgetFieldColumnsListView.php
  Renders the configurable column list.

includes/WidgetForm.php
  Defines widget-level fields for data selection, row grouping, filtering, ordering, and limits.

views/column.edit.php
views/column.edit.js.php
  Render and control the column editor.

views/widget.edit.php
views/widget.edit.js.php
  Render and control the main widget configuration form.

views/widget.view.php
  Renders the grouped metrics table.
```

## Licence

This derivative module contains code based on Zabbix and retains the GNU Affero General Public License version 3 notices present in the source files.

See the source-file headers and repository licence for the complete licence terms.
