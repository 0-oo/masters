<?php
$db = $this->db();
$form = $this->form();
$html = new P3_Html();

$conditions = array(
	'' => 'と一致',
	'IN' => 'のいづれか（カンマ区切り）',
	'NOT IN' => 'でない（複数ならカンマ区切り）',
	'>' => 'より大きい',
	'>=' => '以上',
	'<' => 'より小さい',
	'<=' => '以下',
	'BETWEEN' => 'の間（カンマ区切り）',
	'LIKE %-%' => 'を含む',
	'NOT_LIKE %-%' => 'を含まない',
	'LIKE -%' => 'で始まる',
	'NOT_LIKE -%' => '以外で始まる',
	'LIKE %-' => 'で終わる',
	'NOT_LIKE %-' => '以外で終わる',
);

$where = Masters::getProp('where', array());
$whereIndices = array(-1);


// 検索条件

$params = $this->params();

foreach ($params as $k => $v) {
	preg_match('/^column_([0-9]+)\z/', $k, $matches);
	
	if (!$v || !$matches) {
		continue;
	}
	
	$column = $v;
	Masters::checkValidColumn($column);
	$whereIndex = $matches[1];
	$whereIndices[] = $whereIndex;
	$value = $params["value_$whereIndex"];
	$condition = $params["condition_$whereIndex"];
	
	switch ($condition) {
		case '':
		case '>':
		case '>=':
		case '<':
		case '<=':
			$where[rtrim("$column $condition")] = $value;
			break;
		case 'IN':
		case 'NOT IN':
			$values = explode(',', $value);
			$where["$column $condition (" . str_repeat('?,', count($values) - 1) . '?)'] = $values;
			break;
		case 'BETWEEN':
			$values = explode(',', $value);
			
			if (count($values) == 2) {
				$where["$column BETWEEN ? AND ?"] = $values;
			}
			break;
		case 'LIKE %-%':
		case 'NOT_LIKE %-%':
		case 'LIKE -%':
		case 'NOT_LIKE -%':
		case 'LIKE %-':
		case 'NOT_LIKE %-':
			list($condition, $exp) = explode(' ', $condition);
			$where["$column " . str_replace('_', ' ', $condition)] = str_replace('-', $value, $exp);
			break;
		default:
			throw new Exception("想定外のパラメータ $condition");
	}
}


// データ取得

$select = implode(', ', Masters::getColumns());

if (!$select) {
	$select = '*';
}

$table = Masters::getTable();

$rows = $db->select(	// 最大100件取得
	$select,
	$table,
	$where,
	'ORDER BY ' . implode(', ', Masters::getProp('order', array('id'))) . ' LIMIT 100'
);

$count = $db->count($table, $where);	// 全件数


// 列情報の整理

$filter = $this->filter();
$confColumns = Masters::getProp('columns');
$columns = array();
$header = '<tr class="header">';
$selectors = '';
$whereColumns = array('' => '選択してください');

for ($i = 0; $i < $rows->columnCount(); $i++) {
	$meta = $rows->getColumnMeta($i);
	$columnName = $meta['name'];
	$attr = array('data-column-name' => $columnName);
	$type = null;
	
	if ($meta['pdo_type'] == PDO::PARAM_INT) {
		$attr['class'] = 'num';
		$type = 'integer';
	}
	
	if (in_array('not_null', $meta['flags'])) {
		$attr['data-required'] = true;
	}
	
	if ($confColumns) {
		$column = arrayValue($columnName, $confColumns, $columnName);
		
		if (is_array($column)) {
			$label = $column['label'];
			$type = arrayValue('type', $column, $type);
			
			$foreign = arrayValue('foreign', $column);
			$hasValue = true;
			
			if ($foreign) {
				$foreignRows = $db->select(
					$foreign['value'] . ' AS value, ' . $foreign['label'] . ' AS label',
					$foreign['table'],
					$foreign['where'],
					'ORDER BY ' . implode(', ', $foreign['order'])
				);
				$options = array();
				
				foreach ($foreignRows as $foreignRow) {
					$options[$foreignRow['value']] = $foreignRow['label'];
				}
			} else {
				$options = arrayValue('select', $column);
				
				if ($options) {
					$hasValue = false;
				} else {
					$options = arrayValue('select_value', $column);
				}
			}
			
			if ($options) {
				$selectors .= $form->select($columnName, $options, $hasValue, array('id' => $columnName));
			}
		} else {
			$label = $column;
		}
	} else {
		$label = $columnName;
	}
	
	if ($type) {
		list($attr['data-pattern'], $attr['data-title']) = $filter->pattern($type);
		$attr['data-han'] = true;
	}
	
	$columns[$columnName] = array('pdo_type' => $meta['pdo_type']);
	$header .= $html->tag('th', $attr, $label);
	$whereColumns[$columnName] = $label;
}

$header .= "<th></th>\n";
$header .= "</tr>\n";


// 検索条件の表示

list($url) = explode('?', $_SERVER['REQUEST_URI']);
?>
<form action="<?php $url ?>">
<?php
foreach ($whereIndices as $whereIndex) {
	if ($whereIndex == -1) {
		$attr = array('id' => 'where');
		$columnName = '';
		$valueName = '';
		$conditionnName = '';
	} else {
		$attr = array();
		$columnName = "column_$whereIndex";
		$valueName = "value_$whereIndex";
		$conditionnName = "condition_$whereIndex";
	}
	
	echo $html->tag('div', $attr);
	?>
		<button type="button" class="where-minus"><i class="icon-minus"></i></button>
		<?php
		echo $form->select($columnName, $whereColumns) . ' が ';
		echo $form->text($valueName, array('placeholder' => '入力してください'));
		echo $form->select($conditionnName, $conditions);
		?>
	</div>
	<?php
}
?>
<input type="hidden" id="where-index" value="<?php echo $whereIndex + 1 ?>" />
<div>
<button type="button" id="where-plus"><i class="icon-plus"></i></button>
<button type="submit" id="where-search"><i class="icon-search"></i></button>
( <?php echo $rows->rowCount() . ' / ' . number_format($count) ?> )
</div>
</form>


<?php /* 一覧の表示 */ ?>

<table id="data-list" class="table table-striped table-bordered table-hover">

<thead><?php echo $header ?></thead>

<tfoot>
<tr>
<?php
$first = true;

foreach ($columns as $column) {
	if ($first) {
		echo '<th id="data-plus"><i class="icon-plus"></i></th>';
		$first = false;
	} else {
		$attr = array();
		
		if ($column['pdo_type'] == PDO::PARAM_INT) {
			$attr['class'] = 'num';
		}
		
		echo $html->tag('td', $attr, null);
	}
}
?>
<th class="data-buttons">
<button id="btn-add" title="保存" disabled="disabled"><i class="icon-ok"></i></button>
</th>
</tr>
<?php echo $header ?>
</tfoot>

<tbody>
<?php
foreach ($rows as $i => $row) {
	if ($i && $i % 15 == 0) {
		echo $header;
	}
	
	echo "<tr>\n";
	$key = true;
	
	foreach ($row as $k => $v) {
		$attr = array();
		
		if ($key) {
			$tag = 'th';
			$key = false;
		} else {
			$tag = 'td';
		}
		
		if ($columns[$k]['pdo_type'] == PDO::PARAM_INT) {
			$attr['class'] = 'num';
		}
		
		echo $html->tag($tag, $attr, $v);
	}
	?>
	<th class="data-buttons">
	<button class="btn-ok" title="保存" disabled="disabled"><i class="icon-ok"></i></button>
	<button class="btn-remove" title="削除"><i class="icon-remove"></i></button>
	<button class="btn-cancel" title="キャンセル" disabled="disabled"><i class="icon-repeat"></i></button>
	</th>
	</tr>
	<?php
}
?>
</tbody>

</table>

<div id="selectors"><?php echo $selectors ?></div>
